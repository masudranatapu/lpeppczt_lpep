<?php

namespace App\Models;

use App\Traits\UserLog;
use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminAgentTransaction extends Model
{
    use HasFactory, InvoiceNo, UserLog;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(AdminAgentTransactionDetail::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
