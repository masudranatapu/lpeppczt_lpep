<?php

use App\Models\AgentPurchaseProduct;
use App\Models\AgentStockTransfer;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AgentStockTransfer::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(AgentPurchaseProduct::class)->constrained();
            $table->double("quantity", 16, 2);
            $table->double("sold_quantity", 16, 2)->default(0.00);
            $table->double("available_quantity", 16, 2)->nullable()->virtualAs("quantity - sold_quantity");
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
        Schema::dropIfExists('agent_stock_transfer_details');
    }
}
