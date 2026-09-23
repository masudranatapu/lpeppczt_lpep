<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandWarehousePurchasePricePrecision extends Migration
{
    public function up()
    {
        Schema::table('lpep_warehouse_purchase_items', function (Blueprint $table) {
            $table->decimal('purchase_price', 18, 6)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('lpep_warehouse_purchase_items', function (Blueprint $table) {
            $table->decimal('purchase_price', 12, 2)->default(0)->change();
        });
    }
}
