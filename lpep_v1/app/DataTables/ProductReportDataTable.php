<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductReportExport;
use Yajra\DataTables\Services\DataTable;

class ProductReportDataTable extends DataTable
{
    protected $exportClass = ProductReportExport::class;

    private function transform($collection)
    {
        return  $collection->map(function ($item) {
            $item->final_purchase_quantity = round($item->total_purchase_quantity - $item->total_purchase_return_quantity, 2);
            $item->final_purchase_amount = round($item->total_purchase_amount - $item->total_purchase_return_amount, 2);
            $item->final_sold_quantity = round($item->total_sold_quantity, 2);
            $item->final_sold_amount = round($item->total_sold_amount, 2);
            $item->final_sold_return_quantity = round($item->total_sold_return_quantity, 2);
            $item->final_sold_return_amount = round($item->total_sold_return_amount, 2);
            $item->margin_quantity = round($item->final_purchase_quantity - $item->final_sold_quantity + $item->final_sold_return_quantity, 2);
            $item->margin_amount = round($item->final_sold_amount - $item->final_sold_return_amount - $item->final_purchase_amount, 2);
            $item->margin_selling_price = round($item->margin_quantity * $item->selling_price, 2);
            return $item;
        });
    }

    public function dataTable($query)
    {
        $items = $this->transform($query->get());

        return datatables()
            ->collection($items)
            ->addIndexColumn()
            ->editColumn('product_name', fn ($item) => "<strong>{$item->product_name}</strong>")
            ->addColumn('final_purchase_string', fn ($item) => "({$item->final_purchase_quantity}) $item->final_purchase_amount")
            ->addColumn('final_sold_string', fn ($item) => "({$item->final_sold_quantity}) $item->final_sold_amount")
            ->addColumn('final_sold_return_string', fn ($item) => "({$item->final_sold_return_quantity}) $item->final_sold_return_amount")
            ->addColumn('margin_selling_price_string', fn ($item) => "({$item->margin_quantity}) $item->margin_selling_price")
            ->escapeColumns([])
            ->with('sums', [
                'final_purchase_quantity' => $items->sum('final_purchase_quantity'),
                'final_purchase_amount' => $items->sum('final_purchase_amount'),
                'final_sold_quantity' => $items->sum('final_sold_quantity'),
                'final_sold_amount' => $items->sum('final_sold_amount'),
                'final_sold_return_quantity' => $items->sum('final_sold_return_quantity'),
                'final_sold_return_amount' => $items->sum('final_sold_return_amount'),
                'margin_quantity' => $items->sum('margin_quantity'),
                'margin_amount' => $items->sum('margin_amount'),
                'margin_selling_price' => $items->sum('margin_selling_price'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Product::query()
            ->select('products.*')
            ->withCount([
                "purchaseProducts as total_purchase_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(quantity)"));
                },
                "purchaseProducts as total_purchase_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(total_price)"));
                },
                "purchaseReturnProducts as total_purchase_return_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(quantity)"));
                },
                "purchaseReturnProducts as total_purchase_return_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(total)"));
                },
                "saleProducts as total_sold_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(qty)"));
                },
                "saleProducts as total_sold_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(total_price)"));
                },
                "saleReturnProducts as total_sold_return_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(qty)"));
                },
                "saleReturnProducts as total_sold_return_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start_date, $request->end_date]);
                        })
                        ->select(DB::raw("SUM(total_price)"));
                }
            ])
            ->when($request->search_by, function ($query) use ($request) {
                $query->where('product_name', "like", "%{$request->search_by}%");
            });
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->headerCallback('function (thead) {$(thead).addClass("theme-primary text-white")}')
            ->parameters([
                'dom'          => 'Blfrtip',
                'buttons'      => ['pdf', 'excel'],
                'initComplete' => "function () {
                                        $('.dt-buttons').removeClass('dt-buttons');
                                        $('#data-table_filter').css('display','inline-block');
                                        $('#data-table_filter').css('margin-left','20px');
                                    }",
            ])
            ->drawCallback("function() {
                const sums = this.api().ajax.json().sums;
                $('#data-table > tfoot > tr > th:nth-child(1)').text('Total');
                $('#data-table > tfoot > tr > th:nth-child(3)').text('('+sums.final_purchase_quantity+') '+sums.final_purchase_amount);
                $('#data-table > tfoot > tr > th:nth-child(4)').text('('+sums.final_sold_quantity+') '+sums.final_sold_amount);
                $('#data-table > tfoot > tr > th:nth-child(5)').text('('+sums.final_sold_return_quantity+') '+sums.final_sold_return_amount);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(sums.margin_amount);
                $('#data-table > tfoot > tr > th:nth-child(7)').text('('+sums.margin_quantity+') '+sums.margin_selling_price);

                $('#data-table_wrapper').prepend('<div id=\"filter-bar\" style=\"display:flex; justify-content: space-between; align-items: center\"></div>');
                let element = $('#data-table_wrapper .dataTables_length').detach();
                $('#filter-bar').append(element);
                element = $('#data-table_wrapper .btn-group').detach();
                $('#filter-bar').append(element);
                element = $('#data-table_wrapper .dataTables_filter').detach();
                $('#filter-bar').append(element);
                $('#filter-bar input').focus();
            }")
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]])
            ->pageLength(25);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => __('page.preport')[1], 'searchable' => false],
            ['data' => 'product_name', 'title' => __('page.preport')[2], 'width' => '25%'],
            ['data' => 'final_purchase_string', 'title' => __('page.preport')[4]],
            ['data' => 'final_sold_string', 'title' => __('page.preport')[5]],
            ['data' => 'final_sold_return_string', 'title' => __('page.return')],
            ['data' => 'margin_amount', 'title' => __('page.preport')[6]],
            ['data' => 'margin_selling_price_string', 'title' => __('page.preport')[7]],
        ];
    }

    protected function filename()
    {
        return 'ProductReport_' . date('Y-m-d_h-ia');
    }
}
