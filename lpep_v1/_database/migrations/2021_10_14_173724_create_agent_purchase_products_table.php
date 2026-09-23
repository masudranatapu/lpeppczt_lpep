<?php

use App\Models\AgentPurchase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AgentPurchase::class)->constrained()->cascadeOnDelete();
            $table->double('purchase_price', 16, 2);
            $table->double('quantity', 16, 2);
            $table->double('total_price', 16, 2);
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
        Schema::dropIfExists('agent_purchase_products');
    }
}
