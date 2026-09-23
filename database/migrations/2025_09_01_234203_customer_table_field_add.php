<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerTableFieldAdd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_customers', function (Blueprint $table) {
           $table->integer('cow')->nullable()->after('group_number');
            $table->integer('bull')->nullable()->after('cow');
            $table->integer('bakna')->nullable()->after('bull');
            $table->integer('goat')->nullable()->after('bakna');
            $table->integer('khasi')->nullable()->after('goat');
            $table->string('membership')->nullable()->after('khasi');
            $table->string('deworming')->nullable()->after('membership');
            $table->string('bringing')->nullable()->after('deworming');
            $table->string('fattening')->nullable()->after('bringing');
            $table->string('treatment')->nullable()->after('fattening');
            $table->string('ai')->nullable()->after('treatment');
            $table->string('medicine')->nullable()->after('ai');
            $table->string('feed')->nullable()->after('medicine');
            $table->date('date')->nullable()->after('feed');
            $table->integer('beneficiary_number')->nullable()->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_customers', function (Blueprint $table) {
            $table->dropColumn(['cow', 'bull', 'bakna', 'goat', 'khasi', 'membership', 'deworming', 'bringing', 'fattening', 'treatment', 'ai', 'medicine', 'feed', 'date', 'beneficiary_number']);
        });
    }
}
