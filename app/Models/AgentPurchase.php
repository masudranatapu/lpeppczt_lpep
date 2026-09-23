<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPurchase extends Model
{
    use HasFactory, InvoiceNo, UserLog;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(AppCustomer::class, 'app_customer_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(AgentPurchaseProduct::class);
    }

    public function transactions()
    {
        return $this->hasMany(AgentPurchaseTransaction::class);
    }
}
