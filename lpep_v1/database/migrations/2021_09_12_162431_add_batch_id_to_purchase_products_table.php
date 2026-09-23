<?php

use App\Models\BoxPattern;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchIdToPurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->string('batch_id')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignIdFor(BoxPattern::class)->nullable()->constrained();
            $table->decimal('box_pattern_quantity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('box_pattern_id');
            $table->dropColumn(['batch_id', 'expiry_date', 'box_pattern_quantity']);
        });
    }
}
