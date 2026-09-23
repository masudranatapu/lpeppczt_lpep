<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitFee extends Model
{
    use HasFactory;
     protected $fillable = [
        'visit_info_id',
        'fee_type',
        'amount',
    ];

    public function visit()
    {
        return $this->belongsTo(VisitInfo::class, 'visit_info_id');
    }
}
