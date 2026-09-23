<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPurchaseProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(AgentPurchase::class, 'agent_purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function agentStockTransferDetails()
    {
        return $this->hasMany(AgentStockTransferDetails::class);
    }

    public function scopeWithStockProperties($q)
    {
        return $q->withSum('agentStockTransferDetails as transferred_out_qty', 'quantity')
            ->addSelect(['available_quantity' => fn ($q) => $q->selectRaw('quantity - COALESCE(transferred_out_qty, 0)')]);
    }
}
