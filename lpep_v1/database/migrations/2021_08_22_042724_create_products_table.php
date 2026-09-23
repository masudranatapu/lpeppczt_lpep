<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->enum('origin', ['supplier', 'customer']);
            $table->string('product_name');
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('unit_id')->index('products_unit_id_foreign');
            $table->unsignedBigInteger('category_id')->nullable()->index('products_category_id_foreign');
            $table->unsignedBigInteger('brand_id')->nullable()->index('products_brand_id_foreign');
            $table->double('alert_quantity', 8, 2)->nullable();
            $table->float('discount_price', 10, 0)->nullable()->default(0);
            $table->double('selling_price', 8, 2);
            $table->float('discount_selling_price', 10, 0)->nullable()->default(0);
            $table->double('other_price', 8, 2)->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('vat_group_id')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users');
            $table->foreignIdFor(User::class, 'updated_by')->nullable()->constrained('users');
            $table->text('product_description')->nullable();
            $table->integer('status');
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
        Schema::dropIfExists('products');
    }
}
