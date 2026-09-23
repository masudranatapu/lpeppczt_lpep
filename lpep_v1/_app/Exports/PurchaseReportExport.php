<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class PurchaseReportExport extends Export
{
  public function title(): string
  {
    return 'Purchase Report';
  }

  public function view(): View
  {
    return view('exports.purchase-report', ['collection' => $this->collection]);
  }
}
