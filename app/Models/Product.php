<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function agentPurchaseProducts()
    {
        return $this->hasMany(AgentPurchaseProduct::class, 'product_id');
    }

    public function agentStockTransferDetails()
    {
        return $this->hasMany(AgentStockTransferDetails::class, 'product_id');
    }

    public function warehousePurchaseItems()
    {
        return $this->hasMany(WarehousePurchaseItem::class, 'product_id');
    }

    public function warehouseSaleItems()
    {
        return $this->hasMany(WarehouseSaleItem::class, 'product_id');
    }

    public function warehouseStockTransferItems()
    {
        return $this->hasMany(WarehouseStockTransferItem::class, 'product_id');
    }

    public function warehouseSalesmanAssignmentItems()
    {
        return $this->hasMany(WarehouseSalesmanAssignmentItem::class, 'product_id');
    }

    public function warehouseSalesmanReturnItems()
    {
        return $this->hasMany(WarehouseSalesmanReturnItem::class, 'product_id');
    }

    public function scopeWarehouseStock($query, $warehouseId)
    {
        $warehouseId = (int) $warehouseId;

        $sums = [
            'warehousePurchaseItems as warehouse_purchase_qty' => function ($q) use ($warehouseId) {
                $q->whereHas('purchase', function ($purchaseQuery) use ($warehouseId) {
                    $purchaseQuery->where('warehouse_id', $warehouseId);
                });
            },
            'warehouseStockTransferItems as warehouse_transfer_qty' => function ($q) use ($warehouseId) {
                $q->whereHas('transfer', function ($transferQuery) use ($warehouseId) {
                    $transferQuery->where('warehouse_id', $warehouseId);
                });
            },
            'warehouseSalesmanAssignmentItems as warehouse_assigned_qty' => function ($q) use ($warehouseId) {
                $q->whereHas('assignment', function ($assignmentQuery) use ($warehouseId) {
                    $assignmentQuery->where('warehouse_id', $warehouseId);
                });
            },
            'warehouseSaleItems as warehouse_sale_qty' => function ($q) use ($warehouseId) {
                $q->whereHas('sale', function ($saleQuery) use ($warehouseId) {
                    $saleQuery->where('warehouse_id', $warehouseId);
                });
            },
        ];

        if (Schema::hasTable('lpep_warehouse_salesman_return_items')) {
            $sums['warehouseSalesmanReturnItems as warehouse_return_qty'] = function ($q) use ($warehouseId) {
                $q->whereHas('return', function ($returnQuery) use ($warehouseId) {
                    $returnQuery->where('warehouse_id', $warehouseId);
                });
            };
        }

        return $query->withSum($sums, 'quantity');
    }

    public function scopeSalesmanStock($query, $salesmanId)
    {
        $sums = [
            'warehouseSalesmanAssignmentItems as salesman_assigned_qty' => function ($q) use ($salesmanId) {
                $q->whereHas('assignment', function ($assignmentQuery) use ($salesmanId) {
                    $assignmentQuery->where('warehouse_salesman_id', $salesmanId);
                });
            },
            'warehouseSaleItems as salesman_sale_qty' => function ($q) use ($salesmanId) {
                $q->whereHas('sale', function ($saleQuery) use ($salesmanId) {
                    $saleQuery->where('warehouse_salesman_id', $salesmanId);
                });
            },
        ];

        if (Schema::hasTable('lpep_warehouse_salesman_return_items')) {
            $sums['warehouseSalesmanReturnItems as salesman_return_qty'] = function ($q) use ($salesmanId) {
                $q->whereHas('return', function ($returnQuery) use ($salesmanId) {
                    $returnQuery->where('warehouse_salesman_id', $salesmanId);
                });
            };
        }

        return $query->withSum($sums, 'quantity');
    }
}
