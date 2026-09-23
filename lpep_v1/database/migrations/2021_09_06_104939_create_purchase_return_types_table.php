<?php

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignIdFor(Business::class)->constrained();
            $table->foreignIdFor(User::class, "created_by")->nullable()->constrained('users');
            $table->foreignIdFor(User::class, "updated_by")->nullable()->constrained('users');
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
        Schema::dropIfExists('purchase_return_types');
    }
}
