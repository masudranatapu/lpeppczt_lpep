<?php

use App\Models\PurchaseReturn;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseReturnIdToContactPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_payments', function (Blueprint $table) {
            $table->foreignIdFor(PurchaseReturn::class)->nullable()->after('purchase_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId("purchase_return_id");
        });
    }
}
