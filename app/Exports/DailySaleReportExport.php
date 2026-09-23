<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class DailySaleReportExport extends Export
{
  public function title(): string
  {
    return 'Daily Sale Report';
  }

  public function view(): View
  {
    return view('exports.daily-sale-report', ['collection' => $this->collection]);
  }
}
