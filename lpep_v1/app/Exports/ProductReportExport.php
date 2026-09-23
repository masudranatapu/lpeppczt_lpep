<?php

namespace App\Exports;

use App\Exports\Export;
use Illuminate\Contracts\View\View;

class ProductReportExport extends Export
{
  public function title(): string
  {
    return 'Product Report';
  }

  public function view(): View
  {
    return view('exports.product-report', ['collection' => $this->collection]);
  }
}
