<?php

namespace App\DataTables;

use App\Models\Category;
use App\Exports\CategoryReportExport;
use Yajra\DataTables\Services\DataTable;

class CategoryReportDataTable extends DataTable
{
    protected $exportClass = CategoryReportExport::class;

    private function transform($collection)
    {
        return  $collection->map(function ($item) {
            $item->final_purchase_quantity = round($item->products->sum('total_purchase_quantity') - $item->products->sum('total_purchase_return_quantity'), 2);
            $item->final_purchase_amount = round($item->products->sum('total_purchase_amount') - $item->products->sum('total_purchase_return_amount'), 2);
            $item->final_sold_quantity = round($item->products->sum('total_sold_quantity') - $item->products->sum('total_sold_return_quantity'), 2);
            $item->final_sold_amount = round($item->products->sum('total_sold_amount') - $item->products->sum('total_sold_return_amount'), 2);
            $item->margin = round($item->final_sold_amount - $item->final_purchase_amount, 2);
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
                'final_purchase_quantity' => $items->sum('final_purchase_quantity'),
                'final_purchase_amount' => $items->sum('final_purchase_amount'),
                'final_sold_quantity' => $items->sum('final_sold_quantity'),
                'final_sold_amount' => $items->sum('final_sold_amount'),
                'margin' => $items->sum('margin'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Category::query()
            ->orderBy('id')
            ->with([
                'products' => function ($q) use ($request) {
                    $q->select('category_id')
                        ->withSum('purchaseProduct as total_purchase_quantity', 'quantity')
                        ->withSum('purchaseProduct as total_purchase_amount', 'total_price')
                        ->withSum('purchaseReturnProduct as total_purchase_return_quantity', 'quantity')
                        ->withSum('purchaseReturnProduct as total_purchase_return_amount', 'total')
                        ->withSum('saleProduct as total_sold_quantity', 'qty')
                        ->withSum('saleProduct as total_sold_amount', 'total_price')
                        ->withSum('returnProduct as total_sold_return_quantity', 'qty')
                        ->withSum('returnProduct as total_sold_return_amount', 'total_price');
                }
            ]);
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
                $('#data-table > tfoot > tr > th:nth-child(3)').text(this.api().ajax.json().sums.final_purchase_quantity);
                $('#data-table > tfoot > tr > th:nth-child(4)').text(this.api().ajax.json().sums.final_sold_quantity);
                $('#data-table > tfoot > tr > th:nth-child(5)').text(this.api().ajax.json().sums.final_purchase_amount);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.final_sold_amount);
                $('#data-table > tfoot > tr > th:nth-child(7)').text(this.api().ajax.json().sums.margin);

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
            ['data' => 'DT_RowIndex', 'title' => __('page.creport')[1], 'searchable' => false],
            ['data' => 'category_name', 'title' => __('page.creport')[2]],
            // ['data' => 'business_name', 'title' => __('page.creport')[3], 'orderable' => false, 'searchable' => false],
            ['data' => 'final_purchase_quantity', 'title' => __('page.creport')[4]],
            ['data' => 'final_sold_quantity', 'title' => __('page.creport')[5]],
            ['data' => 'final_purchase_amount', 'title' => __('page.creport')[6]],
            ['data' => 'final_sold_amount', 'title' => __('page.creport')[7]],
            ['data' => 'margin', 'title' => __('page.creport')[8]],
        ];
    }

    protected function filename()
    {
        return 'CategoryReport_' . date('Y-m-d_h-ia');
    }
}
