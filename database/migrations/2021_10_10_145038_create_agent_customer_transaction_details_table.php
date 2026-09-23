<?php

use App\Models\AdminAgentTransaction;
use App\Models\AgentCustomerTransaction;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentCustomerTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_customer_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_customer_transaction_id');
            $table->unsignedBigInteger('admin_agent_transaction_id')->nullable();
            $table->foreignIdFor(Purchase::class)->nullable()->constrained();
            $table->foreignIdFor(Sale::class)->nullable()->constrained();
            $table->double('amount', 16, 2);
            $table->timestamps();

            $table->foreign('agent_customer_transaction_id', 'actd_agent_customer_transaction_id_foreign')->references('id')->on('agent_customer_transactions')->onDelete('cascade');
            $table->foreign('admin_agent_transaction_id', 'actd_admin_agent_transaction_id_foreign')->references('id')->on('admin_agent_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_customer_transaction_details');
    }
}
