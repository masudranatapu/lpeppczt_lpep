<?php

namespace App\Models;

use App\Traits\AutoFileDelete;
use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calf extends Model
{
    use HasFactory, UserLog, Exclude, AutoFileDelete;

    protected $fillable = [
        'id', 'farm_id', 'cattle_id',
        'tag', 'name', 'birth_date', 'gender', 'weight', 'image',
        'created_at', 'created_by', 'created_by_type',
        'updated_at', 'updated_by', 'updated_by_type'
    ];

    public function cattle()
    {
        return $this->belongsTo(Cattle::class, 'cattle_id');
    }

    public function birthProblems()
    {
        return $this->hasMany(ClfBirthProblem::class, 'calf_id');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    protected static function autoFileDeleteConfig()
    {
        return [
            [
                'disk' => 'simpleupload',
                'property' => 'image'
            ]
        ];
    }
}
