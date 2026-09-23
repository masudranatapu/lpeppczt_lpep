<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index('sales_user_id_foreign');
            $table->unsignedBigInteger('contact_id')->index('sales_contact_id_foreign');
            $table->date('sale_date');
            $table->double('sub_total', 8, 2);
            $table->double('discount_amount', 8, 2);
            $table->double('vat', 8, 2);
            $table->integer('deliverycharge')->nullable();
            $table->string('preorder', 200)->nullable();
            $table->double('total_amount', 8, 2);
            $table->double('paying_amount', 8, 2);
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
        Schema::dropIfExists('sales');
    }
}
