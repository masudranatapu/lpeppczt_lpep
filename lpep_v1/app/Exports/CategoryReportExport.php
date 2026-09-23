<?php

namespace App\Exports;

use App\Exports\Export;
use Illuminate\Contracts\View\View;

class CategoryReportExport extends Export
{
  public function title(): string
  {
    return 'Category Report';
  }

  public function view(): View
  {
    return view('exports.category-report', ['collection' => $this->collection]);
  }
}
