<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSale extends Model
{
    use HasFactory, InvoiceNo;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(AppCustomer::class, 'app_customer_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function saleProducts()
    {
        return $this->hasMany(AgentSaleProduct::class, 'agent_sale_id');
    }

    public function saleTransactions()
    {
        return $this->hasMany(AgentSaleTransaction::class, 'agent_sale_id');
    }



    /**
     * Scope a query to filter incomes by year and month.
     *
     * @param Builder $query
     * @param int|null $year
     * @param int|null $month
     * @return Builder
     */
    public function scopeFilterByYearAndMonth(Builder $query, $year = null, $month = null): Builder
    {
        if ($year) {
            $query->whereYear('sale_date', $year);
        }
        if ($month) {
            $query->whereMonth('sale_date', $month);
        }

        return $query;
    }


    public function scopeFilterByDate(Builder $query, $start_date = null, $end_date = null): Builder
    {

        if ($start_date && $end_date) {
            $query->whereBetween('sale_date', [$start_date,$end_date]);
        }

        return $query;
    }







}
