<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AreaManagerDailyReportExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(private array $data) {}

    public function view(): View
    {
        return view('warehouse-portal.reports.area-manager-daily-excel', $this->data);
    }

    public function title(): string
    {
        return 'Area Manager Daily';
    }
}
