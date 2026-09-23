<?php

namespace App\DataTables;

use App\Models\Sale;
use App\Exports\SaleReportExport;
use Yajra\DataTables\Services\DataTable;

class SaleReportDataTable extends DataTable
{
    protected $exportClass = SaleReportExport::class;

    private function transform($collection)
    {
        return $collection->map(function ($item) {
            $item->invoice = date('Y') . $item->id;
            $item->due = round($item->total_amount - $item->paying_amount, 2);
            return $item;
        });
    }

    public function dataTable($query)
    {
        $items = $this->transform($query->get());

        return datatables()
            ->collection($items)
            ->editColumn('payment_status', function ($item) {
                return $item->due > 0
                    ? "<span class=\"badge badge-danger\">Due</span>"
                    : "<span class=\"badge badge-success\">Paid</span>";
            })
            ->escapeColumns([])
            ->addIndexColumn()
            ->with('sums', [
                'total_amount' => $items->sum('total_amount'),
                'paying_amount' => $items->sum('paying_amount'),
                'due' => $items->sum('due'),
            ]);
    }

    // public function query()
    // {
    //     $request = request();

    //     return Sale::query()
    //         ->when($request->start && $request->end, fn ($query) => $query->whereBetween('sale_date', [$request->start, $request->end]))
    //         ->when($request->user, fn ($query) => $query->where("user_id", $request->user))
    //         ->addSelect([
    //             'customer_name' => function ($q) {
    //                 $q->select('name')
    //                     ->from('contacts')
    //                     ->whereColumn('contacts.id', 'sales.contact_id')
    //                     ->limit(1);
    //             }
    //         ])
    //         ->orderBy('id', 'desc');
    // }

    public function query()
{
    $request = request();
    // $model = ($request->user == 1) ? Sale::query() : \App\Models\AgentSale::query();

    return Sale::query()
        ->when($request->start && $request->end, fn($query) => $query->whereBetween('sale_date', [$request->start, $request->end]))
        ->when($request->user, fn($query) => $query->where("user_id", $request->user))
        ->orderBy('id', 'desc');
}




    protected function getColumns()
    {
        return [
            ['data' => 'DT_RowIndex', 'title' => __('page.sreport')[0], 'searchable' => false],
            ['data' => 'sale_date', 'title' => __('page.sreport')[1]],
            // ['data' => 'business_name', 'title' => __('page.sreport')[2]],
            ['data' => 'invoice', 'title' => __('page.sreport')[3]],
            ['data' => 'customer_name', 'title' => __('page.sreport')[4]],
            ['data' => 'total_amount', 'title' => __('page.sreport')[5]],
            ['data' => 'paying_amount', 'title' => __('page.sreport')[6]],
            ['data' => 'due', 'title' => __('page.sreport')[7]],
            ['data' => 'payment_status', 'title' => __('page.sreport')[8]],
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
                $('#data-table > tfoot > tr > th:nth-child(5)').text(this.api().ajax.json().sums.total_amount);
                $('#data-table > tfoot > tr > th:nth-child(6)').text(this.api().ajax.json().sums.paying_amount);
                $('#data-table > tfoot > tr > th:nth-child(7)').text(this.api().ajax.json().sums.due);

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
        return 'SaleReport_' . date('Y-m-d_h-ia');
    }
}
