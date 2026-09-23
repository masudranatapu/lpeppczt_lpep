<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLpepWarehouseSalePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('lpep_warehouse_sale_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_sale_id');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('warehouse_sale_id', 'wh_sale_payments_sale_fk')
                ->references('id')->on('lpep_warehouse_sales')->onDelete('cascade');
            $table->index(['payment_date', 'warehouse_sale_id'], 'wh_sale_payments_date_sale_idx');
        });

        // Preserve reporting for sales created before payment history existed.
        DB::table('lpep_warehouse_sale_payments')->insertUsing(
            ['warehouse_sale_id', 'payment_date', 'amount', 'payment_method', 'notes', 'created_by', 'created_at', 'updated_at'],
            DB::table('lpep_warehouse_sales')
                ->where('paid_amount', '>', 0)
                ->selectRaw("id, sale_date, paid_amount, CASE WHEN payment_method = 'Credit' THEN 'Cash' ELSE payment_method END, 'Opening payment record', created_by, created_at, updated_at")
        );
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_sale_payments');
    }
}
