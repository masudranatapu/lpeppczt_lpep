<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBioEnergiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio_energies', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date')->nullable();
            $table->string('client_name');
            $table->string('client_number')->nullable();
            $table->string('district');
            $table->string('upazila');
            $table->string('union');
            $table->string('livestock details');
            $table->string('size');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('organizer_name');
            $table->text('condition');
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
        Schema::dropIfExists('bio_energies');
    }
}
