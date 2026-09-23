<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedAmountToAgentCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_customer_transactions', function (Blueprint $table) {
            $table->double('used_amount', 16, 2)->default(0)->after('amount');
            $table->double('available_amount', 16, 2)->virtualAs('amount - used_amount')->after('used_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_customer_transactions', function (Blueprint $table) {
            $table->dropColumn(['used_amount', 'available_amount']);
        });
    }
}
