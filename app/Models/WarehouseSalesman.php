<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WarehouseSalesman extends Authenticatable
{
    use HasFactory;

    protected $table = 'lpep_warehouse_salesmen';

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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sales()
    {
        return $this->hasMany(WarehouseSale::class, 'warehouse_salesman_id');
    }

    public function stockAssignments()
    {
        return $this->hasMany(WarehouseSalesmanAssignment::class, 'warehouse_salesman_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
