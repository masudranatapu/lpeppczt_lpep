<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::query()->insert([
            [
                'actual_name' => 'Piece',
                'short_name' => 'pc',
                'is_decimal' => false,
                'created_by' => 1
            ],
            [
                'actual_name' => 'Milliliter',
                'short_name' => 'ml',
                'is_decimal' => false,
                'created_by' => 1
            ],
            [
                'actual_name' => 'Milligram',
                'short_name' => 'mg',
                'is_decimal' => false,
                'created_by' => 1
            ],
            [
                'actual_name' => 'Kilogram',
                'short_name' => 'kg',
                'is_decimal' => true,
                'created_by' => 1
            ],
        ]);
    }
}
