<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_customers', function (Blueprint $table) {
            // Set on beneficiaries created by the Excel import (a copy; the original is never changed).
            $table->unsignedBigInteger('imported_from_id')->nullable()->index()->after('updated_by');
            $table->timestamp('imported_at')->nullable()->after('imported_from_id');
        });
    }

    public function down(): void
    {
        Schema::table('app_customers', function (Blueprint $table) {
            $table->dropIndex(['imported_from_id']);
            $table->dropColumn(['imported_from_id', 'imported_at']);
        });
    }
};
