<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;

class StockTransfer extends Model
{
    use HasFactory, InvoiceNo;

    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(User::class);
    }

     public function details()
    {
        return $this->hasMany(StockTransferDetail::class, 'stock_transfer_id');
    }


     /**
     * Get all of the products for the Purchase
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,         // Final model
            StockTransferDetail::class, // Intermediate model
            'stock_transfer_id',          // Foreign key on PurchaseProduct table
            'id',                   // Local key on Product table
            'id',                   // Local key on Purchase table
            'product_id'            // Foreign key on PurchaseProduct pointing to Product
        );
    }


    public function scopeFilterByDate(Builder $query, $start_date = null, $end_date = null): Builder
    {

        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date,$end_date]);
        }
        return $query;
    }




    public static function filterTransfers($request)
    {
        return self::query()
            ->when($request->type == 'received', fn($q) => $q->where('agent_id', auth()->id()))
            ->when(!is_null($request->agent_id), fn($q) => $q->where('agent_id', $request->agent_id))
            ->when($request->start && $request->end, fn($q) => $q->filterByDate($request->start, $request->end))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($subQuery) use ($request) {
                    $subQuery->where('invoice_no', 'like', "%{$request->search}%")
                        ->orWhere('total_quantity', 'like', "%{$request->search}%")
                        ->orWhereHas('products', function ($q2) use ($request) {
                            $q2->where('product_name', 'like', "%{$request->search}%");
                        });
                });
            });
    }
}
