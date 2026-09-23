<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class WarehousePortalSalesReportExport implements FromView, ShouldAutoSize, WithTitle
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('warehouse-portal.reports.excel', $this->data);
    }

    public function title(): string
    {
        return 'Area Office Sales Report';
    }
}
