<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSale extends Model
{
    use HasFactory, InvoiceNo;

    protected $table = 'lpep_warehouse_sales';

    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function salesman()
    {
        return $this->belongsTo(WarehouseSalesman::class, 'warehouse_salesman_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseSaleItem::class, 'warehouse_sale_id');
    }

    public function payments()
    {
        return $this->hasMany(WarehouseSalePayment::class, 'warehouse_sale_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
