<?php

namespace App\Models;

use App\Traits\InvoiceNo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Purchase extends Model
{
    use HasFactory, InvoiceNo;

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::class, 'purchase_id');
    }

    public function supplierPayment()
    {
        return $this->hasOne(ContactPayment::class, 'purchase_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseReturn()
    {
        return $this->hasOne(PurchaseReturn::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }


    /**
     * Get all of the products for the Purchase
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,         // Final model
            PurchaseProduct::class, // Intermediate model
            'purchase_id',          // Foreign key on PurchaseProduct table
            'id',                   // Local key on Product table
            'id',                   // Local key on Purchase table
            'product_id'            // Foreign key on PurchaseProduct pointing to Product
        );
    }
}
