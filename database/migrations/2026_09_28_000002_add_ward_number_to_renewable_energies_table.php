<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWardNumberToRenewableEnergiesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('renewable_energies', 'ward_number')) {
            Schema::table('renewable_energies', function (Blueprint $table) {
                $table->unsignedTinyInteger('ward_number')->nullable()->after('union_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('renewable_energies', 'ward_number')) {
            Schema::table('renewable_energies', function (Blueprint $table) {
                $table->dropColumn('ward_number');
            });
        }
    }
}
