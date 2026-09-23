<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseInventoryFlowTables extends Migration
{
    public function up()
    {
        Schema::table('lpep_warehouse_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->change();
        });

        Schema::create('lpep_warehouse_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('warehouse_id');
            $table->date('transfer_date');
            $table->text('notes')->nullable();
            $table->decimal('total_quantity', 14, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('warehouse_id')->references('id')->on('lpep_warehouses')->onDelete('restrict');
        });

        Schema::create('lpep_warehouse_stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_stock_transfer_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 14, 2);
            $table->timestamps();
            $table->foreign('warehouse_stock_transfer_id', 'wh_transfer_items_transfer_fk')->references('id')->on('lpep_warehouse_stock_transfers')->onDelete('cascade');
            $table->foreign('product_id', 'wh_transfer_items_product_fk')->references('id')->on('products')->onDelete('restrict');
            $table->index(['product_id', 'warehouse_stock_transfer_id'], 'wh_transfer_items_product_transfer_idx');
        });

        Schema::create('lpep_warehouse_salesman_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('warehouse_salesman_id');
            $table->date('assignment_date');
            $table->text('notes')->nullable();
            $table->decimal('total_quantity', 14, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('warehouse_id', 'wh_assignments_warehouse_fk')->references('id')->on('lpep_warehouses')->onDelete('restrict');
            $table->foreign('warehouse_salesman_id', 'wh_assignments_salesman_fk')->references('id')->on('lpep_warehouse_salesmen')->onDelete('restrict');
        });

        Schema::create('lpep_warehouse_salesman_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_salesman_assignment_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 14, 2);
            $table->timestamps();
            $table->foreign('warehouse_salesman_assignment_id', 'wh_assignment_items_assignment_fk')->references('id')->on('lpep_warehouse_salesman_assignments')->onDelete('cascade');
            $table->foreign('product_id', 'wh_assignment_items_product_fk')->references('id')->on('products')->onDelete('restrict');
            $table->index(['product_id', 'warehouse_salesman_assignment_id'], 'wh_assignment_items_product_assignment_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_salesman_assignment_items');
        Schema::dropIfExists('lpep_warehouse_salesman_assignments');
        Schema::dropIfExists('lpep_warehouse_stock_transfer_items');
        Schema::dropIfExists('lpep_warehouse_stock_transfers');

        Schema::table('lpep_warehouse_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable(false)->change();
        });
    }
}
