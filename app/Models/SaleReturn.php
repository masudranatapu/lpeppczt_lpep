<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function returnProducts()
    {
        return $this->hasMany(ReturnProduct::class, 'sale_return_id');
    }

    public function customerPayment()
    {
        return $this->hasMany(ContactPayment::class, 'contact_id');
    }
}
