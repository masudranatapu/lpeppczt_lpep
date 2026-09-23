<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Warehouse extends Authenticatable
{
    use HasFactory;

    protected $table = 'lpep_warehouses';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getNameAttribute($value): string
    {
        $name = trim((string) $value);

        return preg_replace('/^\s*id\)\>\s*/i', '', $name) ?? $name;
    }

    public function salesmen()
    {
        return $this->hasMany(WarehouseSalesman::class, 'warehouse_id');
    }

    public function purchases()
    {
        return $this->hasMany(WarehousePurchase::class, 'warehouse_id');
    }

    public function stockTransfers()
    {
        return $this->hasMany(WarehouseStockTransfer::class, 'warehouse_id');
    }

    public function salesmanAssignments()
    {
        return $this->hasMany(WarehouseSalesmanAssignment::class, 'warehouse_id');
    }

    public function sales()
    {
        return $this->hasMany(WarehouseSale::class, 'warehouse_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
