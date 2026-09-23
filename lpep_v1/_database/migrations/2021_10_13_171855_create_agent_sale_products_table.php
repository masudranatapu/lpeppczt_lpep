<?php

use App\Models\AgentSale;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSaleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AgentSale::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('stock_transfer_detail_id');
            $table->foreign('stock_transfer_detail_id', 'asp_stock_transfer_detail_id')->on('stock_transfer_details')->references('id');
            $table->foreignIdFor(Product::class)->constrained();
            $table->double('qty', 16, 2);
            $table->double('price', 16, 2);
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
        Schema::dropIfExists('agent_sale_products');
    }
}
