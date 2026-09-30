<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class WarehouseStockExport implements FromView, ShouldAutoSize, WithTitle
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('warehouse.stock-export', ['reportType' => 'excel'] + $this->data);
    }

    public function title(): string
    {
        return 'Area Office Stock';
    }
}
