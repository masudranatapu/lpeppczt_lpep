<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseSalesmanReturnItem extends Model
{
    protected $table = 'lpep_warehouse_salesman_return_items';
    protected $guarded = [];

    public function return() { return $this->belongsTo(WarehouseSalesmanReturn::class, 'warehouse_salesman_return_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
