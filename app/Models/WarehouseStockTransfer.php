<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;

class WarehouseStockTransfer extends Model
{
    use InvoiceNo;

    protected $table = 'lpep_warehouse_stock_transfers';
    protected $guarded = [];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(WarehouseStockTransferItem::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
