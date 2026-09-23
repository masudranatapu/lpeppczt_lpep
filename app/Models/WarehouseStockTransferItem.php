<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockTransferItem extends Model
{
    protected $table = 'lpep_warehouse_stock_transfer_items';
    protected $guarded = [];

    public function transfer() { return $this->belongsTo(WarehouseStockTransfer::class, 'warehouse_stock_transfer_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
