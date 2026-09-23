<?php

namespace App\Models;

use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CattleDisease extends Model
{
    use HasFactory, UserLog;

    protected $guarded = [];

    public function vaccines()
    {
        return $this->hasMany(CattleVaccine::class, 'disease_id');
    }
}
