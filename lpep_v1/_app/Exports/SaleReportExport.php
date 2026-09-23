<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class SaleReportExport extends Export
{
  public function title(): string
  {
    return 'Sale Report';
  }

  public function view(): View
  {
    return view('exports.sale-report', ['collection' => $this->collection]);
  }
}
