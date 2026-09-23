<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehousePurchasePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_purchase_id');
            $table->date('payment_date');
            $table->string('pay_by');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->double('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_purchase_id')->references('id')->on('lpep_warehouse_purchases')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_purchase_payments');
    }
}
