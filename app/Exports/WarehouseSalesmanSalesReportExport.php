<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class WarehouseSalesmanSalesReportExport implements FromView, ShouldAutoSize, WithTitle
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('warehouse-salesman.report-excel', $this->data);
    }

    public function title(): string
    {
        return 'Sales Report';
    }
}
