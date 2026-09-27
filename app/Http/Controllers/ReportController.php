<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\DataTables\SaleReportDataTable;
use App\DataTables\SoldReportDataTable;
use App\DataTables\ProductReportDataTable;
use App\DataTables\CategoryReportDataTable;
use App\DataTables\CustomerReportDataTable;
use App\DataTables\PurchaseReportDataTable;
use App\DataTables\SupplierReportDataTable;
use App\DataTables\DailySaleReportDataTable;
use App\DataTables\MonthlySaleReportDataTable;
use App\Exports\StockReportExport;
use App\Models\AgentSale;
use App\Models\Product;
use App\Models\User;
use App\Models\WarehouseSale;
use App\Models\WarehouseSalesman;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function saleReport(SaleReportDataTable $dataTable)
    {
        return $dataTable->render('report.saleReport');
    }

    public function dailySaleReport(DailySaleReportDataTable $dataTable)
    {
        return $dataTable->render('report.dailySaleReport');
    }

    public function monthlySaleReport(MonthlySaleReportDataTable $dataTable)
    {
        return $dataTable->render('report.monthlySaleReport');
    }

    public function stockReport(Request $request)
    {
        $query = Product::query()
            ->when($request->product_name, function ($q) use ($request) {
                $q->where('products.product_name', 'like', '%' . $request->product_name . '%');
            })
            ->addSelect([
                'purchase_price' => function ($q) {
                    $q->select('purchase_price')
                        ->from('purchase_products')
                        ->whereColumn('purchase_products.product_id', 'products.id')
                        ->orderByDesc('purchase_products.id')
                        ->limit(1);
                }
            ])
            ->withStockProperties();

        if ($request->from && $request->to) {
            $query->whereBetween('products.created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        } elseif ($request->from) {
            $query->whereDate('products.created_at', '>=', $request->from);
        } elseif ($request->to) {
            $query->whereDate('products.created_at', '<=', $request->to);
        }
        $products = $query->get();

        return view('report.stock-report', [
            'products' => $products,
            'search' => $request->product_name ?? null,
        ]);
    }


    public function stockReportExport()
    {
        return Excel::download(new StockReportExport, 'stock-report.xlsx');
    }


    public function productReport(ProductReportDataTable $dataTable)
    {
        return $dataTable->render('report.productReport');
    }

    public function categoryReport(CategoryReportDataTable $dataTable)
    {
        return $dataTable->render('report.categoryReport');
    }

    public function purchaseReport(PurchaseReportDataTable $dataTable)
    {
        return $dataTable->render('report.purchaseReport');
    }

    public function customerReport(CustomerReportDataTable $dataTable)
    {
        return $dataTable->render('report.customerReport');
    }

    public function supplierReport(SupplierReportDataTable $dataTable)
    {
        return $dataTable->render('report.supplierReport');
    }

    public function profitLoss(Request $request)
    {
        $purchase = Purchase::query()
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("purchase_date", [$request->start, $request->end]);
            })
            ->sum('total');

        $sales = Sale::query()
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("sale_date", [$request->start, $request->end]);
            })
            ->sum('total_amount');

        $return = SaleReturn::query()
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("return_date", [$request->start, $request->end]);
            })
            ->sum('total_amount');


        $receive_payment = Contact::join('contact_payments', "contact_payments.contact_id", "=", "contacts.id")
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("contact_payments.paying_date", [$request->start, $request->end]);
            })
            ->where('contacts.type', "=", 'customer')
            ->sum('paying_amount');

        $send_payment = Contact::join('contact_payments', "contact_payments.contact_id", "=", "contacts.id")
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("contact_payments.paying_date", [$request->start, $request->end]);
            })
            ->where('contacts.type', "=", 'supplier')
            ->sum('paying_amount');

        $expense = Expense::query()
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("expanse_date", [$request->start, $request->end]);
            })
            ->sum('amount');

        $salary = Employee::join('salaries', "salaries.employee_id", "=", "employees.id")
            ->when($request->start && $request->end, function ($query) use ($request) {
                $query->whereBetween("salaries.salary_date", [$request->start, $request->end]);
            })
            ->sum('amount');

        return view('report.profitLossReport', [
            'purchase' => $purchase,
            'sale' => $sales,
            'return' => $return,
            'receive_payment' => $receive_payment,
            'send_payment' => $send_payment,
            'expense' => $expense,
            'salary' => $salary,
        ]);
    }

    public function soldProduct(SoldReportDataTable $dataTable)
    {
        return $dataTable->render('report.soldProductReport');
    }

    public function dueSaleReport()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');
        $user = request('user');
        $product = request('product');

        $query = AgentSale::query()
            ->whereRaw('total_amount > paying_amount')
            ->when($user, function ($query) use ($user) {
                return $query->where('agent_id', $user);
            })
            ->when($product, function ($query) use ($product) {
                return $query->whereHas('saleProducts', function ($q) use ($product) {
                    $q->where('product_id', $product);
                });
            })
            ->filterByDate($startDate, $endDate)
            ->with([
                'agent:id,name,employee_name',
                'customer:id,agent_id,name,mobile',
                'saleProducts:id,product_id,agent_sale_id,stock_transfer_detail_id,qty,price,total_price',
                'saleProducts.product:id,product_name',
                'saleProducts.stockTransferDetail:id,purchase_product_id',
                'saleProducts.stockTransferDetail.purchaseProduct:id,purchase_price'
            ]);

        $results = $query->orderBy('id', 'DESC')->paginate(100);

        $results->map(function ($sale) {
            $purchaseAmount = 0;
            $sale->saleProducts->map(function ($saleProduct) use (&$purchaseAmount) {
                if ($saleProduct->stockTransferDetail && $saleProduct->stockTransferDetail->purchaseProduct) {
                    $purchasePrice = $saleProduct->stockTransferDetail->purchaseProduct->purchase_price;
                    $purchaseAmount += $purchasePrice * $saleProduct->qty;
                }
            });
            $sale->purchase_amount = $purchaseAmount;
            $sale->profit_amount = $sale->total_amount - $purchaseAmount;
            return $sale;
        });

        $total_profit = $results->sum('profit_amount');

        return view('report.agent_sales_due_report', [
            'agents' => User::active()->agent()->get(['id', 'name', 'employee_name']),
            'sales' => $results,
            'products' => Product::where('status', 1)->orderBy('product_name')->get(),
            'total_profit' => $total_profit
        ]);
    }

    public function lspDueReport(Request $request)
    {
        $filterType = 'custom';
        $request->merge([
            'filter_type' => $filterType,
            'from_date' => $request->input('from_date', now()->startOfMonth()->toDateString()),
            'to_date' => $request->input('to_date', now()->endOfMonth()->toDateString()),
            'per_page' => $request->input('per_page', 'all'),
        ]);
        $filters = $request->validate([
            'filter_type' => ['required', 'in:all,month_year,year,custom'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'year' => ['required_if:filter_type,month_year,year', 'nullable', 'integer', 'between:2000,2100'],
            'month' => ['required_if:filter_type,month_year', 'nullable', 'integer', 'between:1,12'],
            'from_date' => ['required_if:filter_type,custom', 'nullable', 'date'],
            'to_date' => ['required_if:filter_type,custom', 'nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'in:all,10,25,50,100'],
        ]);

        $perPage = $filters['per_page'] ?? 'all';

        $salesQuery = WarehouseSale::query()
            ->whereNotNull('warehouse_salesman_id')
            ->where('due_amount', '>', 0)
            ->when($filters['salesman_id'] ?? null, fn ($query, $id) => $query->where('warehouse_salesman_id', $id))
            ->when($filters['product_id'] ?? null, fn ($query, $id) => $query->whereHas('items', fn ($items) => $items->where('product_id', $id)))
            ->whereBetween('sale_date', [$filters['from_date'], $filters['to_date']])
            ->with(['salesman', 'items.product'])
            ->latest('sale_date');

        $pageSize = $perPage === 'all' ? max($salesQuery->count(), 1) : (int) $perPage;

        $sales = $salesQuery->paginate($pageSize)->withQueryString();

        $salesmen = WarehouseSalesman::query()->orderBy('name')->get(['id', 'name', 'email']);
        $products = Product::query()->where('status', 1)->orderBy('product_name')->get(['id', 'product_name']);
        $filterLabel = $filters['from_date'] . ' to ' . $filters['to_date'];

        return view('report.lsp_due_report', compact('sales', 'salesmen', 'products', 'filterType', 'filterLabel', 'filters', 'perPage'));
    }

    private function lspSaleReportData(Request $request): array
    {
        $lsp = $request->input('lsp'); $product = $request->input('product');
        $startDate = $request->input('start_date') ?: now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ?: now()->endOfMonth()->toDateString();
        $perPage = $request->input('per_page', 'all');
        abort_unless(in_array((string) $perPage, ['all', '10', '20', '50', '100'], true), 422, 'Invalid per-page value.');
        $salesQuery = WarehouseSale::with(['salesman.warehouse', 'warehouse', 'items.product.warehousePurchaseItems.purchase'])
            ->when($lsp, fn ($query) => $query->where('warehouse_salesman_id', $lsp))
            ->when($product, fn ($query) => $query->whereHas('items', fn ($items) => $items->where('product_id', $product)))
            ->whereDate('sale_date', '>=', $startDate)->whereDate('sale_date', '<=', $endDate)->latest('sale_date');
        $allSales = (clone $salesQuery)->get();
        $sales = $perPage === 'all' ? $allSales : $salesQuery->paginate((int) $perPage)->withQueryString();
        // Each row reports only the selected product's lines (all lines when no product is chosen).
        // The invoice discount and payment are shared out by line value, so a product's amount,
        // paid, due and profit add up to the invoice when no product filter is applied.
        $addProfit = function ($sale) use ($product) {
            $items = $product
                ? $sale->items->where('product_id', (int) $product)->values()
                : $sale->items;
            $subtotal = (float) $sale->items->sum('total');
            $netRatio = $subtotal > 0 ? (float) $sale->total_amount / $subtotal : 0;
            $paidRatio = (float) $sale->total_amount > 0 ? (float) $sale->paid_amount / (float) $sale->total_amount : 0;

            $purchaseAmount = $items->sum(function ($item) use ($sale) {
                $purchasePrice = $item->product?->warehousePurchaseItems
                    ->filter(fn ($purchaseItem) => $purchaseItem->purchase !== null
                        && ($purchaseItem->purchase->warehouse_id === null
                            || (int) $purchaseItem->purchase->warehouse_id === (int) $sale->warehouse_id))
                    ->sortByDesc(fn ($purchaseItem) => $purchaseItem->purchase?->purchase_date)
                    ->first()?->purchase_price ?? 0;

                return (float) $purchasePrice * (float) $item->quantity;
            });

            $itemsTotal = round((float) $items->sum('total') * $netRatio, 2);
            $itemsPaid = round($itemsTotal * $paidRatio, 2);

            $sale->report_items = $items;
            $sale->report_quantity = (float) $items->sum('quantity');
            // Without a product filter the row shows the invoice itself (also covers invoices with no lines).
            $sale->report_total = $product ? $itemsTotal : round((float) $sale->total_amount, 2);
            $sale->report_paid = $product ? $itemsPaid : round((float) $sale->paid_amount, 2);
            $sale->report_due = round($sale->report_total - $sale->report_paid, 2);
            $sale->profit_amount = round($itemsPaid - $purchaseAmount, 2);
            return $sale;
        };
        $allSales->each($addProfit);
        if ($sales !== $allSales) {
            $sales->getCollection()->each($addProfit);
        }
        $salesmen = WarehouseSalesman::orderBy('name')->get();
        $products = Product::where('status', 1)->orderBy('product_name')->get();
        return compact('sales', 'allSales', 'salesmen', 'products', 'startDate', 'endDate', 'perPage');
    }

    public function lspSaleReport(Request $request)
    {
        return view('report.lspSales', $this->lspSaleReportData($request));
    }

    public function lspSaleReportPrint(Request $request)
    {
        return view('report.lsp-sales-export', $this->lspSaleReportData($request));
    }

    public function lspSaleReportPdf(Request $request)
    {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('report.lsp-sales-export', $this->lspSaleReportData($request))->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="lsp-sales-report.pdf"']);
    }

    public function lspSaleReportExcel(Request $request)
    {
        return Excel::download(new \App\Exports\LspSalesReportExport($this->lspSaleReportData($request)), 'lsp-sales-report.xlsx');
    }

    public function agentSaleReport()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');
        $user = request('user');
        $product = request('product');

        $query = AgentSale::query()
            ->when($user, function ($query) use ($user) {
                return $query->where('agent_id', $user);
            })
            ->when($product, function ($query) use ($product) {
                return $query->whereHas('saleProducts', function ($q) use ($product) {
                    $q->where('product_id', $product);
                });
            })
            ->filterByDate($startDate, $endDate)
            ->with([
                'agent:id,name,employee_name',
                'customer:id,agent_id,name,mobile',
                'saleProducts' => function ($q) use ($product) {
                    $q->select(
                        'id',
                        'product_id',
                        'agent_sale_id',
                        'stock_transfer_detail_id',
                        'qty',
                        'price',
                        'total_price'
                    );

                    if ($product) {
                        $q->where('product_id', $product);
                    }
                },
                'saleProducts.product:id,product_name',
                'saleProducts.stockTransferDetail:id,purchase_product_id',
                'saleProducts.stockTransferDetail.purchaseProduct:id,purchase_price',
            ]);

        $results = $query->orderBy('id', 'DESC')->get();

        $results->map(function ($sale) use ($product) {
            $purchaseAmount = 0;
            $saleAmount = 0;
            $qty = 0;

            $sale->saleProducts->each(function ($saleProduct) use (&$purchaseAmount, &$saleAmount, &$qty) {
                $saleAmount += $saleProduct->total_price;
                $qty += $saleProduct->qty;

                if ($saleProduct->stockTransferDetail && $saleProduct->stockTransferDetail->purchaseProduct) {
                    $purchaseAmount += $saleProduct->stockTransferDetail->purchaseProduct->purchase_price * $saleProduct->qty;
                }
            });

            $sale->qty = $qty;
            $sale->sale_amount = $saleAmount;
            $sale->purchase_amount = $purchaseAmount;

            if ($product) {
                $dueAmount = $sale->total_amount > 0 ? ($saleAmount / $sale->total_amount) * ($sale->total_amount - $sale->paying_amount) : 0;
                $paidAmount = $saleAmount - $dueAmount;
                $grossProfit = $saleAmount - $purchaseAmount;
                $profitAmount = $paidAmount - $purchaseAmount;
            } else {
                $dueAmount = $sale->total_amount - $sale->paying_amount;
                $paidAmount = $sale->total_amount - $dueAmount;
                $grossProfit = $sale->total_amount - $purchaseAmount;
                $profitAmount = $paidAmount - $purchaseAmount;
            }

            $sale->paid_amount = $paidAmount;
            $sale->due_amount = $dueAmount;
            $sale->gross_profit = $grossProfit;
            $sale->profit_amount = $profitAmount;

            return $sale;
        });

        return view('report.agentSales', [
            'agents' => User::active()->agent()->get(['id', 'name', 'employee_name']),
            'sales' => $results,
            'products' => Product::where('status', 1)->orderBy('product_name')->get(),
            'total_profit' => $results->sum('profit_amount')
        ]);
    }
}
