<?php

namespace Tests\Unit;

use App\Service\WarehouseInventoryService;
use PHPUnit\Framework\TestCase;

class WarehouseInventoryBalanceTest extends TestCase
{
    public function test_example_inventory_chain_balances_at_every_level(): void
    {
        $inventory = new WarehouseInventoryService();

        $this->assertSame(0.0, $inventory->balance(1200, 500 + 700));
        $this->assertSame(500.0, $inventory->balance(500, 0));
        $this->assertSame(700.0, $inventory->balance(700, 0));
        $this->assertSame(375.0, $inventory->balance(500, 125));
    }

    public function test_decimal_quantities_are_rounded_consistently(): void
    {
        $inventory = new WarehouseInventoryService();

        $this->assertSame(0.33, $inventory->balance(1.00, 0.67));
    }

    public function test_salesman_return_balance_reduces_salesman_and_reopens_warehouse_stock(): void
    {
        $inventory = new WarehouseInventoryService();

        $assigned = 10.0;
        $sold = 3.0;
        $returned = 2.0;
        $warehouseReceived = 20.0;
        $directSold = 4.0;

        $this->assertSame(5.0, $inventory->balance($assigned, $sold + $returned));
        $this->assertSame(8.0, $inventory->balance($warehouseReceived, $assigned - $returned + $directSold));
    }
}
