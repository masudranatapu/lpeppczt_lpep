<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_types', function (Blueprint $table) {
            $table->id();
            $table->string('income_type');
            $table->string('calculation_type')->nullable(); // 'full_profit', 'half_profit', or 'formula'
            $table->double('value1', 10, 2)->nullable(); // used in 'formula' only
            $table->double('value2', 10, 2)->nullable(); // used in 'formula' only
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
        Schema::dropIfExists('income_types');
    }
}
