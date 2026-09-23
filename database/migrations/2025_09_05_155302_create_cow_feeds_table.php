<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCowFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cow_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('volume')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->integer('qty')->nullable();
            $table->string('previus_stock')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->cascadeOnDelete();
            $table->foreignId('create_by')->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('cow_feeds');
    }
}
