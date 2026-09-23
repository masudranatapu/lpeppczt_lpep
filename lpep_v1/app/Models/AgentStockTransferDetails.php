<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentStockTransferDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function agentPurchaseProduct()
    {
        return $this->belongsTo(AgentPurchaseProduct::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
