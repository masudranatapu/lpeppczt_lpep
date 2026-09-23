<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class CtlImpregnation extends Model
{
    use HasFactory, UserLog, Exclude, BelongsToThrough;

    protected $fillable = [
        'id', 'farm_id', 'cattle_id', 'manual_hit_id', 'pal_date', 'pal_type',
        'pal_breed_id', 'seed_company_id', 'seed_percentage', 'straw_number', 'worker_info',
        'created_by', 'updated_by', 'created_by_type', 'updated_by_type', 'created_at', 'updated_at'
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function customer()
    {
        return $this->belongsToThrough(AppCustomer::class, Farm::class, null, '', [AppCustomer::class => 'app_customer_id']);
    }

    public function cattle()
    {
        return $this->belongsTo(Cattle::class, 'cattle_id');
    }

    public function manualHit()
    {
        return $this->belongsTo(CtlManualHit::class, 'manual_hit_id');
    }

    public function palBreed()
    {
        return $this->belongsTo(CattleBreed::class, 'pal_breed_id');
    }

    public function seedCompany()
    {
        return $this->belongsTo(SeedCompany::class, 'seed_company_id');
    }
}
