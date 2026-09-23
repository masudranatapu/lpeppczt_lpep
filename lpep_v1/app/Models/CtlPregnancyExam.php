<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class CtlPregnancyExam extends Model
{
    use HasFactory, UserLog, Exclude, BelongsToThrough;

    protected $fillable = [
        'id', 'farm_id', 'cattle_id',
        'impregnation_id', 'is_pregnant', 'expected_delivery_date',
        'created_at', 'created_by', 'created_by_type',
        'updated_at', 'updated_by', 'updated_by_type',
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
        return $this->belongsToThrough(AppCustomer::class, Farm::class, null, '', [AppCustomer::class => 'app_customer_id']);
    }

    public function impregnation()
    {
        return $this->belongsTo(CtlImpregnation::class, 'impregnation_id');
    }
}
