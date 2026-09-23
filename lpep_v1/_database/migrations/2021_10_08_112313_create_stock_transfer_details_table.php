<?php

use App\Models\Product;
use App\Models\StockTransfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StockTransfer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained();
            $table->unsignedBigInteger('purchase_product_id');
            $table->foreign('purchase_product_id', 'std_purchase_product_id')->references('id')->on('purchase_products');
            $table->double('quantity', 16, 2);
            $table->double('sold_quantity', 16, 2)->default(0);
            $table->double('available_quantity', 16, 2)->virtualAs('quantity - sold_quantity');
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
        Schema::dropIfExists('stock_transfer_details');
    }
}
