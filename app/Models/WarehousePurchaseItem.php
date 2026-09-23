<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'lpep_warehouse_purchase_items';

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(WarehousePurchase::class, 'warehouse_purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
