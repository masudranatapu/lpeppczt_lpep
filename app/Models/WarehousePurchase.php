<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePurchase extends Model
{
    use HasFactory, InvoiceNo;

    protected $table = 'lpep_warehouse_purchases';

    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function isLegacyWarehouseReceipt(): bool
    {
        return $this->warehouse_id !== null;
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(WarehousePurchaseItem::class, 'warehouse_purchase_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function payments()
    {
        return $this->hasMany(WarehousePurchasePayment::class, 'warehouse_purchase_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
