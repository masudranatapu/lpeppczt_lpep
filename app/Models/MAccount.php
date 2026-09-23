<?php

namespace App\Models;

use App\Traits\UserLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MAccount extends Model
{
    use HasFactory, UserLog;

    protected $fillable = ['id', 'type', 'name', 'unit','total_amount','quantity','date' ,'created_by', 'created_at', 'updated_by', 'updated_at'];
}
