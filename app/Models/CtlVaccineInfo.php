<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class CtlVaccineInfo extends Model
{
    use HasFactory, UserLog, Exclude, BelongsToThrough;

    protected $fillable = [
        'id', 'farm_id', 'cattle_id',
        'cattle_disease_id', 'cattle_vaccine_id', 'date', 'remark',
        'created_at', 'created_by', 'created_by_type',
        'updated_at', 'updated_by', 'updated_by_type'
    ];

    public function cattle()
    {
        return $this->belongsTo(Cattle::class, 'cattle_id');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function customer()
    {
        return $this->belongsToThrough(Contact::class, Farm::class, null, '', [Contact::class => 'app_customer_id']);
    }

    public function cattleDisease()
    {
        return $this->belongsTo(CattleDisease::class, 'cattle_disease_id');
    }

    public function cattleVaccine()
    {
        return $this->belongsTo(CattleVaccine::class, 'cattle_vaccine_id');
    }
}
