<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtlAbortionInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctl_abortion_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('cattle_id');
            $table->unsignedBigInteger('pregnancy_exam_id');
            $table->string('date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('created_by_type');
            $table->string('updated_by_type')->nullable();
            $table->timestamps();

            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('cattle_id')->references('id')->on('cattle');
            $table->foreign('pregnancy_exam_id')->references('id')->on('ctl_pregnancy_exams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctl_abortion_infos');
    }
}
