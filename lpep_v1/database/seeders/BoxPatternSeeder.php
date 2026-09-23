<?php

namespace Database\Seeders;

use App\Models\BoxPattern;
use Illuminate\Database\Seeder;

class BoxPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BoxPattern::query()->insert([
            [
                'name' => 'Leaf 1',
                'quantity' => 20
            ],
            [
                'name' => 'Leaf 2',
                'quantity' => 30
            ],
            [
                'name' => 'Leaf 3',
                'quantity' => 40
            ],
            [
                'name' => '1:1',
                'quantity' => 1
            ],
            [
                'name' => '1x15',
                'quantity' => 15
            ],
        ]);
    }
}
