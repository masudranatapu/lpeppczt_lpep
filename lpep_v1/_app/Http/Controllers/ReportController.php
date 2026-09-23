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
use Maatwebsite\Excel\Facades\Excel;

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
