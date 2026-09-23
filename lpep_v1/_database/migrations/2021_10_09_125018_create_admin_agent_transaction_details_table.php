<?php

use App\Models\AdminDeposit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminAgentTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_agent_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("admin_agent_transaction_id");
            $table->foreignIdFor(AdminDeposit::class)->constrained();
            $table->double('amount', 16, 2);
            $table->foreign('admin_agent_transaction_id', 'admin_agent_transaction_id_foreign')->references('id')->on('admin_agent_transactions')->onDelete('cascade');
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
        Schema::dropIfExists('admin_agent_transaction_details');
    }
}
