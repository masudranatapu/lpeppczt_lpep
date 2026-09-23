<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpepWarehousePurchasesTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->date('purchase_date');
            $table->string('invoice_no')->unique();
            $table->string('attachment')->nullable();
            $table->string('pay_by')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->double('paid_amount', 12, 2)->default(0);
            $table->double('due_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->double('total_amount', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('lpep_warehouses')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_purchases');
    }
}
