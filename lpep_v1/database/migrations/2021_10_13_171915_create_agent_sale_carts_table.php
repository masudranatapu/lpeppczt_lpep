<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSaleCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sale_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'agent_id')->constrained('users');
            $table->foreignIdFor(Product::class)->constrained();
            $table->unsignedBigInteger('stock_transfer_detail_id');
            $table->foreign('stock_transfer_detail_id', 'asc_stock_transfer_detail_id')->on('stock_transfer_details')->references('id');
            $table->double('qty', 16, 2);
            $table->double('price', 16, 2);
            $table->double('total_price', 16, 2);
            $table->double('vat', 16, 2);
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
        Schema::dropIfExists('agent_sale_carts');
    }
}
