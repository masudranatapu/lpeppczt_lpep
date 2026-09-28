<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lpep_warehouses', function (Blueprint $table) {
            // Daily earning target of the whole Area Office, used by the Area Manager Daily Report.
            $table->decimal('daily_target', 12, 2)->default(5000)->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('lpep_warehouses', function (Blueprint $table) {
            $table->dropColumn('daily_target');
        });
    }
};
