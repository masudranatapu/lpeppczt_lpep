<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtlDiseaseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctl_disease_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cattle_id');
            $table->unsignedBigInteger('disease_history_id');
            $table->timestamps();

            $table->foreign('cattle_id')->references('id')->on('cattle')->onDelete('cascade');
            $table->foreign('disease_history_id')->references('id')->on('disease_histories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctl_disease_histories');
    }
}
