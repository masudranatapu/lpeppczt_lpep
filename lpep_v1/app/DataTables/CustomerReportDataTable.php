<?php

namespace App\DataTables;

use App\Exports\CustomerReportExport;
use App\Models\Contact;
use Yajra\DataTables\Services\DataTable;

class CustomerReportDataTable extends DataTable
{
    protected $exportClass = CustomerReportExport::class;

    public function transform($collection)
    {
        return $collection->map(function ($item) {
            $item->sale_count = round($item->sale_count, 2);
            $item->total_sale = round($item->total_sale, 2);
            $item->total_paying_amount = round($item->total_paying_amount, 2);
            $item->due = round($item->total_sale - $item->total_paying_amount, 2);
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
                'sale_count' => $items->sum('sale_count'),
                'total_sale' => $items->sum('total_sale'),
                'total_paying_amount' => $items->sum('total_paying_amount'),
                'due' => $items->sum('due'),
            ]);
    }

    public function query()
    {
        $request = request();

        return Contact::query()
            ->where('type', 'customer')
            ->when($request->contact_id, fn ($q) => $q->where('id', $request->contact_id))
            ->withCount('sale as sale_count')
            ->withSum('sale as total_sale', 'total_amount')
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
            ['data' => 'sale_count', 'title' => __('page.coreport')[5]],
            ['data' => 'total_sale', 'title' => __('page.coreport')[6]],
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
                $('#data-table > tfoot > tr > th:nth-child(5)').text(this.api().ajax.json().sums.sale_count);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.total_sale);
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
        return 'CustomerReport_' . date('Y-m-d_h-ia');
    }
}
