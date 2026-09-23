<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class LspSalesReportExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(private array $data) {}

    public function view(): View
    {
        return view('report.lsp-sales-excel', $this->data);
    }

    public function title(): string
    {
        return 'LSP Sales';
    }
}
