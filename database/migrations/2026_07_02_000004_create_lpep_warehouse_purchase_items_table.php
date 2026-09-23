<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpepWarehousePurchaseItemsTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_purchase_id');
            $table->unsignedBigInteger('product_id');
            $table->double('quantity', 12, 2);
            $table->double('purchase_price', 12, 2)->default(0);
            $table->double('sale_price', 12, 2)->default(0);
            $table->double('total', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('warehouse_purchase_id')->references('id')->on('lpep_warehouse_purchases')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_purchase_items');
    }
}
