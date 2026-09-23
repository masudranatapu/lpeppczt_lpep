<?php

namespace App\Exports;

use App\Exports\Export;
use Illuminate\Contracts\View\View;

class CustomerReportExport extends Export
{
  public function title(): string
  {
    return 'Customer Report';
  }

  public function view(): View
  {
    return view('exports.customer-report', ['collection' => $this->collection]);
  }
}
