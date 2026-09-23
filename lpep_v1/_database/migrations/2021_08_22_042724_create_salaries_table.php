<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index('salaries_employee_id_foreign');
            $table->unsignedBigInteger('account_id')->nullable()->index('salaries_account_id_foreign');
            $table->string('pay_by');
            $table->integer('amount');
            $table->date('salary_date');
            $table->string('salary_month');
            $table->integer('salary_year');
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
        Schema::dropIfExists('salaries');
    }
}
