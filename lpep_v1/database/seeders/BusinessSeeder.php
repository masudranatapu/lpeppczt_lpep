<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Business::query()->create([
            'name' => 'LPEP',
            'invoice_name' => 'LPEP',
            'email' => 'admin@gmail.com',
            'mobile' => '01234567890',
            'logo' => "/logo.jpeg",
            'color' => '#2d9a76',
        ]);

        Contact::query()
            ->create([
                'type' => 'customer',
                'name' => 'Guest',
                'email' => "guest@gmail.com",
                'Mobile' => "01234567890",
                'address' => 'Uttara, Dhaka',
                'status' => 1,
            ]);
    }
}
