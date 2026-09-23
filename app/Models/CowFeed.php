<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CowFeed extends Model
{
    protected $fillable = [
        'item_name','volume','unit_price','qty','previus_stock',
        'purchase_date','end_date','farm_id','create_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'end_date'      => 'date',
        'unit_price'    => 'decimal:2',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }
}
