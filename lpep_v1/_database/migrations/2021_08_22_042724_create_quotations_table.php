<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index('quotations_user_id_foreign');
            $table->unsignedBigInteger('contact_id')->index('quotations_contact_id_foreign');
            $table->date('quotation_date');
            $table->double('sub_total', 8, 2)->nullable();
            $table->double('discount_amount', 8, 2)->nullable();
            $table->double('vat', 8, 2)->nullable();
            $table->double('total_amount', 8, 2);
            $table->double('paying_amount', 8, 2)->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
