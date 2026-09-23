<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpepWarehouseSaleItemsTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_sale_id');
            $table->unsignedBigInteger('product_id');
            $table->double('quantity', 12, 2);
            $table->double('sale_price', 12, 2)->default(0);
            $table->double('total', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('warehouse_sale_id')->references('id')->on('lpep_warehouse_sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_sale_items');
    }
}
