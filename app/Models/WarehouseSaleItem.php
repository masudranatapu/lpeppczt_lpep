<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSaleItem extends Model
{
    use HasFactory;

    protected $table = 'lpep_warehouse_sale_items';

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(WarehouseSale::class, 'warehouse_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
