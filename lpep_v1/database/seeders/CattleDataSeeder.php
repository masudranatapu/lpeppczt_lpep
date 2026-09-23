<?php

namespace Database\Seeders;

use App\Models\CattleBreed;
use App\Models\CattleDisease;
use App\Models\CattleGroup;
use App\Models\CattleVaccine;
use App\Models\HealthInfo;
use App\Models\DiseaseHistory;
use App\Models\InsuranceCompany;
use App\Models\InsuranceType;
use App\Models\SeedCompany;
use Illuminate\Database\Seeder;

class CattleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->diseaseHistory();
        $this->healthInfo();
        $this->insurance();
        $this->diseaseAndVaccine();
        $this->cattleGroups();
        $this->cattleBreeds();
        $this->seedCompanies();
    }

    private function diseaseHistory()
    {
        DiseaseHistory::query()->insert([
            [
                'name' => 'অকাল গর্ভপাত',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'গর্ভাবস্থায় জরায়ু বের হয়ে আসা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function healthInfo()
    {
        HealthInfo::query()->insert([
            [
                'name' => 'পুষ্টিহীনতা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'হরমোন জনিত সমস্যা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'জন্মগত সমস্যা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'ব্যবস্থাপনা সংক্রান্ত সমস্যা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'সাবক্লিনিকাল ওলান পাকা রোগ',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'গাভী বার বার হিটে আসে',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'জরায়ু বের হয়ে আসছে',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'দুধ আসে না',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function insurance()
    {
        InsuranceCompany::query()->insert([
            [
                'name' => 'ফিনিক্স',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'গ্রীন ডেল্টা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'সূর্যমূখী',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'পপুলার',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);

        InsuranceType::query()->insert([
            [
                'name' => 'জীবন বীমা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'আজীবনের জন্য পঙ্গু / অক্ষম',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'চুরি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function diseaseAndVaccine()
    {
        CattleDisease::query()->insert([
            [
                'id' => 1,
                'name' => 'তড়কা/Anthrax',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'ক্ষুরা রোগ/FMD',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'বাদলা/BQ',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'গলা ফোলা/HS',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'name' => 'জলাতঙ্ক/Rabies',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 6,
                'name' => 'কৃমিনাশক',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 7,
                'name' => 'লাম্পি স্কিন ডিজিজ',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);

        CattleVaccine::query()->insert([
            [
                'disease_id' => 1,
                'name' => 'Anthrax Vaccine',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 1,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 2,
                'name' => 'Aftovaxpur',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 2,
                'name' => 'Arriah FMD',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 2,
                'name' => 'Bangla FMD',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 2,
                'name' => 'Khuravax FMD',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 2,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 3,
                'name' => 'BQ Vaccine',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 3,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 4,
                'name' => 'HS Vaccine',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 4,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 5,
                'name' => 'Rabies Live Attenuated Vaccine',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 5,
                'name' => 'Rabisin',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 5,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Amectin Plus (Ivermectin + Clorsulon)',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Antiworm',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Benazol',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Endex',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Fasinex',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'LT-Vet',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Nitronix',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Oxyclozanide',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Oxyconide',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Parakil Plus (Ivermectin + Clorsulon)',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Renadex',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Tremacid',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Trilev-Vet',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Trmisol',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Triolev Vet',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 6,
                'name' => 'Vermic (Ivermectin)',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 7,
                'name' => 'Goat Pox Vaccine',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 7,
                'name' => 'Lumpyvex',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'disease_id' => 7,
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function cattleGroups()
    {
        CattleGroup::query()->insert([
            [
                'name' => 'অধিক দুধ উৎপাদনকারী',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'কম দুধ উৎপাদনকারী',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'মোটাতাজাকরন',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'দুধ শুকিয়ে গেছে',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'ফ্রেশ বকনা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'দুধ দিচ্ছে',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'ষাড়',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function cattleBreeds()
    {
        CattleBreed::query()->insert([
            [
                'name' => 'ফ্রিজিয়ান',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'শাহিওয়াল',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'পাবনা/দেশি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'লাল সিন্ধি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'মুন্ডি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'ব্রাহমা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'জার্সি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'সংকর',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'অন্যান্য',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function seedCompanies()
    {
        SeedCompany::query()->insert([
            [
                'name' => 'ব্র্যাক',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'প্রাণিসম্পদ বিভাগ',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'আমেরিকান ডেইরী',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'লালতীর',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'ইজাব আলায়েন্স',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'মিল্ক ভিটা',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'রুরাল ডেভেলপমেন্ট একাডেমী',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'এসিআই',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'সাভার ডেইরি',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
