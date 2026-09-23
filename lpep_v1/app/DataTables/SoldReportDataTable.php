<?php

namespace App\DataTables;

use App\Models\Product;
use App\Exports\SoldReportExport;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Services\DataTable;

class SoldReportDataTable extends DataTable
{
    protected $exportClass = SoldReportExport::class;

    private function transform($collection)
    {
        return  $collection->map(function ($item) {
            $item->final_sold_quantity = round($item->total_sold_quantity - $item->total_sold_return_quantity, 2);
            $item->total_selling_price = round($item->total_sold_amount - $item->total_sold_return_amount, 2);
            $item->total_purchase_price = round($item->last_purchase_price * $item->final_sold_quantity, 2);
            $item->margin = round($item->total_selling_price - $item->total_purchase_price, 2);
            return $item;
        });
    }

    public function dataTable($query)
    {
        $items = $this->transform($query->get());

        return datatables()
            ->collection($items)
            ->addIndexColumn()
            ->with('sums', [
                'final_sold_quantity' => $items->sum('final_sold_quantity'),
                'total_selling_price' => $items->sum('total_selling_price'),
                'total_purchase_price' => $items->sum('total_purchase_price'),
                'margin' => $items->sum('margin'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Product::query()
            ->select('products.*')
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $query->whereHas('saleProducts.sale', function ($query) use ($request) {
                    $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
                });
            })
            ->addSelect([
                'last_purchase_price' => function ($q) {
                    $q->select('purchase_price')
                        ->from('purchase_products')
                        ->whereColumn('purchase_products.product_id', 'products.id')
                        ->orderByDesc('purchase_products.id')
                        ->limit(1);
                },
            ])
            ->withCount([
                "saleProducts as total_sold_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start && $request->end, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start, $request->end]);
                        })
                        ->select(DB::raw("SUM(qty)"));
                },
                "saleProducts as total_sold_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start && $request->end, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start, $request->end]);
                        })
                        ->select(DB::raw("SUM(total_price)"));
                },
                "saleReturnProducts as total_sold_return_quantity" => function ($query) use ($request) {
                    $query
                        ->when($request->start && $request->end, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start, $request->end]);
                        })
                        ->select(DB::raw("SUM(qty)"));
                },
                "saleReturnProducts as total_sold_return_amount" => function ($query) use ($request) {
                    $query
                        ->when($request->start && $request->end, function ($query) use ($request) {
                            $query->whereBetween(DB::raw("DATE_FORMAT(created_at,'%Y-%m-%d')"), [$request->start, $request->end]);
                        })
                        ->select(DB::raw("SUM(total_price)"));
                },
            ])
            ->having('total_sold_quantity', '>', 0);
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
                $('#data-table > tfoot > tr > th:nth-child(1)').text('Total');
                $('#data-table > tfoot > tr > th:nth-child(3)').text(this.api().ajax.json().sums.final_sold_quantity);
                $('#data-table > tfoot > tr > th:nth-child(4)').text(this.api().ajax.json().sums.total_selling_price);
                $('#data-table > tfoot > tr > th:nth-child(5)').text(this.api().ajax.json().sums.total_purchase_price);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.margin);

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
            ->pageLength(25)
            ->orderBy(1);
    }

    protected function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => __('page.sl'), 'searchable' => false],
            // ['data' => 'business_name', 'title' => __('page.branch1'), 'orderable' => false, 'searchable' => false],
            ['data' => 'product_name', 'title' => __('page.product_name')],
            ['data' => 'final_sold_quantity', 'title' => __('page.sellingqty')],
            ['data' => 'total_selling_price', 'title' => __('page.sellingprice')],
            ['data' => 'total_purchase_price', 'title' => __('page.purchaseprice')],
            ['data' => 'margin', 'title' => __('page.profit_loss')],
        ];
    }

    protected function filename()
    {
        return 'ProductWiseSoldReport_' . date('Y-m-d_h-ia');
    }
}
