<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    public function quotationProduct()
    {
        return $this->hasMany(QuotationProduct::class, 'quotation_id');
    }

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
