<?php

namespace App\Models;

use App\Traits\UserLog;
use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentCustomerTransaction extends Model
{
    use HasFactory, InvoiceNo, UserLog;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(AgentCustomerTransactionDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(AppCustomer::class, 'app_customer_id');
    }

    public function saleTransactions()
    {
        return $this->hasMany(AgentSaleTransaction::class, 'agent_customer_transaction_id');
    }
}
