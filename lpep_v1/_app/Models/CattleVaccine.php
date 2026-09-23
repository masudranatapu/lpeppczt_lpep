<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CattleVaccine extends Model
{
    use HasFactory, UserLog, Exclude;

    protected $fillable = [
        'id', 'disease_id', 'name',
        'created_at', 'created_by',
        'updated_at', 'updated_by'
    ];

    public function disease()
    {
        return $this->belongsTo(CattleDisease::class, 'disease_id');
    }
}
