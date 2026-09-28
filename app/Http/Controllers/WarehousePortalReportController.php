<?php

namespace App\Http\Controllers;

use App\Exports\AreaManagerDailyReportExport;
use App\Exports\WarehousePortalSalesReportExport;
use App\Service\AreaManagerDailyReportService;
use App\Models\WarehouseSale;
use App\Models\WarehouseSaleItem;
use App\Models\WarehouseSalePayment;
use App\Models\WarehouseSalesman;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class WarehousePortalReportController extends Controller
{
    private function currentWarehouse()
    {
        $warehouse = Auth::guard('warehouse')->user();

        abort_unless($warehouse, 403);

        return $warehouse;
    }

    private function resolveFilters(Request $request): array
    {
        $data = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'per_page' => ['nullable', 'string', 'in:all,10,20,50,100'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $today = Carbon::today();
        $perPage = (string) ($data['per_page'] ?? 'all');
        $year = (int) ($data['year'] ?? $today->year);
        $month = (int) ($data['month'] ?? $today->month);
        $startDate = Carbon::parse($data['start_date'] ?? $today->copy()->startOfMonth()->toDateString())->startOfDay();
        $endDate = Carbon::parse($data['end_date'] ?? $today->copy()->endOfMonth()->toDateString())->endOfDay();
        $label = $startDate->isSameDay($endDate) ? $startDate->format('d M Y') : $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

        return [
            'period' => 'custom',
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'label' => $label,
            'per_page' => $perPage,
            'year' => $year,
            'month' => $month,
        ];
    }

    private function buildReportData($warehouse, array $filters, bool $paginate): array
    {
        $salesFilter = function ($query) use ($warehouse, $filters) {
            $query->where('warehouse_id', $warehouse->id)
                ->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
        };

        $baseSales = WarehouseSale::query();
        $salesFilter($baseSales);

        $itemFilter = function ($query) use ($warehouse, $filters) {
            $query->whereHas('sale', function ($saleQuery) use ($warehouse, $filters) {
                $saleQuery->where('warehouse_id', $warehouse->id)
                    ->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
            });
        };

        $salesAggregates = DB::table('lpep_warehouse_sales as aggregate_sales')
            ->leftJoin('lpep_warehouse_sale_payments as aggregate_payments', function ($join) use ($filters) {
                $join->on('aggregate_payments.warehouse_sale_id', '=', 'aggregate_sales.id')
                    ->whereBetween('aggregate_payments.payment_date', [$filters['start_date'], $filters['end_date']]);
            })
            ->where('aggregate_sales.warehouse_id', $warehouse->id)
            ->whereNotNull('aggregate_sales.warehouse_salesman_id')
            ->groupBy('aggregate_sales.warehouse_salesman_id')
            ->selectRaw('aggregate_sales.warehouse_salesman_id, COUNT(DISTINCT CASE WHEN aggregate_sales.sale_date BETWEEN ? AND ? THEN aggregate_sales.id END) as sale_count, COALESCE(SUM(aggregate_payments.amount), 0) as total_sales_amount', [$filters['start_date'], $filters['end_date']]);

        $quantityAggregates = DB::table('lpep_warehouse_sale_items as report_items')
            ->join('lpep_warehouse_sales as report_sales', 'report_sales.id', '=', 'report_items.warehouse_sale_id')
            ->where('report_sales.warehouse_id', $warehouse->id)
            ->whereBetween('report_sales.sale_date', [$filters['start_date'], $filters['end_date']])
            ->whereNotNull('report_sales.warehouse_salesman_id')
            ->groupBy('report_sales.warehouse_salesman_id')
            ->selectRaw('report_sales.warehouse_salesman_id, SUM(report_items.quantity) as total_quantity');

        $salesmen = WarehouseSalesman::query()
            ->where('lpep_warehouse_salesmen.warehouse_id', $warehouse->id)
            ->leftJoinSub($salesAggregates, 'sales_report', function ($join) {
                $join->on('sales_report.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id');
            })
            ->leftJoinSub($quantityAggregates, 'quantity_report', function ($join) {
                $join->on('quantity_report.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id');
            })
            ->select('lpep_warehouse_salesmen.*')
            ->selectRaw('COALESCE(sales_report.sale_count, 0) as sale_count')
            ->selectRaw('COALESCE(quantity_report.total_quantity, 0) as total_quantity')
            ->selectRaw('COALESCE(sales_report.total_sales_amount, 0) as total_sales_amount')
            ->orderByDesc('total_sales_amount')
            ->orderBy('name')
            ->get();

        $directSalesQuery = clone $baseSales;
        $directSalesQuery->whereNull('warehouse_salesman_id');
        $directSaleIds = (clone $directSalesQuery)->select('id');
        $directSales = (object) [
            'sale_count' => (clone $directSalesQuery)->count(),
            'total_quantity' => (float) WarehouseSaleItem::query()
                ->whereIn('warehouse_sale_id', $directSaleIds)
                ->sum('quantity'),
            'total_sales_amount' => (float) WarehouseSalePayment::query()
                ->whereHas('sale', fn ($query) => $query->where('warehouse_id', $warehouse->id)->whereNull('warehouse_salesman_id'))
                ->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']])->sum('amount'),
        ];

        $summary = (object) [
            'sale_count' => (clone $baseSales)->count(),
            'total_amount' => (float) (clone $baseSales)->sum('total_amount'),
            'paid_amount' => (float) (clone $baseSales)->sum('paid_amount'),
            'due_amount' => (float) (clone $baseSales)->sum('due_amount'),
            'total_quantity' => (float) WarehouseSaleItem::query()->where($itemFilter)->sum('quantity'),
            'total_sales_amount' => (float) WarehouseSalePayment::query()
                ->whereHas('sale', fn ($query) => $query->where('warehouse_id', $warehouse->id))
                ->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']])->sum('amount'),
            'registered_salesmen' => $salesmen->count(),
            'active_salesmen' => $salesmen->where('sale_count', '>', 0)->count(),
        ];

        $transactionsQuery = (clone $baseSales)
            ->with(['salesman', 'items.product.unit'])
            ->withSum('items as total_quantity', 'quantity')
            ->orderByDesc('sale_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $sales = $paginate && $filters['per_page'] !== 'all'
            ? $transactionsQuery->paginate((int) $filters['per_page'])->withQueryString()
            : $transactionsQuery->get();

        return compact('warehouse', 'filters', 'salesmen', 'directSales', 'summary', 'sales');
    }

    private function ensureSalesmanBelongsToWarehouse(WarehouseSalesman $salesman, $warehouse): void
    {
        abort_unless((int) $salesman->warehouse_id === (int) $warehouse->id, 403);
    }

    private function buildSalesmanReportData($warehouse, WarehouseSalesman $salesman, array $filters, bool $paginate): array
    {
        $this->ensureSalesmanBelongsToWarehouse($salesman, $warehouse);

        $applySaleFilters = function ($query) use ($warehouse, $salesman, $filters) {
            $query->where('warehouse_id', $warehouse->id)
                ->where('warehouse_salesman_id', $salesman->id)
                ->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
        };

        $baseSalesQuery = WarehouseSale::query();
        $applySaleFilters($baseSalesQuery);

        $productSales = WarehouseSaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(total) as total_sales_amount')
            ->selectRaw('COUNT(DISTINCT warehouse_sale_id) as sale_count')
            ->whereHas('sale', $applySaleFilters)
            ->with('product.unit')
            ->groupBy('product_id')
            ->orderByDesc('total_sales_amount')
            ->get();

        $summary = (object) [
            'sale_count' => (clone $baseSalesQuery)->count(),
            'total_quantity' => (float) WarehouseSaleItem::query()
                ->whereHas('sale', $applySaleFilters)
                ->sum('quantity'),
            'total_sales_amount' => (float) WarehouseSalePayment::query()
                ->whereHas('sale', fn ($query) => $query->where('warehouse_id', $warehouse->id)->where('warehouse_salesman_id', $salesman->id))
                ->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']])->sum('amount'),
            'product_count' => $productSales->count(),
        ];

        $salesQuery = (clone $baseSalesQuery)
            ->with(['items.product.unit', 'items.product.warehousePurchaseItems.purchase'])
            ->withSum('items as total_quantity', 'quantity')
            ->orderByDesc('sale_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $sales = $paginate
            ? $salesQuery->paginate(20)->withQueryString()
            : $salesQuery->get();

        $sales->getCollection()->each(function ($sale) use ($warehouse) {
            $sale->profit_amount = $sale->items->sum(function ($item) use ($sale, $warehouse) {
                $purchasePrice = $item->product?->warehousePurchaseItems
                    ->filter(fn ($purchaseItem) => (int) $purchaseItem->purchase?->warehouse_id === (int) $warehouse->id
                        && $purchaseItem->purchase?->purchase_date <= $sale->sale_date)
                    ->sortByDesc(fn ($purchaseItem) => $purchaseItem->purchase?->purchase_date)
                    ->first()?->purchase_price ?? 0;

                return ((float) $item->sale_price - (float) $purchasePrice) * (float) $item->quantity;
            });
        });

        return compact('warehouse', 'salesman', 'filters', 'productSales', 'summary', 'sales');
    }

    private function salesmanCurrentStock($warehouse, WarehouseSalesman $salesman)
    {
        $this->ensureSalesmanBelongsToWarehouse($salesman, $warehouse);

        $assigned = DB::table('lpep_warehouse_salesman_assignment_items as items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'items.warehouse_salesman_assignment_id')
            ->where('assignments.warehouse_id', $warehouse->id)
            ->where('assignments.warehouse_salesman_id', $salesman->id)
            ->groupBy('items.product_id')
            ->selectRaw('items.product_id, SUM(items.quantity) as assigned_quantity');

        $sold = DB::table('lpep_warehouse_sale_items as items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->where('sales.warehouse_salesman_id', $salesman->id)
            ->groupBy('items.product_id')
            ->selectRaw('items.product_id, SUM(items.quantity) as sold_quantity');

        $returned = DB::table('lpep_warehouse_salesman_return_items as items')
            ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'items.warehouse_salesman_return_id')
            ->where('returns.warehouse_id', $warehouse->id)
            ->where('returns.warehouse_salesman_id', $salesman->id)
            ->groupBy('items.product_id')
            ->selectRaw('items.product_id, SUM(items.quantity) as returned_quantity');

        return \App\Models\Product::query()
            ->leftJoinSub($assigned, 'assigned_stock', function ($join) {
                $join->on('assigned_stock.product_id', '=', 'products.id');
            })
            ->leftJoinSub($sold, 'sold_stock', function ($join) {
                $join->on('sold_stock.product_id', '=', 'products.id');
            })
            ->leftJoinSub($returned, 'returned_stock', function ($join) {
                $join->on('returned_stock.product_id', '=', 'products.id');
            })
            ->whereRaw('COALESCE(assigned_stock.assigned_quantity, 0) > 0')
            ->select('products.*')
            ->selectRaw('COALESCE(assigned_stock.assigned_quantity, 0) as assigned_quantity')
            ->selectRaw('COALESCE(sold_stock.sold_quantity, 0) as sold_quantity')
            ->selectRaw('COALESCE(returned_stock.returned_quantity, 0) as returned_quantity')
            ->selectRaw('COALESCE(assigned_stock.assigned_quantity, 0) - COALESCE(sold_stock.sold_quantity, 0) - COALESCE(returned_stock.returned_quantity, 0) as current_quantity')
            ->with('unit')
            ->orderByRaw('COALESCE(assigned_stock.assigned_quantity, 0) - COALESCE(sold_stock.sold_quantity, 0) - COALESCE(returned_stock.returned_quantity, 0) DESC')
            ->orderBy('products.product_name')
            ->get();
    }

    private function reportPayload(Request $request, bool $paginate): array
    {
        $warehouse = $this->currentWarehouse();
        $filters = $this->resolveFilters($request);
        $data = $this->buildReportData($warehouse, $filters, $paginate);

        $firstSaleDate = WarehouseSale::query()
            ->where('warehouse_id', $warehouse->id)
            ->min('sale_date');
        $data['business'] = currentBranch();

        return $data;
    }

    public function index(Request $request)
    {
        return view('warehouse-portal.reports.index', $this->reportPayload($request, true));
    }

    public function print(Request $request)
    {
        $data = $this->reportPayload($request, false);
        $data['autoPrint'] = true;

        return view('warehouse-portal.reports.export', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->reportPayload($request, false);
        $data['autoPrint'] = false;

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('warehouse-portal.reports.export', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'warehouse-sales-report-' . $data['filters']['start_date'] . '-to-' . $data['filters']['end_date'] . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function excel(Request $request)
    {
        $data = $this->reportPayload($request, false);
        $filename = 'warehouse-sales-report-' . $data['filters']['start_date'] . '-to-' . $data['filters']['end_date'] . '.xlsx';

        return Excel::download(new WarehousePortalSalesReportExport($data), $filename);
    }

    private function areaManagerDailyPayload(Request $request, AreaManagerDailyReportService $report): array
    {
        $data = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);

        return $report->build($this->currentWarehouse(), $data['month'] ?? now()->format('Y-m'));
    }

    public function areaManagerDaily(Request $request, AreaManagerDailyReportService $report)
    {
        return view('warehouse-portal.reports.area-manager-daily', $this->areaManagerDailyPayload($request, $report));
    }

    public function areaManagerDailyPdf(Request $request, AreaManagerDailyReportService $report)
    {
        $data = $this->areaManagerDailyPayload($request, $report);

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('warehouse-portal.reports.area-manager-daily-pdf', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="area-manager-daily-report-' . $data['month'] . '.pdf"',
        ]);
    }

    public function areaManagerDailyExcel(Request $request, AreaManagerDailyReportService $report)
    {
        $data = $this->areaManagerDailyPayload($request, $report);

        return Excel::download(new AreaManagerDailyReportExport($data), 'area-manager-daily-report-' . $data['month'] . '.xlsx');
    }

    public function salesmen(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $filters = $this->resolveFilters($request);

        $salesAggregates = DB::table('lpep_warehouse_sales as aggregate_sales')
            ->leftJoin('lpep_warehouse_sale_payments as aggregate_payments', function ($join) use ($filters) {
                $join->on('aggregate_payments.warehouse_sale_id', '=', 'aggregate_sales.id')
                    ->whereBetween('aggregate_payments.payment_date', [$filters['start_date'], $filters['end_date']]);
            })
            ->where('aggregate_sales.warehouse_id', $warehouse->id)
            ->whereNotNull('aggregate_sales.warehouse_salesman_id')
            ->groupBy('aggregate_sales.warehouse_salesman_id')
            ->selectRaw('aggregate_sales.warehouse_salesman_id, COUNT(DISTINCT CASE WHEN aggregate_sales.sale_date BETWEEN ? AND ? THEN aggregate_sales.id END) as sale_count, COALESCE(SUM(aggregate_payments.amount), 0) as total_sales_amount', [$filters['start_date'], $filters['end_date']]);

        $quantityAggregates = DB::table('lpep_warehouse_sale_items as report_items')
            ->join('lpep_warehouse_sales as report_sales', 'report_sales.id', '=', 'report_items.warehouse_sale_id')
            ->where('report_sales.warehouse_id', $warehouse->id)
            ->whereBetween('report_sales.sale_date', [$filters['start_date'], $filters['end_date']])
            ->whereNotNull('report_sales.warehouse_salesman_id')
            ->groupBy('report_sales.warehouse_salesman_id')
            ->selectRaw('report_sales.warehouse_salesman_id, SUM(report_items.quantity) as total_quantity');

        $assignedStock = DB::table('lpep_warehouse_salesman_assignment_items as assignment_items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'assignment_items.warehouse_salesman_assignment_id')
            ->where('assignments.warehouse_id', $warehouse->id)
            ->groupBy('assignments.warehouse_salesman_id', 'assignment_items.product_id')
            ->selectRaw('assignments.warehouse_salesman_id, assignment_items.product_id, SUM(assignment_items.quantity) as assigned_quantity');

        $soldStock = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->whereNotNull('sales.warehouse_salesman_id')
            ->groupBy('sales.warehouse_salesman_id', 'sale_items.product_id')
            ->selectRaw('sales.warehouse_salesman_id, sale_items.product_id, SUM(sale_items.quantity) as sold_quantity');

        $returnedStock = DB::table('lpep_warehouse_salesman_return_items as return_items')
            ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'return_items.warehouse_salesman_return_id')
            ->where('returns.warehouse_id', $warehouse->id)
            ->groupBy('returns.warehouse_salesman_id', 'return_items.product_id')
            ->selectRaw('returns.warehouse_salesman_id, return_items.product_id, SUM(return_items.quantity) as returned_quantity');

        $stockAggregates = DB::query()
            ->fromSub($assignedStock, 'assigned_stock')
            ->leftJoinSub($soldStock, 'sold_stock', function ($join) {
                $join->on('sold_stock.warehouse_salesman_id', '=', 'assigned_stock.warehouse_salesman_id')
                    ->on('sold_stock.product_id', '=', 'assigned_stock.product_id');
            })
            ->leftJoinSub($returnedStock, 'returned_stock', function ($join) {
                $join->on('returned_stock.warehouse_salesman_id', '=', 'assigned_stock.warehouse_salesman_id')
                    ->on('returned_stock.product_id', '=', 'assigned_stock.product_id');
            })
            ->groupBy('assigned_stock.warehouse_salesman_id')
            ->selectRaw('assigned_stock.warehouse_salesman_id, SUM(assigned_stock.assigned_quantity - COALESCE(sold_stock.sold_quantity, 0) - COALESCE(returned_stock.returned_quantity, 0)) as current_stock_quantity');

        $salesmen = WarehouseSalesman::query()
            ->where('lpep_warehouse_salesmen.warehouse_id', $warehouse->id)
            ->leftJoinSub($salesAggregates, 'sales_report', function ($join) {
                $join->on('sales_report.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id');
            })
            ->leftJoinSub($quantityAggregates, 'quantity_report', function ($join) {
                $join->on('quantity_report.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id');
            })
            ->leftJoinSub($stockAggregates, 'current_stock', function ($join) {
                $join->on('current_stock.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id');
            })
            ->select('lpep_warehouse_salesmen.*')
            ->selectRaw('COALESCE(sales_report.sale_count, 0) as sale_count')
            ->selectRaw('COALESCE(quantity_report.total_quantity, 0) as total_quantity')
            ->selectRaw('COALESCE(sales_report.total_sales_amount, 0) as total_sales_amount')
            ->selectRaw('COALESCE(current_stock.current_stock_quantity, 0) as current_stock_quantity')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $firstSaleDate = WarehouseSale::query()
            ->where('warehouse_id', $warehouse->id)
            ->min('sale_date');
        $oldestYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : now()->year;
        $oldestYear = min($oldestYear, $filters['year']);
        $newestYear = max(now()->year, $filters['year']);
        $yearOptions = range($newestYear, $oldestYear);

        return view('warehouse-portal.salesmen.index', compact('warehouse', 'salesmen', 'filters', 'yearOptions'));
    }

    public function salesmanReport(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        $warehouse = $this->currentWarehouse();
        $filters = $this->resolveFilters($request);
        $data = $this->buildSalesmanReportData($warehouse, $warehouse_salesman, $filters, true);

        $firstSaleDate = WarehouseSale::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('warehouse_salesman_id', $warehouse_salesman->id)
            ->min('sale_date');
        $oldestYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : now()->year;
        $oldestYear = min($oldestYear, $filters['year']);
        $newestYear = max(now()->year, $filters['year']);
        $data['yearOptions'] = range($newestYear, $oldestYear);

        return view('warehouse-portal.salesmen.report', $data);
    }

    public function salesmanStock(WarehouseSalesman $warehouse_salesman)
    {
        $warehouse = $this->currentWarehouse();
        $this->ensureSalesmanBelongsToWarehouse($warehouse_salesman, $warehouse);
        $stock = $this->salesmanCurrentStock($warehouse, $warehouse_salesman);
        $summary = (object) [
            'product_count' => $stock->count(),
            'assigned_quantity' => (float) $stock->sum('assigned_quantity'),
            'sold_quantity' => (float) $stock->sum('sold_quantity'),
            'returned_quantity' => (float) $stock->sum('returned_quantity'),
            'current_quantity' => (float) $stock->sum('current_quantity'),
        ];

        return view('warehouse-portal.salesmen.stock', [
            'warehouse' => $warehouse,
            'salesman' => $warehouse_salesman,
            'stock' => $stock,
            'summary' => $summary,
        ]);
    }
}
