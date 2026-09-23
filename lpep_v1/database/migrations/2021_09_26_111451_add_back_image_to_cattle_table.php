<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackImageToCattleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cattle', function (Blueprint $table) {
            $table->string('back_image_1')->after('right_image_2')->nullable();
            $table->string('back_image_2')->after('back_image_1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cattle', function (Blueprint $table) {
            $table->dropColumn(['back_image_1', 'back_image_2']);
        });
    }
}
