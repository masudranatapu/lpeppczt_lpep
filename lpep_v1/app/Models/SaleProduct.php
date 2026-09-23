<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
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
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function purchaseProduct()
    {
        return $this->belongsTo(PurchaseProduct::class);
    }


    public function agentStockTransferDetail()
    {
        return $this->belongsTo(AgentStockTransferDetails::class);
    }
}
