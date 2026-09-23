<?php

namespace App\Models;

use App\Traits\UserLog;
use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminDeposit extends Model
{
    use HasFactory, InvoiceNo, UserLog;

    protected $guarded = [];
}
