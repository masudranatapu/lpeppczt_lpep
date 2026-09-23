<?php

use App\Models\AgentStockTransferDetails;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentStockTransferDetailsIdToSellCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->foreignIdFor(AgentStockTransferDetails::class)->nullable()->constrained();
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
            $table->dropConstrainedForeignId("agent_stock_transfer_details_id");
        });
    }
}
