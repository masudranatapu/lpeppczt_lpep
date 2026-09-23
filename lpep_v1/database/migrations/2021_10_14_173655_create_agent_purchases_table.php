<?php

use App\Models\AppCustomer;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'agent_id')->constrained('users');
            $table->foreignIdFor(AppCustomer::class)->constrained();
            $table->date('purchase_date');
            $table->string('invoice_no');
            $table->double('total', 16, 2);
            $table->text('note')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
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
        Schema::dropIfExists('agent_purchases');
    }
}
