<?php

namespace Database\Seeders;

use App\Models\MAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MAccount::query()->insert([
            [
                "type" => "Income",
                "name" => "দুধ বিক্রয়",
                "unit" => "লিটার",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Income",
                "name" => "গরু বিক্রয়",
                "unit" => "টি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Income",
                "name" => "গোবর বিক্রয়",
                "unit" => "মণ",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "খড় ক্রয়",
                "unit" => "কেজি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ঘাস ক্রয়",
                "unit" => "আটি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "লালি ক্রয়",
                "unit" => "আটি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "খৈল ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "লবণ ক্রয়",
                "unit" => "কেজি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "বিট লবণ ক্রয়",
                "unit" => "কেজি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ইউরিয়া সার ক্রয়",
                "unit" => "কেজি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ঔষধ ক্রয়",
                "unit" => "পিস",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ভুষি ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "শ্রমিক বিল",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "বিদ্যুৎ বিল",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "পরিবহন বিল",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "বৈদ্যুতিক যন্ত্রপাতি ক্রয়",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "বীজ ভরণ",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "গমের ভুষি ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "মশুরির ভুষি ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "কুড়া ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "সয়াবিন খৈল ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "সয়াবিন খৈল ক্রয়",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ডিসিপি প্লাস",
                "unit" => "কেজি",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ইন্টারনেট বিল",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "অন্যান্য",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "খামার মেরামত খরচ",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "গরু ক্রয়",
                "unit" => "টাকা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
            [
                "type" => "Expense",
                "name" => "ভূট্রা ভাঙ্গা",
                "unit" => "বস্তা",
                "created_by" => 1,
                "created_at" => Carbon::now()
            ],
        ]);
    }
}
