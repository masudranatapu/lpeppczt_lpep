<?php

namespace App\Models;

use App\Traits\Exclude;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MAccountEntry extends Model
{
    use HasFactory, Exclude;

    protected $fillable = ['id', 'farm_id', 'account_id', 'quantity', 'amount_per_unit', 'date', 'total_amount', 'created_at', 'updated_at'];

    public function account()
    {
        return $this->belongsTo(MAccount::class, 'account_id');
    }
}
