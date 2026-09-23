<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToAgentPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_purchase_products', function (Blueprint $table) {
            $table->foreignIdFor(Product::class)->after('agent_purchase_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_purchase_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
}
