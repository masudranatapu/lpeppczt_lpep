<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSalePayment extends Model
{
    use HasFactory;

    protected $table = 'lpep_warehouse_sale_payments';
    protected $guarded = [];
    protected $casts = ['payment_date' => 'date'];

    public function sale()
    {
        return $this->belongsTo(WarehouseSale::class, 'warehouse_sale_id');
    }
}
