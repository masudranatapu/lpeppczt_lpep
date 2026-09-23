<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStockTransfer;
use App\Service\WarehouseInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseStockTransferController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin', 403);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();
        $filters = $request->validate([
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->endOfMonth()->toDateString();
        $search = trim((string) ($filters['search'] ?? ''));
        $transfersQuery = WarehouseStockTransfer::with(['warehouse', 'items.product'])
            ->when($filters['warehouse_id'] ?? null, fn ($query, $id) => $query->where('warehouse_id', $id))
            ->whereDate('transfer_date', '>=', $fromDate)->whereDate('transfer_date', '<=', $toDate)
            ->when($search !== '', fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('total_quantity', 'like', '%' . $search . '%')
                    ->orWhereHas('items.product', fn ($product) => $product->where('product_name', 'like', '%' . $search . '%'));
            }))->latest('transfer_date')->latest('id');
        $filteredTotalQuantity = (float) (clone $transfersQuery)->sum('total_quantity');
        $transfers = $transfersQuery->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get(['id', 'name']);
        return view('warehouse-stock-transfer.index', compact('transfers', 'warehouses', 'fromDate', 'toDate', 'search', 'filteredTotalQuantity'));
    }

    public function adminStockSummary(Request $request, WarehouseInventoryService $inventory)
    {
        $this->ensureAdmin();
        $productName = trim((string) $request->query('product_name', ''));
        $stockStatus = strtolower(trim((string) $request->query('stock_status', 'all'), " \t\n\r\0\x0B,"));
        if (! in_array($stockStatus, ['all', 'in', 'low', 'out'], true)) {
            $stockStatus = 'all';
        }

        $adminStock = Product::query()
            ->when($productName !== '', fn ($query) => $query->where('product_name', 'like', '%' . $productName . '%'))
            ->orderBy('product_name')->get()->map(function ($product) use ($inventory) {
            $product->purchased_quantity = $inventory->adminPurchased($product->id);
            $product->transferred_quantity = $inventory->adminTransferred($product->id);
            $product->available_quantity = $inventory->balance(
                $product->purchased_quantity,
                $product->transferred_quantity
            );

            return $product;
        })->filter(fn ($product) => $product->purchased_quantity > 0 || $product->transferred_quantity > 0)
            ->filter(function ($product) use ($stockStatus) {
                $available = (float) $product->available_quantity;
                return $stockStatus === 'all'
                    || ($stockStatus === 'in' && $available > 0)
                    || ($stockStatus === 'low' && $available > 0 && $available <= 10)
                    || ($stockStatus === 'out' && $available <= 0);
            })->values();

        $totals = (object) [
            'purchased' => $adminStock->sum('purchased_quantity'),
            'transferred' => $adminStock->sum('transferred_quantity'),
            'available' => $adminStock->sum('available_quantity'),
            'products' => $adminStock->count(),
        ];

        return view('warehouse-stock-transfer.admin-stock-summary', compact('adminStock', 'totals', 'productName', 'stockStatus'));
    }

    public function create(Request $request, WarehouseInventoryService $inventory)
    {
        $this->ensureAdmin();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $selectedWarehouseId = (int) $request->query('warehouse_id');
        $products = Product::orderBy('product_name')->get()->map(function ($product) use ($inventory) {
            $product->available_quantity = $inventory->adminAvailable($product->id);
            return $product;
        })->filter(fn ($product) => $product->available_quantity > 0)->values();
        return view('warehouse-stock-transfer.create', compact('warehouses', 'products', 'selectedWarehouseId'));
    }

    public function store(Request $request, WarehouseInventoryService $inventory)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:lpep_warehouses,id'],
            'transfer_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        DB::transaction(function () use ($data, $inventory) {
            $items = collect($data['items'])->sortBy('product_id')->values();
            foreach ($items as $index => $item) {
                $inventory->lockAdminProduct((int) $item['product_id']);
                $available = $inventory->adminAvailable((int) $item['product_id']);
                if ($available < (float) $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" => "Only {$available} unit(s) remain in admin stock.",
                    ]);
                }
            }
            $transfer = WarehouseStockTransfer::create([
                'invoice_no' => WarehouseStockTransfer::nextInvoiceNo(),
                'warehouse_id' => $data['warehouse_id'],
                'transfer_date' => $data['transfer_date'],
                'notes' => $data['notes'] ?? null,
                'total_quantity' => $items->sum('quantity'),
                'created_by' => Auth::id(),
            ]);
            foreach ($items as $item) {
                $transfer->items()->create($item);
            }
        });

        return redirect()->route('warehouse-stock-transfers.index')->with('message', 'Stock transferred to warehouse successfully.');
    }

    public function show(WarehouseStockTransfer $warehouse_stock_transfer)
    {
        $this->ensureAdmin();
        $warehouse_stock_transfer->load(['warehouse', 'items.product.unit', 'createdBy']);
        return view('warehouse-stock-transfer.show', ['transfer' => $warehouse_stock_transfer]);
    }

    public function destroy(WarehouseStockTransfer $warehouse_stock_transfer)
    {
        $this->ensureAdmin();
        $warehouse_stock_transfer->delete();
        return redirect()->route('warehouse-stock-transfers.index')->with('message', 'Transfer deleted successfully.');
    }
}
