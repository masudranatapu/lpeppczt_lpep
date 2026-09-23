<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class MonthlySaleReportExport extends Export
{
  public function title(): string
  {
    return 'Monthly Sale Report';
  }

  public function view(): View
  {
    return view('exports.monthly-sale-report', ['collection' => $this->collection]);
  }
}
