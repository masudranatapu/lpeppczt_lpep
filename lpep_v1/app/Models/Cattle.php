<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cattle extends Model
{
    use HasFactory, UserLog;

    protected $guarded = [];
    
     protected $casts = [
        'id' => 'integer',
        'farm_id' => 'integer',
        'cattle_group_id' => 'integer',
        'cattle_breed_id' => 'integer',
        'insurance_company_id' => 'integer',
        'insurance_type_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function diseaseHistories()
    {
        return $this->hasMany(CtlDiseaseHistory::class, 'cattle_id');
    }

    public function healthInfos()
    {
        return $this->hasMany(CtlHealthInfo::class, 'cattle_id');
    }

    public function calves()
    {
        return $this->hasMany(Calf::class, 'cattle_id');
    }
}
