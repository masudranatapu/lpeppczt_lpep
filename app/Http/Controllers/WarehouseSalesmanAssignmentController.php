<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseSalesman;
use App\Models\WarehouseSalesmanAssignment;
use App\Service\WarehouseInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseSalesmanAssignmentController extends Controller
{
    private function ensureAssignmentAccess(WarehouseSalesmanAssignment $assignment): void
    {
        abort_unless(
            $this->isAdmin()
            || (Auth::guard('warehouse')->check() && (int) Auth::guard('warehouse')->id() === (int) $assignment->warehouse_id),
            403
        );
    }

    private function isAdmin(): bool
    {
        return Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin';
    }

    private function warehouseId(Request $request): int
    {
        if ($this->isAdmin()) {
            return (int) $request->input('warehouse_id', $request->query('warehouse_id'));
        }
        abort_unless(Auth::guard('warehouse')->check(), 403);
        return (int) Auth::guard('warehouse')->id();
    }

    private function assignmentIndexPayload(Request $request, int $warehouseId): array
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'per_page' => ['nullable', 'string', 'in:all,10,25,50,100'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $fromDate = $filters['from_date'] ?? ($this->isAdmin() ? null : now()->startOfMonth()->toDateString());
        $toDate = $filters['to_date'] ?? ($this->isAdmin() ? null : now()->endOfMonth()->toDateString());
        $salesmanId = isset($filters['salesman_id']) ? (int) $filters['salesman_id'] : null;
        $perPage = (string) ($filters['per_page'] ?? 'all');
        if (! in_array($perPage, ['all', '10', '25', '50', '100'], true)) {
            $perPage = 'all';
        }

        $yearOptions = collect();
        $yearStart = WarehouseSalesmanAssignment::when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))->min('assignment_date');
        $yearEnd = WarehouseSalesmanAssignment::when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))->max('assignment_date');
        if ($yearStart || $yearEnd) {
            $startYear = $yearStart ? (int) Carbon::parse($yearStart)->year : now()->year;
            $endYear = $yearEnd ? (int) Carbon::parse($yearEnd)->year : now()->year;
            $yearOptions = collect(range(max($endYear, now()->year), min($startYear, now()->year)));
        }

        $salesmen = WarehouseSalesman::query()
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $assignmentQuery = WarehouseSalesmanAssignment::query()
            ->with(['warehouse', 'salesman', 'items.product'])
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->when($salesmanId, fn ($query) => $query->where('warehouse_salesman_id', $salesmanId))
            ->when($fromDate, fn ($query) => $query->whereDate('assignment_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('assignment_date', '<=', $toDate))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhere('notes', 'like', '%' . $search . '%')
                        ->orWhereDate('assignment_date', $search)
                        ->orWhereHas('salesman', fn ($salesmanQuery) => $salesmanQuery->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%'))
                        ->orWhereHas('items.product', function ($productQuery) use ($search) {
                            $productQuery->where('product_name', 'like', '%' . $search . '%');

                            // The Area Office product table has no `code` column.
                            // Preserve the admin query behavior without running this
                            // invalid condition for Area Office searches.
                            if ($this->isAdmin() && !request()->routeIs('warehouse-salesman-assignments.transfer-list')) {
                                $productQuery->orWhere('code', 'like', '%' . $search . '%');
                            }
                        });
                });
            });

        $filteredTotalQuantity = (float) (clone $assignmentQuery)->sum('total_quantity');
        $assignments = $perPage === 'all'
            ? $assignmentQuery->latest()->get()
            : $assignmentQuery->latest()->paginate((int) $perPage)->withQueryString();

        return compact('assignments', 'salesmen', 'search', 'fromDate', 'toDate', 'salesmanId', 'perPage', 'filteredTotalQuantity', 'yearOptions');
    }

    public function index(Request $request)
    {
        abort_unless($this->isAdmin() || Auth::guard('warehouse')->check(), 403);
        $warehouseId = $this->isAdmin() ? (int) $request->query('warehouse_id') : (int) Auth::guard('warehouse')->id();

        if ($this->isAdmin()) {
            $filters = $this->assignmentIndexPayload($request, $warehouseId);
            $warehouses = Warehouse::orderBy('name')->get();

            return view('warehouse-salesman-assignment.index', array_merge($filters, compact('warehouses', 'warehouseId')));
        }

        $filters = $this->assignmentIndexPayload($request, $warehouseId);

        return view('warehouse-portal.assignments.index', array_merge($filters, compact('warehouseId')));
    }

    public function transferList(Request $request)
    {
        abort_unless($this->isAdmin(), 403);
        $request->merge(['per_page' => $request->input('per_page', '25')]);
        $warehouseId = (int) $request->query('warehouse_id', 0);
        $filters = $this->assignmentIndexPayload($request, $warehouseId);
        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);

        return view('warehouse-salesman-assignment.transfer-list', array_merge($filters, compact('warehouses', 'warehouseId')));
    }

    public function create(Request $request, WarehouseInventoryService $inventory)
    {
        abort_unless($this->isAdmin() || Auth::guard('warehouse')->check(), 403);
        $warehouseId = $this->warehouseId($request);
        if (!$warehouseId) {
            return redirect()->route('warehouse-salesman-assignments.index')->withErrors(['warehouse_id' => 'Select a warehouse first.']);
        }
        $warehouse = Warehouse::findOrFail($warehouseId);
        $salesmen = $warehouse->salesmen()->where('status', 1)->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get()->map(function ($product) use ($inventory, $warehouseId) {
            $product->available_quantity = $inventory->warehouseUnassignedAvailable($warehouseId, $product->id);
            return $product;
        })->filter(fn ($product) => $product->available_quantity > 0)->values();
        $view = $this->isAdmin() ? 'warehouse-salesman-assignment.create' : 'warehouse-portal.assignments.create';
        return view($view, compact('warehouse', 'salesmen', 'products'));
    }

    public function store(Request $request, WarehouseInventoryService $inventory)
    {
        abort_unless($this->isAdmin() || Auth::guard('warehouse')->check(), 403);
        $warehouseId = $this->warehouseId($request);
        $data = $request->validate([
            'warehouse_id' => ['nullable', 'exists:lpep_warehouses,id'],
            'warehouse_salesman_id' => ['required', 'exists:lpep_warehouse_salesmen,id'],
            'assignment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);
        $salesman = WarehouseSalesman::findOrFail($data['warehouse_salesman_id']);
        if ((int) $salesman->warehouse_id !== $warehouseId) {
            throw ValidationException::withMessages(['warehouse_salesman_id' => 'The salesman does not belong to this warehouse.']);
        }

        DB::transaction(function () use ($data, $warehouseId, $inventory) {
            $items = collect($data['items'])->sortBy('product_id')->values();
            foreach ($items as $index => $item) {
                $inventory->lockWarehouseProduct($warehouseId, (int) $item['product_id']);
                $available = $inventory->warehouseUnassignedAvailable($warehouseId, (int) $item['product_id']);
                if ($available < (float) $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Only {$available} unassigned unit(s) remain at this warehouse.",
                    ]);
                }
            }
            $assignment = WarehouseSalesmanAssignment::create([
                'invoice_no' => WarehouseSalesmanAssignment::nextInvoiceNo(),
                'warehouse_id' => $warehouseId,
                'warehouse_salesman_id' => $data['warehouse_salesman_id'],
                'assignment_date' => $data['assignment_date'],
                'notes' => $data['notes'] ?? null,
                'total_quantity' => $items->sum('quantity'),
                'created_by' => $this->isAdmin() ? Auth::id() : Auth::guard('warehouse')->id(),
            ]);
            foreach ($items as $item) {
                $assignment->items()->create($item);
            }
        });

        $route = $this->isAdmin() ? 'warehouse-salesman-assignments.index' : 'warehouse.salesman-assignments.index';
        return redirect()->route($route, $this->isAdmin() ? ['warehouse_id' => $warehouseId] : [])->with('message', 'Stock assigned to salesman successfully.');
    }

    public function show(WarehouseSalesmanAssignment $warehouse_salesman_assignment)
    {
        $this->ensureAssignmentAccess($warehouse_salesman_assignment);
        $warehouse_salesman_assignment->load(['warehouse', 'salesman', 'items.product.unit', 'createdBy']);
        $view = $this->isAdmin() ? 'warehouse-salesman-assignment.show' : 'warehouse-portal.assignments.show';
        return view($view, ['assignment' => $warehouse_salesman_assignment]);
    }

    public function edit(WarehouseSalesmanAssignment $warehouse_salesman_assignment, Request $request, WarehouseInventoryService $inventory)
    {
        $this->ensureAssignmentAccess($warehouse_salesman_assignment);
        abort_unless($this->isAdmin() || Auth::guard('warehouse')->check(), 403);

        $warehouse = Warehouse::findOrFail($warehouse_salesman_assignment->warehouse_id);
        $salesmen = $warehouse->salesmen()->where('status', 1)->orderBy('name')->get();

        $assignmentItems = $warehouse_salesman_assignment->items->keyBy('product_id');
        $products = Product::orderBy('product_name')->get()->map(function ($product) use ($inventory, $warehouse, $assignmentItems) {
            $currentAssigned = (float) ($assignmentItems->get($product->id)->quantity ?? 0);
            $product->available_quantity = $inventory->warehouseUnassignedAvailable((int) $warehouse->id, (int) $product->id) + $currentAssigned;
            $product->current_quantity = $currentAssigned;
            return $product;
        })->filter(fn ($product) => $product->available_quantity > 0 || $product->current_quantity > 0)->values();

        $view = $this->isAdmin() ? 'warehouse-salesman-assignment.edit' : 'warehouse-portal.assignments.edit';

        return view($view, [
            'warehouse' => $warehouse,
            'salesmen' => $salesmen,
            'products' => $products,
            'assignment' => $warehouse_salesman_assignment->load('items.product'),
        ]);
    }

    public function update(Request $request, WarehouseSalesmanAssignment $warehouse_salesman_assignment, WarehouseInventoryService $inventory)
    {
        $this->ensureAssignmentAccess($warehouse_salesman_assignment);
        abort_unless($this->isAdmin() || Auth::guard('warehouse')->check(), 403);

        $warehouseId = (int) $warehouse_salesman_assignment->warehouse_id;
        $data = $request->validate([
            'warehouse_salesman_id' => ['required', 'exists:lpep_warehouse_salesmen,id'],
            'assignment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        $salesman = WarehouseSalesman::findOrFail($data['warehouse_salesman_id']);
        if ((int) $salesman->warehouse_id !== $warehouseId) {
            throw ValidationException::withMessages(['warehouse_salesman_id' => 'The salesman does not belong to this warehouse.']);
        }

        $currentQuantities = $warehouse_salesman_assignment->items()->get()->groupBy('product_id')->map->sum('quantity');

        DB::transaction(function () use ($data, $warehouse_salesman_assignment, $warehouseId, $inventory, $currentQuantities) {
            $items = collect($data['items'])->sortBy('product_id')->values();
            foreach ($items as $index => $item) {
                $productId = (int) $item['product_id'];
                $inventory->lockWarehouseProduct($warehouseId, $productId);
                $available = $inventory->warehouseUnassignedAvailable($warehouseId, $productId) + (float) ($currentQuantities[$productId] ?? 0);
                if ($available < (float) $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Only {$available} unit(s) remain available for this assignment.",
                    ]);
                }
            }

            $warehouse_salesman_assignment->update([
                'warehouse_salesman_id' => $data['warehouse_salesman_id'],
                'assignment_date' => $data['assignment_date'],
                'notes' => $data['notes'] ?? null,
                'total_quantity' => $items->sum('quantity'),
            ]);

            $warehouse_salesman_assignment->items()->delete();
            foreach ($items as $item) {
                $warehouse_salesman_assignment->items()->create($item);
            }
        });

        $route = $this->isAdmin() ? 'warehouse-salesman-assignments.index' : 'warehouse.salesman-assignments.index';
        return redirect()->route($route, $this->isAdmin() ? ['warehouse_id' => $warehouseId] : [])->with('message', 'Assignment updated successfully.');
    }

    public function destroy(WarehouseSalesmanAssignment $warehouse_salesman_assignment)
    {
        $this->ensureAssignmentAccess($warehouse_salesman_assignment);

        DB::transaction(function () use ($warehouse_salesman_assignment) {
            $warehouse_salesman_assignment->items()->delete();
            $warehouse_salesman_assignment->delete();
        });

        $route = $this->isAdmin() ? 'warehouse-salesman-assignments.index' : 'warehouse.salesman-assignments.index';
        return redirect()->route($route, $this->isAdmin() ? ['warehouse_id' => $warehouse_salesman_assignment->warehouse_id] : [])->with('message', 'Assignment deleted successfully.');
    }

    public function print(WarehouseSalesmanAssignment $warehouse_salesman_assignment)
    {
        $this->ensureAssignmentAccess($warehouse_salesman_assignment);
        $warehouse_salesman_assignment->load(['warehouse', 'salesman', 'items.product.unit', 'createdBy']);

        return view('warehouse-portal.assignments.print', [
            'assignment' => $warehouse_salesman_assignment,
            'business' => currentBranch(),
        ]);
    }
}
