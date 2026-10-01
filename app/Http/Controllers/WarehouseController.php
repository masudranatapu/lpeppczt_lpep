<?php

namespace App\Http\Controllers;

use App\Exports\AreaManagerDailyReportExport;
use App\Models\Product;
use App\Models\Warehouse;
use App\Service\AreaManagerDailyReportService;
use App\Models\WarehouseSalesman;
use App\Models\WarehousePurchaseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WarehouseController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin', 403);
    }

    private function areaManagerDailyPayload(Request $request, AreaManagerDailyReportService $report): array
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);
        $warehouses = Warehouse::query()->orderBy('name')->get(['id', 'name']);
        $warehouseId = (int) ($data['warehouse_id'] ?? $warehouses->first()?->id);
        abort_unless($warehouseId, 404, 'No Area Office found.');

        return $report->build(Warehouse::findOrFail($warehouseId), $data['month'] ?? now()->format('Y-m'))
            + ['warehouses' => $warehouses];
    }

    public function areaManagerDaily(Request $request, AreaManagerDailyReportService $report)
    {
        return view('warehouse.area-manager-daily', $this->areaManagerDailyPayload($request, $report));
    }

    public function areaManagerDailyPdf(Request $request, AreaManagerDailyReportService $report)
    {
        $data = $this->areaManagerDailyPayload($request, $report);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('warehouse-portal.reports.area-manager-daily-pdf', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="area-manager-daily-report-' . $data['warehouse']->code . '-' . $data['month'] . '.pdf"',
        ]);
    }

    public function areaManagerDailyExcel(Request $request, AreaManagerDailyReportService $report)
    {
        $data = $this->areaManagerDailyPayload($request, $report);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new AreaManagerDailyReportExport($data),
            'area-manager-daily-report-' . $data['warehouse']->code . '-' . $data['month'] . '.xlsx'
        );
    }

    public function index()
    {
        $this->ensureAdmin();

        $warehouses = Warehouse::query()
            ->withCount(['salesmen', 'purchases', 'stockTransfers', 'sales'])
            ->latest()
            ->get();

        return view('warehouse.index', compact('warehouses'));
    }

    public function stockOverview(Request $request)
    {
        $this->ensureAdmin();
        return view('warehouse.stock', $this->buildStockOverviewPayload($request, true));
    }

    private function buildStockOverviewPayload(Request $request, bool $paginate): array
    {
        $filters = $request->validate([
            'product_name' => ['nullable', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'per_page' => ['nullable', 'string', 'in:25,50,100,all'],
        ]);

        $search = trim((string) ($filters['product_name'] ?? ''));
        $warehouseId = isset($filters['warehouse_id']) ? (int) $filters['warehouse_id'] : null;
        $salesmanId = isset($filters['salesman_id']) ? (int) $filters['salesman_id'] : null;
        $perPageInput = (string) ($filters['per_page'] ?? 'all');
        $perPage = in_array($perPageInput, ['25', '50', '100', 'all'], true) ? $perPageInput : 'all';

        $warehouseOptions = Warehouse::query()->orderBy('name')->get(['id', 'name', 'code']);

        $salesmanOptions = WarehouseSalesman::query()
            ->with('warehouse:id,name,code')
            ->where('status', 1)
            ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'warehouse_id']);

        if ($salesmanId && ! $salesmanOptions->contains('id', $salesmanId)) {
            $salesmanId = null;
        }

        $selectedSalesman = $salesmanId ? $salesmanOptions->firstWhere('id', $salesmanId) : null;

        if (! $warehouseId && $selectedSalesman?->warehouse_id) {
            $warehouseId = (int) $selectedSalesman->warehouse_id;
        }

        $latestPurchasePrice = function ($query) use ($warehouseId) {
            $query->select('purchase_price')
                ->from('lpep_warehouse_purchase_items as purchase_items')
                ->join('lpep_warehouse_purchases as purchases', 'purchases.id', '=', 'purchase_items.warehouse_purchase_id')
                ->whereColumn('purchase_items.product_id', 'products.id')
                ->when($warehouseId, fn ($subQuery) => $subQuery->where('purchases.warehouse_id', $warehouseId))
                ->orderByDesc('purchase_items.id')
                ->limit(1);
        };

        $globalLatestPurchasePrice = function ($query) {
            $query->select('purchase_price')
                ->from('lpep_warehouse_purchase_items as purchase_items')
                ->whereColumn('purchase_items.product_id', 'products.id')
                ->orderByDesc('purchase_items.id')
                ->limit(1);
        };

        $productsQuery = Product::query()
            ->select('products.*')
            ->addSelect([
                'latest_purchase_price' => $latestPurchasePrice,
                'global_latest_purchase_price' => $globalLatestPurchasePrice,
            ])
            ->active()
            ->when($search !== '', fn ($query) => $query->where('products.product_name', 'like', '%' . $search . '%'));

        $purchaseItems = DB::table('lpep_warehouse_purchase_items as purchase_items')
            ->join('lpep_warehouse_purchases as purchases', 'purchases.id', '=', 'purchase_items.warehouse_purchase_id')
            ->whereNotNull('purchases.warehouse_id')
            ->when($warehouseId, fn ($query) => $query->where('purchases.warehouse_id', $warehouseId))
            ->groupBy('purchase_items.product_id')
            ->selectRaw('purchase_items.product_id, SUM(purchase_items.quantity) as quantity');

        $transferItems = DB::table('lpep_warehouse_stock_transfer_items as transfer_items')
            ->join('lpep_warehouse_stock_transfers as transfers', 'transfers.id', '=', 'transfer_items.warehouse_stock_transfer_id')
            ->when($warehouseId, fn ($query) => $query->where('transfers.warehouse_id', $warehouseId))
            ->groupBy('transfer_items.product_id')
            ->selectRaw('transfer_items.product_id, SUM(transfer_items.quantity) as quantity');

        $directSalesItems = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->whereNull('sales.warehouse_salesman_id')
            ->when($warehouseId, fn ($query) => $query->where('sales.warehouse_id', $warehouseId))
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as quantity');

        $salesmanSalesItems = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->whereNotNull('sales.warehouse_salesman_id')
            ->when($warehouseId, fn ($query) => $query->where('sales.warehouse_id', $warehouseId))
            ->when($salesmanId, fn ($query) => $query->where('sales.warehouse_salesman_id', $salesmanId))
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as quantity');

        $assignmentItems = DB::table('lpep_warehouse_salesman_assignment_items as assignment_items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'assignment_items.warehouse_salesman_assignment_id')
            ->when($warehouseId, fn ($query) => $query->where('assignments.warehouse_id', $warehouseId))
            ->when($salesmanId, fn ($query) => $query->where('assignments.warehouse_salesman_id', $salesmanId))
            ->groupBy('assignment_items.product_id')
            ->selectRaw('assignment_items.product_id, SUM(assignment_items.quantity) as quantity');

        $returnItems = Schema::hasTable('lpep_warehouse_salesman_return_items')
            ? DB::table('lpep_warehouse_salesman_return_items as return_items')
                ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'return_items.warehouse_salesman_return_id')
                ->when($warehouseId, fn ($query) => $query->where('returns.warehouse_id', $warehouseId))
                ->when($salesmanId, fn ($query) => $query->where('returns.warehouse_salesman_id', $salesmanId))
                ->groupBy('return_items.product_id')
                ->selectRaw('return_items.product_id, SUM(return_items.quantity) as quantity')
            : DB::query()->selectRaw('NULL as product_id, 0 as quantity')->whereRaw('1 = 0');

        // Office stock must always use totals for every LSP, even when the report
        // is filtered to one LSP. The filtered queries above remain useful for
        // showing that selected LSP's movement and balance.
        $allSalesmanSalesItems = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->whereNotNull('sales.warehouse_salesman_id')
            ->when($warehouseId, fn ($query) => $query->where('sales.warehouse_id', $warehouseId))
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as quantity');

        $allAssignmentItems = DB::table('lpep_warehouse_salesman_assignment_items as assignment_items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'assignment_items.warehouse_salesman_assignment_id')
            ->when($warehouseId, fn ($query) => $query->where('assignments.warehouse_id', $warehouseId))
            ->groupBy('assignment_items.product_id')
            ->selectRaw('assignment_items.product_id, SUM(assignment_items.quantity) as quantity');

        $allReturnItems = Schema::hasTable('lpep_warehouse_salesman_return_items')
            ? DB::table('lpep_warehouse_salesman_return_items as return_items')
                ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'return_items.warehouse_salesman_return_id')
                ->when($warehouseId, fn ($query) => $query->where('returns.warehouse_id', $warehouseId))
                ->groupBy('return_items.product_id')
                ->selectRaw('return_items.product_id, SUM(return_items.quantity) as quantity')
            : DB::query()->selectRaw('NULL as product_id, 0 as quantity')->whereRaw('1 = 0');

        $productsQuery = $productsQuery
            ->leftJoinSub($purchaseItems, 'warehouse_purchases', fn ($join) => $join->on('warehouse_purchases.product_id', '=', 'products.id'))
            ->leftJoinSub($transferItems, 'warehouse_transfers', fn ($join) => $join->on('warehouse_transfers.product_id', '=', 'products.id'))
            ->leftJoinSub($directSalesItems, 'warehouse_direct_sales', fn ($join) => $join->on('warehouse_direct_sales.product_id', '=', 'products.id'))
            ->leftJoinSub($salesmanSalesItems, 'warehouse_sales', fn ($join) => $join->on('warehouse_sales.product_id', '=', 'products.id'))
            ->leftJoinSub($assignmentItems, 'warehouse_assignments', fn ($join) => $join->on('warehouse_assignments.product_id', '=', 'products.id'))
            ->leftJoinSub($returnItems, 'warehouse_returns', fn ($join) => $join->on('warehouse_returns.product_id', '=', 'products.id'))
            ->leftJoinSub($allSalesmanSalesItems, 'warehouse_all_sales', fn ($join) => $join->on('warehouse_all_sales.product_id', '=', 'products.id'))
            ->leftJoinSub($allAssignmentItems, 'warehouse_all_assignments', fn ($join) => $join->on('warehouse_all_assignments.product_id', '=', 'products.id'))
            ->leftJoinSub($allReturnItems, 'warehouse_all_returns', fn ($join) => $join->on('warehouse_all_returns.product_id', '=', 'products.id'))
            ->selectRaw('COALESCE(warehouse_purchases.quantity, 0) as warehouse_purchase_qty')
            ->selectRaw('COALESCE(warehouse_transfers.quantity, 0) as warehouse_transfer_qty')
            ->selectRaw('COALESCE(warehouse_direct_sales.quantity, 0) as warehouse_direct_sale_qty')
            ->selectRaw('COALESCE(warehouse_sales.quantity, 0) as warehouse_sale_qty')
            ->selectRaw('COALESCE(warehouse_assignments.quantity, 0) as warehouse_assigned_qty')
            ->selectRaw('COALESCE(warehouse_returns.quantity, 0) as warehouse_return_qty');
        $productsQuery = $productsQuery
            ->selectRaw('COALESCE(warehouse_all_sales.quantity, 0) as warehouse_all_sale_qty')
            ->selectRaw('COALESCE(warehouse_all_assignments.quantity, 0) as warehouse_all_assigned_qty')
            ->selectRaw('COALESCE(warehouse_all_returns.quantity, 0) as warehouse_all_return_qty');

        // Keep filtered reports limited to products with movement in the
        // selected scope; otherwise every active product is returned.
        if ($salesmanId) {
            $productsQuery->where(function ($query) {
                $query->whereRaw('COALESCE(warehouse_sales.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_assignments.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_returns.quantity, 0) > 0');
            });
        } elseif ($warehouseId) {
            $productsQuery->where(function ($query) {
                $query->whereRaw('COALESCE(warehouse_purchases.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_transfers.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_direct_sales.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_sales.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_assignments.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(warehouse_returns.quantity, 0) > 0');
            });
        }

        $calculateMetrics = function ($product) use ($salesmanId) {
            $purchasePrice = (float) ($product->latest_purchase_price ?? $product->global_latest_purchase_price ?? 0);
            $sellingPrice = (float) ($product->selling_price ?? 0);

            $product->purchase_price = $purchasePrice;
            $product->stock_purchase_price = 0;

            $receivedQty = (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0);
            $directSaleQty = (float) ($product->warehouse_direct_sale_qty ?? 0);
            $allAssignedQty = (float) ($product->warehouse_all_assigned_qty ?? 0);
            $allReturnedQty = (float) ($product->warehouse_all_return_qty ?? 0);
            $allLspSaleQty = (float) ($product->warehouse_all_sale_qty ?? 0);
            $reportedAssignedQty = (float) ($product->warehouse_assigned_qty ?? 0);
            $reportedReturnedQty = (float) ($product->warehouse_return_qty ?? 0);
            $reportedLspSaleQty = (float) ($product->warehouse_sale_qty ?? 0);

            $product->received_qty = round($receivedQty, 2);
            $product->assigned_qty = round($salesmanId
                ? max($reportedAssignedQty - $reportedReturnedQty, 0)
                : max($allAssignedQty - $allReturnedQty, 0), 2);
            $product->returned_qty = round($reportedReturnedQty, 2);
            $product->direct_sale_qty = round($directSaleQty, 2);
            $product->lsp_sale_qty = round($reportedLspSaleQty, 2);
            $product->office_stock_qty = round($receivedQty + $allReturnedQty - $allAssignedQty - $directSaleQty, 2);
            $product->lsp_stock_qty = round($reportedAssignedQty - $reportedReturnedQty - $reportedLspSaleQty, 2);
            $product->all_lsp_stock_qty = round($allAssignedQty - $allReturnedQty - $allLspSaleQty, 2);
            // For an LSP report, Total Remaining means that LSP's remaining
            // stock. Returns reduce held stock but are never sales.
            $product->total_remaining_qty = $salesmanId
                ? $product->lsp_stock_qty
                : round($product->office_stock_qty + $product->all_lsp_stock_qty, 2);

            if ($salesmanId) {
                $inQty = (float) ($product->warehouse_assigned_qty ?? 0);
                $outQty = (float) ($product->warehouse_sale_qty ?? 0) + (float) ($product->warehouse_return_qty ?? 0);
            } else {
                $inQty = (float) ($product->warehouse_transfer_qty ?? 0) + (float) ($product->warehouse_return_qty ?? 0);
                $outQty = (float) ($product->warehouse_direct_sale_qty ?? 0) + (float) ($product->warehouse_assigned_qty ?? 0);
            }

            $stockQty = $inQty - $outQty;

            $product->in_qty = round($inQty, 2);
            $product->out_qty = round($outQty, 2);
            $product->stock_qty = round($stockQty, 2);
            // Both monetary totals must always use the same quantity shown in
            // Total Remaining. For an LSP filter it is that LSP's stock; for
            // All/Area Office it is Area Office stock plus all LSP stock.
            $valuedStockQty = (float) $product->total_remaining_qty;
            $product->stock_sale_price = round($valuedStockQty * $sellingPrice, 2);
            $product->stock_purchase_price = round($valuedStockQty * $purchasePrice, 2);

            return $product;
        };

        $summaryProductsQuery = (clone $productsQuery)->with('unit')->orderBy('products.product_name');
        $summaryProducts = $summaryProductsQuery->get()->map($calculateMetrics);

        $products = $paginate
            ? (function () use ($productsQuery, $perPage) {
                $pageSize = $perPage === 'all' ? max(1, (clone $productsQuery)->count()) : (int) $perPage;
                return $productsQuery->with('unit')->orderBy('products.product_name')->paginate($pageSize)->withQueryString();
            })()
            : $summaryProducts->values();

        if ($paginate) {
            $products->getCollection()->transform($calculateMetrics);
        }

        $summary = (object) [
            'product_count' => $summaryProducts->count(),
            'in_qty' => (float) $summaryProducts->sum('in_qty'),
            'out_qty' => (float) $summaryProducts->sum('out_qty'),
            'stock_qty' => (float) $summaryProducts->sum('stock_qty'),
            'stock_sale_price' => (float) $summaryProducts->sum('stock_sale_price'),
            'stock_purchase_price' => (float) $summaryProducts->sum('stock_purchase_price'),
            'received_qty' => (float) $summaryProducts->sum('received_qty'),
            'assigned_qty' => (float) $summaryProducts->sum('assigned_qty'),
            'returned_qty' => (float) $summaryProducts->sum('returned_qty'),
            'direct_sale_qty' => (float) $summaryProducts->sum('direct_sale_qty'),
            'lsp_sale_qty' => (float) $summaryProducts->sum('lsp_sale_qty'),
            'office_stock_qty' => (float) $summaryProducts->sum('office_stock_qty'),
            'lsp_stock_qty' => (float) $summaryProducts->sum('lsp_stock_qty'),
            'total_remaining_qty' => (float) $summaryProducts->sum('total_remaining_qty'),
        ];

        return [
            'products' => $products,
            'warehouseOptions' => $warehouseOptions,
            'salesmanOptions' => $salesmanOptions,
            'selectedWarehouseId' => $warehouseId,
            'selectedSalesmanId' => $salesmanId,
            'search' => $search,
            'perPage' => $perPage,
            'summary' => $summary,
            // Shown at the top of the print / PDF / Excel so the file says whose stock it is.
            'warehouseLabel' => $this->stockWarehouseLabel($warehouseOptions->firstWhere('id', $warehouseId)),
            'salesmanLabel' => $selectedSalesman ? 'LSP: ' . $selectedSalesman->name : 'All LSPs',
        ];
    }

    private function stockWarehouseLabel(?Warehouse $warehouse): string
    {
        if (! $warehouse) {
            return 'All Area Offices';
        }

        return $warehouse->name . ($warehouse->code ? ' (' . $warehouse->code . ')' : '');
    }

    public function stockOverviewPrint(Request $request)
    {
        $this->ensureAdmin();

        return view('warehouse.stock-export', array_merge($this->buildStockOverviewPayload($request, false), [
            'reportType' => 'print',
        ]));
    }

    public function stockOverviewPdf(Request $request)
    {
        $this->ensureAdmin();

        $data = array_merge($this->buildStockOverviewPayload($request, false), [
            'reportType' => 'pdf',
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('warehouse.stock-export', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="warehouse-stock-report.pdf"',
        ]);
    }

    public function stockOverviewExcel(Request $request)
    {
        $this->ensureAdmin();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\WarehouseStockExport($this->buildStockOverviewPayload($request, false)),
            'warehouse-stock-report.xlsx'
        );
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('warehouse.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:lpep_warehouses,code'],
            'email' => ['required', 'email', 'max:255', 'unique:lpep_warehouses,email'],
            'password' => ['required', 'string', 'min:6'],
            'address' => ['nullable', 'string', 'max:255'],
            'daily_target' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        Warehouse::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'address' => $data['address'] ?? null,
            'daily_target' => $data['daily_target'] ?? 5000,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
            'status' => 1,
        ]);

        return redirect()->route('warehouses.index')->with('message', 'Area Office created successfully.');
    }

    public function show(Request $request, Warehouse $warehouse)
    {
        $this->ensureAdmin();

        $filter = $request->validate([
            'period' => ['nullable', 'in:today,week,month,year,month_year,custom'],
            'report_month' => ['required_if:period,month_year', 'nullable', 'integer', 'between:1,12'],
            'report_year' => ['required_if:period,month_year', 'nullable', 'integer', 'between:2000,' . (now()->year + 1)],
            'start_date' => ['required_if:period,custom', 'nullable', 'date'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = $filter['period'] ?? 'today';
        $reportMonth = (int) ($filter['report_month'] ?? now()->month);
        $reportYear = (int) ($filter['report_year'] ?? now()->year);

        if ($period === 'week') {
            $startDate = now()->startOfWeek()->toDateString();
            $endDate = now()->endOfWeek()->toDateString();
            $periodLabel = 'This Week';
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
            $periodLabel = now()->format('F Y');
        } elseif ($period === 'year') {
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->endOfYear()->toDateString();
            $periodLabel = 'This Year';
        } elseif ($period === 'month_year') {
            $selectedMonth = Carbon::create($reportYear, $reportMonth, 1);
            $startDate = $selectedMonth->copy()->startOfMonth()->toDateString();
            $endDate = $selectedMonth->copy()->endOfMonth()->toDateString();
            $periodLabel = $selectedMonth->format('F Y');
        } elseif ($period === 'custom') {
            $startDate = Carbon::parse($filter['start_date'])->toDateString();
            $endDate = Carbon::parse($filter['end_date'])->toDateString();
            $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } else {
            $startDate = today()->toDateString();
            $endDate = today()->toDateString();
            $periodLabel = 'Today';
        }

        $warehouse->load(['salesmen', 'purchases.items.product', 'stockTransfers.items.product', 'sales.items.product']);

        // Net value of each sale line: the invoice discount is shared out by line value,
        // so the lines of an invoice add up to what the customer was charged.
        $withNetAmounts = function ($sale) {
            $subtotal = (float) $sale->items->sum(fn ($item) => (float) $item->quantity * (float) $item->sale_price);
            $ratio = $subtotal > 0 ? (float) $sale->total_amount / $subtotal : 0;
            $sale->items->each(function ($item) use ($ratio) {
                $item->net_amount = (float) $item->quantity * (float) $item->sale_price * $ratio;
            });

            return $sale;
        };
        $warehouse->sales->each($withNetAmounts);

        $purchaseItems = $warehouse->purchases->flatMap->items
            ->concat($warehouse->stockTransfers->flatMap->items);
        $saleItems = $warehouse->sales->flatMap->items;

        $productIds = $purchaseItems->pluck('product_id')
            ->merge($saleItems->pluck('product_id'))
            ->unique()
            ->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $stock = $productIds->map(function ($productId) use ($products, $purchaseItems, $saleItems) {
            $productPurchases = $purchaseItems->where('product_id', $productId);
            $productSales = $saleItems->where('product_id', $productId);
            $latestPurchase = WarehousePurchaseItem::query()
                ->where('product_id', $productId)
                ->latest('id')
                ->first();

            $purchaseQty = (float) $productPurchases->sum('quantity');
            $soldQty = (float) $productSales->sum('quantity');
            $purchasePrice = (float) ($latestPurchase->purchase_price ?? 0);
            $salePrice = (float) ($latestPurchase->sale_price ?? 0);
            $purchaseAmount = $purchaseQty * $purchasePrice;
            $soldAmount = (float) $productSales->sum('net_amount');

            return (object) [
                'product' => $products->get($productId),
                'purchase_price' => $purchasePrice,
                'sale_price' => $salePrice,
                'purchase_qty' => $purchaseQty,
                'sold_qty' => $soldQty,
                'available_qty' => max(0, $purchaseQty - $soldQty),
                'purchase_amount' => $purchaseAmount,
                'sale_amount' => $soldAmount,
                'profit' => $soldAmount - ($purchasePrice * $soldQty),
            ];
        })->filter(function ($row) {
            return $row->product !== null;
        })->sort(function ($first, $second) {
            $firstOutOfStock = $first->available_qty <= 0;
            $secondOutOfStock = $second->available_qty <= 0;

            if ($firstOutOfStock !== $secondOutOfStock) {
                return $firstOutOfStock <=> $secondOutOfStock;
            }

            return strcasecmp($first->product->product_name, $second->product->product_name);
        })->values();

        $totals = (object) [
            'purchase_amount' => $stock->sum('purchase_amount'),
            'sale_amount' => $stock->sum('sale_amount'),
            'purchase_qty' => $stock->sum('purchase_qty'),
            'sold_qty' => $stock->sum('sold_qty'),
            'available_qty' => $stock->sum('available_qty'),
            'profit' => $stock->sum('profit'),
        ];

        $latestPurchasesByProduct = WarehousePurchaseItem::query()
            ->whereIn('product_id', $productIds)
            ->latest('id')
            ->get()
            ->unique('product_id')
            ->keyBy('product_id');

        $periodSales = $warehouse->sales()
            ->with('items.product')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get()
            ->each($withNetAmounts);
        $periodSaleItems = $periodSales->flatMap->items;

        $salesByProduct = $periodSaleItems->groupBy('product_id')->map(function ($items, $productId) use ($products, $latestPurchasesByProduct) {
            $quantity = (float) $items->sum('quantity');
            $salesAmount = (float) $items->sum('net_amount');
            $purchasePrice = (float) ($latestPurchasesByProduct->get($productId)->purchase_price ?? 0);

            return (object) [
                'product' => $products->get($productId) ?? $items->first()->product,
                'quantity' => $quantity,
                'sales_amount' => $salesAmount,
                'average_sale_price' => $quantity > 0 ? $salesAmount / $quantity : 0,
                'purchase_cost' => $purchasePrice * $quantity,
                'profit' => $salesAmount - ($purchasePrice * $quantity),
            ];
        })->sortByDesc('sales_amount')->values();

        $periodSummary = (object) [
            'sale_count' => $periodSales->count(),
            'quantity' => $salesByProduct->sum('quantity'),
            'sales_amount' => $salesByProduct->sum('sales_amount'),
            'purchase_cost' => $salesByProduct->sum('purchase_cost'),
            'profit' => $salesByProduct->sum('profit'),
            'product_count' => $salesByProduct->count(),
        ];

        $firstSaleDate = $warehouse->sales()->min('sale_date');
        $firstSaleYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : now()->year;
        $oldestReportYear = min($firstSaleYear, $reportYear, now()->year);
        $newestReportYear = max(now()->year, $reportYear);
        $reportYearOptions = range($newestReportYear, $oldestReportYear);

        return view('warehouse.show', compact(
            'warehouse',
            'stock',
            'totals',
            'period',
            'startDate',
            'endDate',
            'periodLabel',
            'reportMonth',
            'reportYear',
            'reportYearOptions',
            'periodSummary',
            'salesByProduct'
        ));
    }

    public function edit(Warehouse $warehouse)
    {
        $this->ensureAdmin();

        return view('warehouse.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:lpep_warehouses,code,' . $warehouse->id],
            'email' => ['required', 'email', 'max:255', 'unique:lpep_warehouses,email,' . $warehouse->id],
            'password' => ['nullable', 'string', 'min:6'],
            'address' => ['nullable', 'string', 'max:255'],
            'daily_target' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'integer'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $warehouse->update($data);

        return redirect()->route('warehouses.index')->with('message', 'Area Office updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $this->ensureAdmin();

        if ($warehouse->salesmen()->exists() || $warehouse->purchases()->exists() || $warehouse->stockTransfers()->exists() || $warehouse->salesmanAssignments()->exists() || $warehouse->sales()->exists()) {
            return back()->with('type', 'error')->with('message', 'Area Office has related records and cannot be deleted.');
        }

        $warehouse->delete();

        return back()->with('message', 'Area Office deleted successfully.');
    }
}
