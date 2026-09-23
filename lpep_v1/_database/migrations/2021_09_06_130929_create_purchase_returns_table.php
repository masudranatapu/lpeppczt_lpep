<?php

use App\Models\Business;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\PurchaseReturnType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->constrained();
            $table->foreignIdFor(Purchase::class)->constrained();
            $table->foreignIdFor(PurchaseReturnType::class)->constrained();
            $table->foreignIdFor(Contact::class)->constrained();
            $table->double("total");
            $table->double("total_pay")->nullable();
            $table->date("date");
            $table->mediumText('note')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('purchase_returns');
    }
}
