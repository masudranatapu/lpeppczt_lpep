<?php

use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchIdToSellCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseProduct::class)->nullable()->constrained();
            $table->string('batch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_product_id');
            $table->dropColumn('batch_id');
        });
    }
}
