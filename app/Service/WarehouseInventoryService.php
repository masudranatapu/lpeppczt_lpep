<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WarehouseInventoryService
{
    public function balance(float $stockIn, float $stockOut): float
    {
        return round($stockIn - $stockOut, 2);
    }

    public function lockAdminProduct(int $productId): void
    {
        DB::table('lpep_warehouse_purchase_items')
            ->join('lpep_warehouse_purchases', 'lpep_warehouse_purchases.id', '=', 'lpep_warehouse_purchase_items.warehouse_purchase_id')
            ->whereNull('lpep_warehouse_purchases.warehouse_id')
            ->where('lpep_warehouse_purchase_items.product_id', $productId)
            ->select('lpep_warehouse_purchase_items.id')
            ->lockForUpdate()->get();
        DB::table('lpep_warehouse_stock_transfer_items')->where('product_id', $productId)->lockForUpdate()->get();
    }

    public function lockWarehouseProduct(int $warehouseId, int $productId): void
    {
        // One stable parent-row lock serializes assignments and sales for a warehouse.
        DB::table('lpep_warehouses')->where('id', $warehouseId)->lockForUpdate()->first();
        DB::table('lpep_warehouse_stock_transfers')->where('warehouse_id', $warehouseId)->lockForUpdate()->get();
        DB::table('lpep_warehouse_salesman_assignments')->where('warehouse_id', $warehouseId)->lockForUpdate()->get();
        if (Schema::hasTable('lpep_warehouse_salesman_returns')) {
            DB::table('lpep_warehouse_salesman_returns')->where('warehouse_id', $warehouseId)->lockForUpdate()->get();
        }
        DB::table('lpep_warehouse_sales')->where('warehouse_id', $warehouseId)->lockForUpdate()->get();
        DB::table('lpep_warehouse_purchase_items')
            ->join('lpep_warehouse_purchases', 'lpep_warehouse_purchases.id', '=', 'lpep_warehouse_purchase_items.warehouse_purchase_id')
            ->where('lpep_warehouse_purchases.warehouse_id', $warehouseId)
            ->where('lpep_warehouse_purchase_items.product_id', $productId)
            ->select('lpep_warehouse_purchase_items.id')->lockForUpdate()->get();
    }

    public function adminPurchased(int $productId): float
    {
        return (float) DB::table('lpep_warehouse_purchase_items as i')
            ->join('lpep_warehouse_purchases as p', 'p.id', '=', 'i.warehouse_purchase_id')
            ->whereNull('p.warehouse_id')->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function adminTransferred(int $productId): float
    {
        return (float) DB::table('lpep_warehouse_stock_transfer_items')->where('product_id', $productId)->sum('quantity');
    }

    public function adminAvailable(int $productId): float
    {
        return $this->balance($this->adminPurchased($productId), $this->adminTransferred($productId));
    }

    public function warehouseReceived(int $warehouseId, int $productId): float
    {
        $transferred = DB::table('lpep_warehouse_stock_transfer_items as i')
            ->join('lpep_warehouse_stock_transfers as t', 't.id', '=', 'i.warehouse_stock_transfer_id')
            ->where('t.warehouse_id', $warehouseId)->where('i.product_id', $productId)->sum('i.quantity');
        // Purchases made before the central-stock workflow are treated as already received.
        $legacy = DB::table('lpep_warehouse_purchase_items as i')
            ->join('lpep_warehouse_purchases as p', 'p.id', '=', 'i.warehouse_purchase_id')
            ->where('p.warehouse_id', $warehouseId)->where('i.product_id', $productId)->sum('i.quantity');
        return round((float) $transferred + (float) $legacy, 2);
    }

    public function warehouseAssigned(int $warehouseId, int $productId): float
    {
        return (float) DB::table('lpep_warehouse_salesman_assignment_items as i')
            ->join('lpep_warehouse_salesman_assignments as a', 'a.id', '=', 'i.warehouse_salesman_assignment_id')
            ->where('a.warehouse_id', $warehouseId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function warehouseSalesmanReturned(int $warehouseId, int $productId): float
    {
        if (!Schema::hasTable('lpep_warehouse_salesman_return_items')) {
            return 0.0;
        }

        return (float) DB::table('lpep_warehouse_salesman_return_items as i')
            ->join('lpep_warehouse_salesman_returns as r', 'r.id', '=', 'i.warehouse_salesman_return_id')
            ->where('r.warehouse_id', $warehouseId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function warehouseSold(int $warehouseId, int $productId): float
    {
        return (float) DB::table('lpep_warehouse_sale_items as i')
            ->join('lpep_warehouse_sales as s', 's.id', '=', 'i.warehouse_sale_id')
            ->where('s.warehouse_id', $warehouseId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function warehouseDirectSold(int $warehouseId, int $productId): float
    {
        return (float) DB::table('lpep_warehouse_sale_items as i')
            ->join('lpep_warehouse_sales as s', 's.id', '=', 'i.warehouse_sale_id')
            ->where('s.warehouse_id', $warehouseId)->whereNull('s.warehouse_salesman_id')
            ->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function warehouseAvailable(int $warehouseId, int $productId): float
    {
        return $this->balance($this->warehouseReceived($warehouseId, $productId), $this->warehouseSold($warehouseId, $productId));
    }

    public function warehouseUnassignedAvailable(int $warehouseId, int $productId): float
    {
        return $this->balance(
            $this->warehouseReceived($warehouseId, $productId),
            $this->warehouseAssigned($warehouseId, $productId)
                - $this->warehouseSalesmanReturned($warehouseId, $productId)
                + $this->warehouseDirectSold($warehouseId, $productId)
        );
    }

    public function salesmanAssigned(int $salesmanId, int $productId): float
    {
        return (float) DB::table('lpep_warehouse_salesman_assignment_items as i')
            ->join('lpep_warehouse_salesman_assignments as a', 'a.id', '=', 'i.warehouse_salesman_assignment_id')
            ->where('a.warehouse_salesman_id', $salesmanId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function salesmanSold(int $salesmanId, int $productId): float
    {
        return (float) DB::table('lpep_warehouse_sale_items as i')
            ->join('lpep_warehouse_sales as s', 's.id', '=', 'i.warehouse_sale_id')
            ->where('s.warehouse_salesman_id', $salesmanId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function salesmanReturned(int $salesmanId, int $productId): float
    {
        if (!Schema::hasTable('lpep_warehouse_salesman_return_items')) {
            return 0.0;
        }

        return (float) DB::table('lpep_warehouse_salesman_return_items as i')
            ->join('lpep_warehouse_salesman_returns as r', 'r.id', '=', 'i.warehouse_salesman_return_id')
            ->where('r.warehouse_salesman_id', $salesmanId)->where('i.product_id', $productId)->sum('i.quantity');
    }

    public function salesmanAvailable(int $salesmanId, int $productId): float
    {
        return $this->balance(
            $this->salesmanAssigned($salesmanId, $productId),
            $this->salesmanSold($salesmanId, $productId) + $this->salesmanReturned($salesmanId, $productId)
        );
    }
}
