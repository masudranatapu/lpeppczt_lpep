<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id')->index('contact_payments_contact_id_foreign');
            $table->integer('purchase_id')->nullable();
            $table->integer('sale_id')->nullable();
            $table->integer('sale_return_id')->nullable();
            $table->decimal('paying_amount', 16, 2);
            $table->string('pay_by');
            $table->integer('account_id')->nullable();
            $table->date('paying_date');
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
        Schema::dropIfExists('contact_payments');
    }
}
