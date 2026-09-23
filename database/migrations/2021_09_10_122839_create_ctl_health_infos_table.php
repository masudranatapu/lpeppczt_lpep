<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtlHealthInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctl_health_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cattle_id');
            $table->unsignedBigInteger('health_info_id');
            $table->timestamps();

            $table->foreign('cattle_id')->references('id')->on('cattle')->onDelete('cascade');
            $table->foreign('health_info_id')->references('id')->on('health_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctl_health_infos');
    }
}
