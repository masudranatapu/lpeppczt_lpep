<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClfBirthProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clf_birth_problems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calf_id');
            $table->unsignedBigInteger('calf_birth_problem_id');
            $table->timestamps();

            $table->foreign('calf_id')->references('id')->on('calves')->onDelete('cascade');
            $table->foreign('calf_birth_problem_id')->references('id')->on('calf_birth_problems');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clf_birth_problems');
    }
}
