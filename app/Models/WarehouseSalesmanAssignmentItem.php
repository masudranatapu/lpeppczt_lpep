<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseSalesmanAssignmentItem extends Model
{
    protected $table = 'lpep_warehouse_salesman_assignment_items';
    protected $guarded = [];

    public function assignment() { return $this->belongsTo(WarehouseSalesmanAssignment::class, 'warehouse_salesman_assignment_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
