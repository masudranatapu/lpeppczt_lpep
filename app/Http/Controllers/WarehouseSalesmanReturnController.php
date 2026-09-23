<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseSalesman;
use App\Models\WarehouseSalesmanReturn;
use App\Service\WarehouseInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseSalesmanReturnController extends Controller
{
    private function isAdmin(): bool
    {
        return Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin';
    }

    private function warehouseId(): int
    {
        abort_unless(Auth::guard('warehouse')->check(), 403);
        return (int) Auth::guard('warehouse')->id();
    }

    private function ensureReturnAccess(WarehouseSalesmanReturn $return): void
    {
        abort_unless($this->isAdmin() || (int) $return->warehouse_id === $this->warehouseId(), 403);
    }

    public function adminIndex(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        $filters = $request->validate([
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'per_page' => ['nullable', 'integer'],
        ]);

        $warehouseId = isset($filters['warehouse_id']) ? (int) $filters['warehouse_id'] : null;
        $salesmanId = isset($filters['salesman_id']) ? (int) $filters['salesman_id'] : null;
        $perPage = (int) ($filters['per_page'] ?? 20);
        $perPageOptions = [20, 50, 100];

        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 20;
        }

        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name', 'code']);
        $salesmen = WarehouseSalesman::query()
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->orderBy('name')
            ->get(['id', 'warehouse_id', 'name', 'email']);

        $returnQuery = WarehouseSalesmanReturn::query()
            ->with(['warehouse', 'salesman', 'items.product'])
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->when($salesmanId, fn ($query) => $query->where('warehouse_salesman_id', $salesmanId));

        $filteredTotalQuantity = (float) (clone $returnQuery)->sum('total_quantity');
        $returns = $returnQuery->latest()->paginate($perPage)->withQueryString();

        return view('warehouse-salesman-return.index', compact(
            'returns', 'warehouses', 'salesmen', 'warehouseId', 'salesmanId',
            'perPage', 'perPageOptions', 'filteredTotalQuantity'
        ));
    }

    public function adminShow(WarehouseSalesmanReturn $warehouse_salesman_return)
    {
        abort_unless($this->isAdmin(), 403);
        $warehouse_salesman_return->load(['warehouse', 'salesman', 'items.product.unit', 'createdBy']);

        return view('warehouse-salesman-return.show', ['return' => $warehouse_salesman_return]);
    }

    public function index(Request $request)
    {
        $warehouseId = $this->warehouseId();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,' . (now()->year + 1)],
            'per_page' => ['nullable', 'integer'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $salesmanId = isset($filters['salesman_id']) ? (int) $filters['salesman_id'] : null;
        $month = $request->has('month') ? (isset($filters['month']) ? (int) $filters['month'] : null) : now()->month;
        $year = $request->has('year') ? (isset($filters['year']) ? (int) $filters['year'] : null) : now()->year;
        $perPage = (int) ($filters['per_page'] ?? 10);
        $perPageOptions = [10, 25, 50, 100];

        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 10;
        }

        $firstDate = WarehouseSalesmanReturn::where('warehouse_id', $warehouseId)->min('return_date_time');
        $lastDate = WarehouseSalesmanReturn::where('warehouse_id', $warehouseId)->max('return_date_time');
        $oldestYear = $firstDate ? (int) date('Y', strtotime($firstDate)) : now()->year;
        $newestYear = $lastDate ? (int) date('Y', strtotime($lastDate)) : now()->year;
        $oldestYear = min($oldestYear, $year ?? now()->year);
        $newestYear = max($newestYear, $year ?? now()->year);
        $yearOptions = range($newestYear, $oldestYear);

        $salesmen = WarehouseSalesman::query()
            ->where('warehouse_id', $warehouseId)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $returnQuery = WarehouseSalesmanReturn::query()
            ->with(['salesman', 'items.product'])
            ->where('warehouse_id', $warehouseId)
            ->when($salesmanId, fn ($query) => $query->where('warehouse_salesman_id', $salesmanId))
            ->when($month, fn ($query) => $query->whereMonth('return_date_time', $month))
            ->when($year, fn ($query) => $query->whereYear('return_date_time', $year))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhere('notes', 'like', '%' . $search . '%')
                        ->orWhereDate('return_date_time', $search)
                        ->orWhereHas('salesman', fn ($salesmanQuery) => $salesmanQuery->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%'))
                        ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('product_name', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%'));
                });
            });

        $filteredTotalQuantity = (float) (clone $returnQuery)->sum('total_quantity');
        $returns = $returnQuery->latest()->paginate($perPage)->withQueryString();

        return view('warehouse-portal.returns.index', compact(
            'returns', 'salesmen', 'salesmanId', 'search', 'month', 'year',
            'yearOptions', 'perPage', 'perPageOptions', 'filteredTotalQuantity'
        ));
    }

    public function create(Request $request, WarehouseInventoryService $inventory)
    {
        $warehouseId = $this->warehouseId();
        $salesmen = WarehouseSalesman::query()
            ->where('warehouse_id', $warehouseId)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $selectedSalesmanId = (int) $request->query('salesman_id', old('warehouse_salesman_id', 0));
        $selectedSalesman = $selectedSalesmanId ? $salesmen->firstWhere('id', $selectedSalesmanId) : null;
        $products = collect();

        if ($selectedSalesman) {
            $latestTransfers = DB::table('lpep_warehouse_salesman_assignment_items as i')
                ->join('lpep_warehouse_salesman_assignments as a', 'a.id', '=', 'i.warehouse_salesman_assignment_id')
                ->where('a.warehouse_salesman_id', $selectedSalesman->id)
                ->groupBy('i.product_id')
                ->selectRaw('i.product_id, MAX(a.created_at) as last_transfer_at, MAX(a.assignment_date) as last_transfer_date')
                ->get()
                ->keyBy('product_id');

            $products = Product::query()
                ->with('unit')
                ->orderBy('product_name')
                ->get()
                ->map(function ($product) use ($inventory, $selectedSalesman, $latestTransfers) {
                    $product->available_quantity = $inventory->salesmanAvailable($selectedSalesman->id, $product->id);
                    $transfer = $latestTransfers->get($product->id);
                    $product->last_transfer_at = $transfer?->last_transfer_at ?: $transfer?->last_transfer_date;
                    return $product;
                })
                ->filter(fn ($product) => $product->available_quantity > 0)
                ->values();
        }

        return view('warehouse-portal.returns.create', compact('salesmen', 'selectedSalesman', 'selectedSalesmanId', 'products'));
    }

    public function store(Request $request, WarehouseInventoryService $inventory)
    {
        $warehouseId = $this->warehouseId();
        $data = $request->validate([
            'warehouse_salesman_id' => ['required', 'exists:lpep_warehouse_salesmen,id'],
            'return_date_time' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $salesman = WarehouseSalesman::findOrFail($data['warehouse_salesman_id']);
        if ((int) $salesman->warehouse_id !== $warehouseId) {
            throw ValidationException::withMessages(['warehouse_salesman_id' => 'The salesman does not belong to this warehouse.']);
        }

        $items = collect($data['items'])
            ->map(fn ($item) => [
                'product_id' => (int) $item['product_id'],
                'quantity' => round((float) ($item['quantity'] ?? 0), 2),
            ])
            ->filter(fn ($item) => $item['quantity'] > 0)
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Enter a return quantity for at least one product.']);
        }

        DB::transaction(function () use ($data, $warehouseId, $salesman, $items, $inventory) {
            $items = $items->sortBy('product_id')->values();
            foreach ($items as $index => $item) {
                $inventory->lockWarehouseProduct($warehouseId, $item['product_id']);
                $available = $inventory->salesmanAvailable($salesman->id, $item['product_id']);
                if ($available < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Only {$available} unit(s) are available for return from this salesman.",
                    ]);
                }
            }

            $return = WarehouseSalesmanReturn::create([
                'invoice_no' => WarehouseSalesmanReturn::nextInvoiceNo(),
                'warehouse_id' => $warehouseId,
                'warehouse_salesman_id' => $salesman->id,
                'return_date_time' => Carbon::parse($data['return_date_time']),
                'notes' => $data['notes'] ?? null,
                'total_quantity' => $items->sum('quantity'),
                'created_by' => Auth::guard('warehouse')->id(),
            ]);

            foreach ($items as $item) {
                $return->items()->create($item);
            }
        });

        return redirect()->route('warehouse.salesman-returns.index')->with('message', 'Salesman stock returned to warehouse successfully.');
    }

    public function show(WarehouseSalesmanReturn $warehouse_salesman_return)
    {
        $this->ensureReturnAccess($warehouse_salesman_return);
        $warehouse_salesman_return->load(['warehouse', 'salesman', 'items.product.unit', 'createdBy']);

        return view('warehouse-portal.returns.show', ['return' => $warehouse_salesman_return]);
    }
}
