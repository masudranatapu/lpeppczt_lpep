<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentStockTransfer extends Model
{
    use HasFactory, InvoiceNo;

    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function details()
    {
        return $this->hasMany(AgentStockTransferDetails::class);
    }
}
