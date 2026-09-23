<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtlHealthInfo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function healthInfo()
    {
        return $this->belongsTo(HealthInfo::class, 'health_info_id');
    }
}
