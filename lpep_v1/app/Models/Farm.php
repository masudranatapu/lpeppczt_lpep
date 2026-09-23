<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory, UserLog;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(AppCustomer::class, 'app_customer_id');
    }

    public function cattle()
    {
        return $this->hasMany(Cattle::class, 'farm_id');
    }

    public function calves()
    {
        return $this->hasMany(Calf::class, 'farm_id');
    }
}
