<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQtyInSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_products', function (Blueprint $table) {
            $table->decimal('qty')->change();
        });
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->decimal('qty')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_products', function (Blueprint $table) {
            $table->integer('qty')->change();
        });
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->integer('qty')->change();
        });
    }
}
