<?php

use App\Models\Contact;
use App\Models\AppCustomer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCustomerIdInAgentCustomerTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_customer_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
            $table->foreignIdFor(AppCustomer::class)->after('type')->constrained();
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
            $table->foreignIdFor(Contact::class)->constrained();
            $table->dropConstrainedForeignId('app_customer_id');
        });
    }
}
