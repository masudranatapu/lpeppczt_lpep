<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;

class WarehouseSalesmanReturn extends Model
{
    use InvoiceNo;

    protected $table = 'lpep_warehouse_salesman_returns';
    protected $guarded = [];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function salesman() { return $this->belongsTo(WarehouseSalesman::class, 'warehouse_salesman_id'); }
    public function items() { return $this->hasMany(WarehouseSalesmanReturnItem::class, 'warehouse_salesman_return_id'); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
