<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellCart extends Model
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
}
