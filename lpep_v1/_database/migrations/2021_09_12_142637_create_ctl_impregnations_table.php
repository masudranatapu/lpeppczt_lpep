<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtlImpregnationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctl_impregnations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('cattle_id');
            $table->unsignedBigInteger('manual_hit_id');
            $table->string('pal_date');
            $table->string('pal_type');
            $table->unsignedBigInteger('pal_breed_id');
            $table->unsignedBigInteger('seed_company_id')->nullable();
            $table->string('seed_percentage')->nullable();
            $table->string('straw_number')->nullable();
            $table->string('worker_info')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('created_by_type');
            $table->string('updated_by_type')->nullable();
            $table->timestamps();

            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('cattle_id')->references('id')->on('cattle');
            $table->foreign('manual_hit_id')->references('id')->on('ctl_manual_hits');
            $table->foreign('pal_breed_id')->references('id')->on('cattle_breeds');
            $table->foreign('seed_company_id')->references('id')->on('seed_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctl_impregnations');
    }
}
