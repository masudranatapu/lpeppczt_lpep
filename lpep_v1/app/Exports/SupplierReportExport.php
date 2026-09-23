<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class SupplierReportExport extends Export
{
  public function title(): string
  {
    return 'Supplier Report';
  }

  public function view(): View
  {
    return view('exports.supplier-report', ['collection' => $this->collection]);
  }
}
