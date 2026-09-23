<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAccountEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_account_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('quantity');
            $table->decimal('amount_per_unit');
            $table->decimal('total_amount');
            $table->string('date');
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('account_id')->references('id')->on('m_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_account_entries');
    }
}
