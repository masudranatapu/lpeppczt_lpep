<?php

// use App\Models\ProductModel;
// use App\Models\SellCart;
// use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellCartModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('sell_cart_models', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
        //     $table->foreignIdFor(SellCart::class)->constrained()->onDelete('cascade');
        //     $table->foreignIdFor(ProductModel::class)->constrained()->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('sell_cart_models');
    }
}
