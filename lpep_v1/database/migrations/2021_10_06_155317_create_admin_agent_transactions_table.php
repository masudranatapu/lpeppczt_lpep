<?php

use App\Models\AdminDeposit;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminAgentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_agent_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('invoice_no');
            $table->enum('type', ['Send', 'Receive']);
            $table->foreignIdFor(User::class, 'agent_id')->constrained('users');
            $table->decimal('amount');
            $table->double('loan_amount', 16, 2)->default(0);
            $table->double('available_amount', 16, 2)->nullable()->virtualAs('amount - loan_amount');
            $table->text('note')->nullable();

            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('admin_agent_transactions');
    }
}
