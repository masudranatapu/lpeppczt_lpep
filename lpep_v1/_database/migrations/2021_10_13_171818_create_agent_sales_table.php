<?php

use App\Models\AppCustomer;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->foreignIdFor(User::class, 'agent_id')->constrained('users');
            $table->foreignIdFor(AppCustomer::class)->constrained();
            $table->date('sale_date');
            $table->double('sub_total', 16, 2);
            $table->double('discount_amount', 16, 2);
            $table->double('vat', 16, 2);
            $table->double('deliverycharge', 16, 2)->nullable();
            $table->double('total_amount', 16, 2);
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
        Schema::dropIfExists('agent_sales');
    }
}
