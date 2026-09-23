<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function query(): Builder
    {
        // Subquery: latest purchase_price from purchase_products per product
        $latestPurchasePrice = DB::table('purchase_products')
            ->select('purchase_price')
            ->whereColumn('purchase_products.product_id', 'products.id')
            ->orderByDesc('purchase_products.id')
            ->limit(1);

        return Product::query()
            ->select([
                'products.id',
                'products.product_name',
                'products.selling_price',
            ])
            ->addSelect([
                'purchase_price' => $latestPurchasePrice,
            ])
            ->withStockProperties()
            ->orderBy('products.id');
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Purchase Price',
            'Selling Price',
            'Total In',
            'Total Out',
            'Stock Qty',
            'Sale Value',
            'Purchase Value',
        ];
    }

    public function map($product): array
    {
        $purchasePrice = (float) ($product->purchase_price ?? 0);
        $sellingPrice  = (float) ($product->selling_price ?? 0);

        $totalIn  = (float) round(
            (float) ($product->purchase_qty ?? 0) +
            (float) ($product->sale_return_qty ?? 0) +
            (float) ($product->transferred_in_qty ?? 0),
        2);

        $totalOut = (float) round(
            (float) ($product->sale_qty ?? 0) +
            (float) ($product->purchase_return_qty ?? 0) +
            (float) ($product->transferred_out_qty ?? 0),
        2);

        $stockQty      = (float) round($totalIn - $totalOut, 2);
        $saleValue     = (float) round($stockQty * $sellingPrice, 2);
        $purchaseValue = (float) round($stockQty * $purchasePrice, 2);

        return [
            (string) $product->product_name,
            $purchasePrice,
            $sellingPrice,
            $totalIn,
            $totalOut,
            $stockQty,
            $saleValue,
            $purchaseValue,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_00,
            'C' => NumberFormat::FORMAT_NUMBER_00,
            'D' => NumberFormat::FORMAT_NUMBER_00,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}

