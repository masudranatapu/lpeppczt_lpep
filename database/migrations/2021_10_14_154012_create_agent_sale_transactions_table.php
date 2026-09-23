<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSaleTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_sale_id');
            $table->foreign('agent_sale_id', 'ast_agent_sale_id')->references('id')->on('agent_sales')->cascadeOnDelete();
            $table->unsignedBigInteger('agent_customer_transaction_id');
            $table->foreign('agent_customer_transaction_id', 'ast_agent_customer_transaction_id')->references('id')->on('agent_customer_transactions');
            $table->double('amount', 16, 2);
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
        Schema::dropIfExists('agent_sale_transactions');
    }
}
