<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentSaleCart extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getQtyAttribute($qty)
    {
        return round($qty, 2);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockTransferDetail()
    {
        return $this->belongsTo(StockTransferDetail::class);
    }
}
