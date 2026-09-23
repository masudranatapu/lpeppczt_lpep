<?php

namespace App\Models;

use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtlManualHit extends Model
{
    use HasFactory, UserLog;

    protected $guarded = [];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }
}
