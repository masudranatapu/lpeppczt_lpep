<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysInAgentCustomerTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_customer_transaction_details', function (Blueprint $table) {
            if (Schema::hasColumn('agent_customer_transaction_details', 'sale_id')) {
                $table->dropConstrainedForeignId('sale_id');
                $table->dropForeign('agent_customer_transaction_details_purchase_id_foreign');
            }
            $table->foreign('purchase_id', 'actd_purchase_id')->references('id')->on('agent_purchases')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_customer_transaction_details', function (Blueprint $table) {
            $table->dropForeign('actd_purchase_id');
        });
    }
}
