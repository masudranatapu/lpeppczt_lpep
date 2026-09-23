<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitInfo extends Model
{
    use HasFactory;
     protected $fillable = [
        'customer_number',
        'app_customer_id',
        'memo_no',
        'attachment_path',
        'visit_date',
        'area_id',
        'agent_id',
        'description',
        'beneficiary_number',
        'visit_type'
    ];

    protected $casts = [
        'memo_no'    => 'array',
        'visit_date' => 'datetime',
    ];

    public function appCustomer()
    {
        return $this->belongsTo(AppCustomer::class, 'app_customer_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

     public function fees()
    {
        return $this->hasMany(VisitFee::class);
    }

}
