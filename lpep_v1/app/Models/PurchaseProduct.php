<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    # ATTRIBUTES

    public function getQuantityAttribute($val)
    {
        return round($val, 2);
    }

    public function getBoxPatternQuantityAttribute($val)
    {
        return round($val, 2);
    }

    public function getPurchasePriceAttribute($val)
    {
        return round($val, 2);
    }

    public function getTotalPriceAttribute($val)
    {
        return round($val, 2);
    }

    public function getSubtotalPriceAttribute($val)
    {
        return round($val, 2);
    }

    public function getOtherCostAttribute($val)
    {
        return round($val, 2);
    }

    # RELATIONS

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseReturnProduct()
    {
        return $this->hasOne(PurchaseReturnProduct::class);
    }

    public function boxPattern()
    {
        return $this->belongsTo(BoxPattern::class);
    }

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class);
    }

    public function stockTransferDetails()
    {
        return $this->hasMany(StockTransferDetail::class);
    }


    public function scopeWithStockProperties($q)
    {
        return $q->withSum('saleProducts as sale_product_qty', 'qty')
            ->withSum('stockTransferDetails as transferred_out_qty', 'quantity')
            ->with('saleProducts')
            ->addSelect(['available_quantity' => fn ($q) => $q->selectRaw('quantity - COALESCE(sale_product_qty, 0) - COALESCE(transferred_out_qty, 0)')])
            ->where('expiry_date', '>=', now()->format('Y-m-d'))
            ->orWhereNull('expiry_date');
    }
}
