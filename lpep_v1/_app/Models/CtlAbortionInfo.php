<?php

namespace App\Models;

use App\Traits\Exclude;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

class CtlAbortionInfo extends Model
{
    use HasFactory, UserLog, Exclude, BelongsToThrough;

    protected $fillable = [
        'id', 'farm_id', 'cattle_id', 'pregnancy_exam_id', 'date',
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
        return $this->belongsToThrough(Contact::class, Farm::class, null, '', [Contact::class => 'customer_id']);
    }

    public function pregnancyExam()
    {
        return $this->belongsTo(CtlPregnancyExam::class, 'pregnancy_exam_id');
    }
}
