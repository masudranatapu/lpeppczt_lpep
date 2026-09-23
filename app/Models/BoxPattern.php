<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxPattern extends Model
{
    use HasFactory;

    public function getQuantityAttribute($value)
    {
        return round($value, 2);
    }
}
