<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_infos', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number')->nullable();
            $table->foreignId('app_customer_id')->constrained('app_customers')->onDelete('cascade');
            $table->json('memo_no')->nullable();
            $table->date('visit_date')->nullable();
            $table->integer('area_id')->nullable();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('visit_infos');
    }
}
