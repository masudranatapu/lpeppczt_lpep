<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function vatGroup()
    {
        return $this->belongsTo(VatGroup::class, 'vat_group_id');
    }

    public function purchaseProduct()
    {
        return $this->hasMany(PurchaseProduct::class, 'product_id');
    }

    public function purchaseReturnProduct()
    {
        return $this->hasMany(PurchaseReturnProduct::class, 'product_id');
    }

    public function saleProduct()
    {
        return $this->hasMany(SaleProduct::class, 'product_id');
    }

    public function saleReturnProduct()
    {
        return $this->hasMany(ReturnProduct::class, 'product_id');
    }

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class, 'product_id');
    }

    /** @deprecated use saleReturnProduct instead */
    public function returnProduct()
    {
        return $this->hasMany(ReturnProduct::class, 'product_id');
    }

    public function purchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::class, 'product_id');
    }

    public function purchaseReturnProducts()
    {
        return $this->hasMany(PurchaseReturnProduct::class, 'product_id');
    }

    public function saleReturnProducts()
    {
        return $this->hasMany(ReturnProduct::class, 'product_id');
    }

    public function models()
    {
        return $this->hasMany(ProductModel::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockTransferDetails()
    {
        return $this->hasMany(StockTransferDetail::class);
    }

    public function agentSaleProducts()
    {
        return $this->hasMany(AgentSaleProduct::class);
    }

    public function scopeWithStockProperties($q, $agent_id = null)
    {
        if (isRole(ROLE_AGENT) || $agent_id) {
            $q->withCount([
                'stockTransferDetails as transferred_in_qty' => fn($q) => $q->whereRelation('transfer', 'agent_id', $agent_id ?? auth()->id())->select(DB::raw('sum(quantity)')),
                'agentSaleProducts as sale_qty' => fn($q) => $q->whereRelation('sale', 'agent_id', $agent_id ?? auth()->id())->select(DB::raw('sum(qty)')),
            ]);
        } else {
            $q->withCount([
                'purchaseProduct as purchase_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'purchaseReturnProduct as purchase_return_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'saleProducts as sale_qty' => fn($q) => $q->select(DB::raw('sum(qty)')),
                'saleReturnProducts as sale_return_qty' => fn($q) => $q->select(DB::raw('sum(qty)')),
                'stockTransferDetails as transferred_out_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
                'agentStockTransferDetails as agent_transferred_qty' => fn($q) => $q->select(DB::raw('sum(quantity)')),
            ]);
        }
    }

    public function agentPurchaseProducts()
    {
        return $this->hasMany(AgentPurchaseProduct::class, 'product_id');
    }

    public function agentStockTransferDetails()
    {
        return $this->hasMany(AgentStockTransferDetails::class, 'product_id');
    }
}
