<?php

namespace App\DataTables;

use App\Models\Purchase;
use App\Exports\PurchaseReportExport;
use Yajra\DataTables\Services\DataTable;

class PurchaseReportDataTable extends DataTable
{
    protected $exportClass = PurchaseReportExport::class;

    private function transform($collection)
    {
        return $collection->map(function ($item) {
            $item->total = round($item->total, 2);
            $item->total_pay = round($item->total_pay, 2);
            // $item->purchase_product_names = collect($item->purchaseProducts)->map(fn ($pro) => $pro->product->product_name . ' (' . $pro->quantity . ')');
            $item->purchase_product_names = collect($item->purchaseProducts)->map(function ($pro) {
                return $pro->product
                    ? $pro->product->product_name . ' (' . $pro->quantity . ')'
                    : 'Unknown Product (' . $pro->quantity . ')';
            });

            $item->due = round($item->total - $item->total_pay, 2);
            return $item;
        });
    }

    public function dataTable($query)
    {
        $items = $this->transform($query->get());

        return datatables()
            ->collection($items)
            ->addIndexColumn()
            ->editColumn('purchase_product_names', fn ($item) => $item->purchase_product_names->join('<br>' . PHP_EOL))
            ->editColumn('payment_status', function ($item) {
                return $item->due > 0
                    ? "<span class=\"badge badge-danger\">Due</span>"
                    : "<span class=\"badge badge-success\">Paid</span>";
            })
            ->escapeColumns([])
            ->with('sums', [
                'total' => $items->sum('total'),
                'total_pay' => $items->sum('total_pay'),
                'due' => $items->sum('due'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Purchase::query()
            ->with('purchaseProducts.product', 'supplier')
            ->when($request->start_date && $request->end_date, fn ($q) => $q->whereBetween('purchase_date', [$request->start_date, $request->end_date]));
    }

    protected function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => __('page.pureport')[1], 'searchable' => false],
            ['data' => 'purchase_date', 'title' => __('page.pureport')[2]],
            // ['data' => 'business.name', 'title' => __('page.pureport')[3]],
            ['data' => 'invoice_no', 'title' => __('page.pureport')[4]],
            ['data' => 'supplier.name', 'title' => __('page.pureport')[5]],
            ['data' => 'purchase_product_names', 'title' => __('page.pureport')[6], 'width' => '20%'],
            ['data' => 'total', 'title' => __('page.pureport')[7]],
            ['data' => 'total_pay', 'title' => __('page.pureport')[8]],
            ['data' => 'due', 'title' => __('page.pureport')[9]],
            ['data' => 'payment_status', 'title' => __('page.pureport')[10]],
        ];
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
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.total);
                $('#data-table > tfoot > tr > th:nth-child(7)').text(this.api().ajax.json().sums.total_pay);
                $('#data-table > tfoot > tr > th:nth-child(8)').text(this.api().ajax.json().sums.due);

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

    protected function filename()
    {
        return 'PurchaseReport_' . date('Y-m-d_h-ia');
    }
}
