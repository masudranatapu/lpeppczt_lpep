<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCattleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cattle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            // গরু
            $table->string('tag');
            $table->string('name');
            $table->unsignedBigInteger('cattle_group_id');
            $table->unsignedBigInteger('cattle_breed_id');
            $table->string('birth_date');
            $table->string('weight');
            $table->string('gender');
            $table->string('health_problem');
            // দুধ, প্রজনন, রোগ তথ্য
            $table->string('avg_milk_production')->nullable();
            $table->string('milk_production_status')->nullable();
            $table->string('calf_count')->nullable();
            $table->string('last_calf_birth_date')->nullable();
            $table->string('genetic_percentage')->nullable();
            // ইন্স্যুরেন্স তথ্য
            $table->unsignedBigInteger('insurance_company_id');
            $table->unsignedBigInteger('insurance_type_id');
            $table->string('insurance_no');
            // ছবি
            $table->string('front_image_1')->nullable();
            $table->string('front_image_2')->nullable();
            $table->string('left_image_1')->nullable();
            $table->string('left_image_2')->nullable();
            $table->string('right_image_1')->nullable();
            $table->string('right_image_2')->nullable();
            // log
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('created_by_type');
            $table->string('updated_by_type')->nullable();
            $table->timestamps();

            // foreign
            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('cattle_group_id')->references('id')->on('cattle_groups');
            $table->foreign('cattle_breed_id')->references('id')->on('cattle_breeds');
            $table->foreign('insurance_company_id')->references('id')->on('insurance_companies');
            $table->foreign('insurance_type_id')->references('id')->on('insurance_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cattle');
    }
}
