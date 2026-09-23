<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AgentSaleProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getQtyAttribute($value)
    {
        return round($value, 2);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function sale()
    {
        return $this->belongsTo(AgentSale::class, 'agent_sale_id');
    }

    public function stockTransferDetail()
    {
        return $this->belongsTo(StockTransferDetail::class);
    }


        public function getPurchasePriceAttribute()
        {
            return PurchaseProduct::where('product_id', $this->product_id)
                ->orderByDesc('id')  // Get the latest purchase price
                ->value('purchase_price');
        }

        public function getProfitAttribute()
        {
            return $this->selling_price - $this->purchase_price;
        }


        public function purchaseProduct()
        {
            return $this->hasOne(PurchaseProduct::class, 'product_id', 'product_id');
        }



}
