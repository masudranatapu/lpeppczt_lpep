<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->index('purchase_products_purchase_id_foreign');
            $table->unsignedBigInteger('product_id')->index('purchase_products_product_id_foreign');
            $table->decimal('purchase_price', 16, 2);
            $table->decimal('quantity', 16, 2);
            $table->decimal('total_price', 16, 2);
            $table->decimal('subtotal_price', 16, 2)->nullable();
            $table->decimal('other_cost', 16, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_products');
    }
}
