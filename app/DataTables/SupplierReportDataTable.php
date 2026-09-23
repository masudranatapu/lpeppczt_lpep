<?php

namespace App\DataTables;

use App\Models\Contact;
use App\Exports\SupplierReportExport;
use Yajra\DataTables\Services\DataTable;

class SupplierReportDataTable extends DataTable
{
    protected $exportClass = SupplierReportExport::class;

    private function transform($collection)
    {
        return $collection->map(function ($item) {
            $item->purchase_count = round($item->purchase_count, 2);
            $item->total_purchase = round($item->total_purchase, 2);
            $item->total_paying_amount = round($item->total_paying_amount, 2);
            $item->due = round($item->total_purchase - $item->total_paying_amount, 2);
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
                'purchase_count' => $items->sum('purchase_count'),
                'total_purchase' => $items->sum('total_purchase'),
                'total_paying_amount' => $items->sum('total_paying_amount'),
                'due' => $items->sum('due'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Contact::query()
            ->where('type', 'supplier')
            ->when($request->contact_id, fn ($q) => $q->where('id', $request->contact_id))
            ->withCount('purchase as purchase_count')
            ->withSum('purchase as total_purchase', 'total')
            ->withSum('contactPayment as total_paying_amount', 'paying_amount');
    }

    protected function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => __('page.coreport')[0], 'searchable' => false],
            ['data' => 'name', 'title' => __('page.coreport')[1]],
            // ['data' => 'business.name', 'title' => __('page.coreport')[2]],
            ['data' => 'supplier_business_name', 'title' => __('page.coreport')[3]],
            ['data' => 'mobile', 'title' => __('page.coreport')[4]],
            ['data' => 'purchase_count', 'title' => __('page.coreport')[5]],
            ['data' => 'total_purchase', 'title' => __('page.coreport')[6]],
            ['data' => 'total_paying_amount', 'title' => __('page.coreport')[7]],
            ['data' => 'due', 'title' => __('page.coreport')[8]],
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
                $('#data-table > tfoot > tr > th:nth-child(5)').text(this.api().ajax.json().sums.purchase_count);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.total_purchase);
                $('#data-table > tfoot > tr > th:nth-child(7)').text(this.api().ajax.json().sums.total_paying_amount);
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
        return 'SupplierReport_' . date('Y-m-d_h-ia');
    }
}
