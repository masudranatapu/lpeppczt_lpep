<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpepWarehouseSalesmanReturnsTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_salesman_returns', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('warehouse_salesman_id');
            $table->dateTime('return_date_time');
            $table->text('notes')->nullable();
            $table->decimal('total_quantity', 14, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id', 'wh_returns_warehouse_fk')->references('id')->on('lpep_warehouses')->onDelete('restrict');
            $table->foreign('warehouse_salesman_id', 'wh_returns_salesman_fk')->references('id')->on('lpep_warehouse_salesmen')->onDelete('restrict');
            $table->index(['warehouse_id', 'return_date_time'], 'wh_returns_warehouse_date_idx');
            $table->index(['warehouse_salesman_id', 'return_date_time'], 'wh_returns_salesman_date_idx');
        });

        Schema::create('lpep_warehouse_salesman_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_salesman_return_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 14, 2);
            $table->timestamps();

            $table->foreign('warehouse_salesman_return_id', 'wh_return_items_return_fk')->references('id')->on('lpep_warehouse_salesman_returns')->onDelete('cascade');
            $table->foreign('product_id', 'wh_return_items_product_fk')->references('id')->on('products')->onDelete('restrict');
            $table->index(['product_id', 'warehouse_salesman_return_id'], 'wh_return_items_product_return_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_salesman_return_items');
        Schema::dropIfExists('lpep_warehouse_salesman_returns');
    }
}
