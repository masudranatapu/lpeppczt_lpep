<?php

namespace App\Models;

use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class AppCustomer extends Model
{
    use HasFactory, UserLog, HasApiTokens;

    protected $guarded = [];

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    public function sales()
    {
        return $this->hasMany(AgentSale::class);
    }

    public function agentCustomerTransactions()
    {
        return $this->hasMany(AgentCustomerTransaction::class);
    }

    public function agentSales()
    {
        return $this->hasMany(AgentSale::class, 'app_customer_id');
    }
}
