<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userPermission()
    {
        return $this->hasOne(UserPermission::class, 'user_id');
    }

    public function sellCarts()
    {
        return $this->hasMany(SellCart::class);
    }

    public function receivedLoansFromAdmin()
    {
        return $this->hasMany(AdminAgentTransaction::class, 'agent_id');
    }

    public function sentLoansToCustomers()
    {
        return $this->hasMany(AgentCustomerTransaction::class, 'agent_id');
    }


    /**
     * Get all of the agents for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agentSales(): HasMany
    {
        return $this->hasMany(AgentSale::class, 'agent_id');
    }

    public function filteredAgentSales($year, $month)
    {
        return $this->hasMany(AgentSale::class, 'agent_id')
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month);
    }


    /**
     * Scope a query to only include agents.
     */
    public function scopeAgent(Builder $query): void
    {
        $query->where('user_type',  'staff');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status',  1);
    }



    /**
     * Get all of the incomeDetails for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomeDetails(): HasMany
    {
        return $this->hasMany(IncomeDetails::class);
    }
}
