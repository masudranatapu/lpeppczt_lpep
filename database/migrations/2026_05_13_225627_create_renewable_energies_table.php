<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewableEnergiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renewable_energies', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['biogas', 'solar']);
            $table->foreignId('area_id')->comment('Selected field area (Staff)')->constrained('users');
            $table->foreignId('agent_id')->nullable()->comment('Selected agent')->constrained('users');
            $table->date('visit_date');
            $table->string('client_name');
            $table->string('client_number')->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas');
            $table->foreignId('union_id')->nullable()->constrained('unions');
            $table->unsignedTinyInteger('ward_number')->nullable();
            $table->string('village')->nullable();
            $table->text('livestock_details')->nullable();
            $table->decimal('plant_size', 10, 2)->nullable();
            $table->string('plant_size_unit')->default('m³');
            $table->date('plant_start_date')->nullable();
            $table->date('plant_end_date')->nullable();
            $table->string('po_name')->nullable();
            $table->text('contribution_condition')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('renewable_energies');
    }
}
