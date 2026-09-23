<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index('sell_carts_user_id_foreign');
            $table->unsignedBigInteger('product_id')->index('sell_carts_product_id_foreign');
            $table->integer('qty')->default(1);
            $table->double('price', 8, 2);
            $table->double('total_price', 8, 2);
            $table->double('vat', 8, 2)->default(0.00);
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
        Schema::dropIfExists('sell_carts');
    }
}
