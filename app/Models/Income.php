<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model
{
    use HasFactory;


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];


    /**
     * Get the incomeType that owns the Income
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function incomeType(): BelongsTo
    {
        return $this->belongsTo(IncomeType::class);
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
            $query->whereYear('income_date', $year);
        }
        if ($month) {
            $query->whereMonth('income_date', $month);
        }

        return $query;
    }


    /**
     * Get all of the details for the Income
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(IncomeDetails::class);
    }
}
