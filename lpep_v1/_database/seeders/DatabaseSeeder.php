<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DivisionSeeder::class,
            DistrictSeeder::class,
            UpazilaSeeder::class,
            BoxPatternSeeder::class,
            UserSeeder::class,
            BusinessSeeder::class,
            UnitSeeder::class,
            CattleDataSeeder::class,
            MAccountSeeder::class,
            DevSeeder::class,
        ]);
    }
}
