<?php

use App\Models\Product;
use App\Models\PurchaseReturn;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("purchase_products", function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement()->change();
        });
        Schema::create('purchase_return_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PurchaseReturn::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->unsignedBigInteger("purchase_product_id");
            $table->unsignedBigInteger("product_model_id");
            $table->double("quantity");
            $table->double("price");
            $table->double("total");
            $table->timestamps();
            $table->foreign('purchase_product_id')->references('id')->on('purchase_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_return_products');
    }
}
