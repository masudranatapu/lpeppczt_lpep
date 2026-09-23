<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtlDiseaseInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctl_disease_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('cattle_id');
            $table->unsignedBigInteger('cattle_disease_id');
            $table->string('date');
            // log
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('created_by_type');
            $table->string('updated_by_type')->nullable();
            $table->timestamps();

            // foreign
            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('cattle_id')->references('id')->on('cattle');
            $table->foreign('cattle_disease_id')->references('id')->on('cattle_diseases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctl_disease_infos');
    }
}
