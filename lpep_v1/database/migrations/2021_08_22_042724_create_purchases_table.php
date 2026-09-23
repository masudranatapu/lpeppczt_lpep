<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->enum('origin', ['supplier', 'customer']);
            $table->unsignedBigInteger('contact_id')->index('purchases_contact_id_foreign');
            $table->date('purchase_date');
            $table->string('invoice_no')->nullable();
            $table->decimal('other_cost', 16, 2)->nullable();
            $table->decimal('total', 16, 2)->nullable();
            $table->decimal('total_pay', 16, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
