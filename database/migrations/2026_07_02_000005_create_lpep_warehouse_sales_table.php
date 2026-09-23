<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpepWarehouseSalesTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('warehouse_salesman_id')->nullable();
            $table->date('sale_date');
            $table->string('invoice_no')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('due_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->double('total_amount', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('lpep_warehouses')->onDelete('cascade');
            $table->foreign('warehouse_salesman_id')->references('id')->on('lpep_warehouse_salesmen')->nullOnDelete();
            $table->index(['warehouse_salesman_id', 'sale_date'], 'warehouse_sales_salesman_date_index');
            $table->index(['warehouse_id', 'sale_date'], 'warehouse_sales_warehouse_date_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_sales');
    }
}
