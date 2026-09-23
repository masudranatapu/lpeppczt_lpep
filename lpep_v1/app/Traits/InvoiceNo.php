<?php

namespace App\Traits;

use App\Models\Purchase;
use App\Models\AdminDeposit;
use App\Models\AgentPurchase;
use App\Models\AgentSale;
use App\Models\Sale;
use App\Models\StockTransfer;

trait InvoiceNo
{
  public static function nextInvoiceNo()
  {
    $format = self::getInvoiceFormat();

    $model = self::query()
      ->select('id', 'invoice_no')
      ->where('invoice_no', 'like', "{$format}%")
      ->orderByDesc('invoice_no')
      ->first();

    $invoice_no = $model ? $model->invoice_no : null;
    $invoice_no = explode('-', $invoice_no);

    return count($invoice_no) < 2
      ? $format . '00001'
      : $invoice_no[0] . '-' . ($invoice_no[1] + 1);
  }

  private static function getInvoiceFormat()
  {
    $format = date('ym');

    switch (self::class) {
      case Purchase::class:
        return 'P-' . $format;
      case Sale::class:
        return 'S-' . $format;
      case AdminDeposit::class:
        return 'D-' . $format;
      case StockTransfer::class:
        return 'ST-' . $format;
      case AgentSale::class:
        return 'AS-' . $format;
      case AgentPurchase::class:
        return 'AP-' . $format;
      default:
        return $format;
    }
  }
}
