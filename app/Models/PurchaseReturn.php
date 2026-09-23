<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchaseReturnProducts()
    {
        return $this->hasMany(PurchaseReturnProduct::class);
    }

    public function purchaseReturnType()
    {
        return $this->belongsTo(PurchaseReturnType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function contactPayment()
    {
        return $this->hasOne(ContactPayment::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }


    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
