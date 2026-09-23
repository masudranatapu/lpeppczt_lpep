<?php

namespace App\Exports;

use App\Exports\Export;
use Illuminate\Contracts\View\View;

class SoldReportExport extends Export
{
  public function title(): string
  {
    return 'Product Wise Sold Report';
  }

  public function view(): View
  {
    return view('exports.sold-report', ['collection' => $this->collection]);
  }
}
