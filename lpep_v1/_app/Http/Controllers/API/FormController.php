<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Calf;
use App\Models\Cattle;
use App\Models\CtlManualHit;
use Illuminate\Http\Request;
use App\Models\CtlWeightInfo;
use App\Models\MAccountEntry;
use App\Models\CtlDiseaseInfo;
use App\Models\CtlVaccineInfo;
use App\Models\CtlAbortionInfo;
use App\Models\CtlImpregnation;
use App\Models\CtlPregnancyExam;
use App\Models\CtlMilkProduction;
use App\Models\CtlFoodConsumption;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FormController extends Controller
{
    private $datePattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";

    public function storeCattle(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'tag' => 'required',
            'name' => 'required',
            'cattle_group_id' => 'required',
            'cattle_breed_id' => 'required',
            'birth_date' => ['required', 'regex:' . $this->datePattern],
            'weight' => 'required',
            'gender' => "required|in:গাভী,দামড়া,সমস্ত",
            'health_problem' => 'required',
            'avg_milk_production' => 'required',
            'milk_production_status' => 'required',
            'calf_count' => 'required',
            'last_calf_birth_date' => ['required', 'regex:' . $this->datePattern],
            'genetic_percentage' => 'required',
            'insurance_company_id' => 'required',
            'insurance_type_id' => 'required',
            'insurance_no' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $cattle = Cattle::query()->create([
                'farm_id' => $request->farm_id,

                // গাভী / ষাড় সম্পর্কিত তথ্য
                'tag' => $request->tag,
                'name' => $request->name,
                'cattle_group_id' => $request->cattle_group_id,
                'cattle_breed_id' => $request->cattle_breed_id,
                'birth_date' => $request->birth_date,
                'weight' => $request->weight,
                'gender' => $request->gender,
                'health_problem' => $request->health_problem,

                // দুধ, প্রজনন, রোগ তথ্য
                'avg_milk_production' => $request->avg_milk_production,
                'milk_production_status' => $request->milk_production_status,
                'calf_count' => $request->calf_count,
                'last_calf_birth_date' => $request->last_calf_birth_date,
                'genetic_percentage' => $request->genetic_percentage,

                // ইন্স্যুরেন্স তথ্য
                'insurance_company_id' => $request->insurance_company_id,
                'insurance_type_id' => $request->insurance_type_id,
                'insurance_no' => $request->insurance_no,

                // ছবি
                'front_image_1' => $request->hasFile('front_image_1') ? $request->front_image_1->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'front_image_2' => $request->hasFile('front_image_2') ? $request->front_image_2->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'left_image_1' => $request->hasFile('left_image_1') ? $request->left_image_1->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'left_image_2' => $request->hasFile('left_image_2') ? $request->left_image_2->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'right_image_1' => $request->hasFile('right_image_1') ? $request->right_image_1->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'right_image_2' => $request->hasFile('right_image_2') ? $request->right_image_2->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'back_image_1' => $request->hasFile('back_image_1') ? $request->back_image_1->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                'back_image_2' => $request->hasFile('back_image_2') ? $request->back_image_2->store('uploads/cattle', ['disk' => 'public_uploads']) : null,

                // log
                'created_by' => $request->user()->id,
                'created_by_type' => 'customer'
            ]);

            foreach (json_decode($request->disease_history_ids ?? '[]') as $disease_history_id) {
                $cattle->diseaseHistories()->create([
                    'disease_history_id' => $disease_history_id,
                ]);
            }

            foreach (json_decode($request->health_info_ids ?? '[]') as $health_info_id) {
                $cattle->healthInfos()->create([
                    'health_info_id' => $health_info_id,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeMilkProduction(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'productions' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
            'time' => 'required'
        ]);

        try {
            DB::beginTransaction();

            foreach (json_decode($request->productions, true) as $production) {
                CtlMilkProduction::query()
                    ->create([
                        'farm_id' => $request->farm_id,
                        'cattle_id' => $production['cattle_id'],
                        'value' => $production['value'],
                        'date' => $request->date,
                        'time' => $request->time,
                        'created_by' => $request->user()->id,
                        'created_by_type' => 'customer',
                    ]);
            }
            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeVaccineInfo(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'cattle_disease_id' => 'required',
            'cattle_vaccine_id' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
        ]);

        try {
            DB::beginTransaction();

            CtlVaccineInfo::query()
                ->create([
                    'farm_id' => $request->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'cattle_disease_id' => $request->cattle_disease_id,
                    'cattle_vaccine_id' => $request->cattle_vaccine_id,
                    'cattle_id' => $request->cattle_id,
                    'date' => $request->date,
                    'remark' => $request->remark,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeDiseaseInfo(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'cattle_disease_id' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
        ]);

        try {
            DB::beginTransaction();

            CtlDiseaseInfo::query()
                ->create([
                    'farm_id' => $request->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'cattle_disease_id' => $request->cattle_disease_id,
                    'cattle_id' => $request->cattle_id,
                    'date' => $request->date,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeWeightInfo(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'weights' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
        ]);

        try {
            DB::beginTransaction();

            foreach (json_decode($request->weights, true) as $weight) {
                CtlWeightInfo::query()
                    ->create([
                        'farm_id' => $request->farm_id,
                        'cattle_id' => $weight['cattle_id'],
                        'value' => $weight['value'],
                        'date' => $request->date,
                        'created_by' => $request->user()->id,
                        'created_by_type' => 'customer',
                    ]);
            }

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeFoodConsumption(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'foods' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
        ]);

        try {
            DB::beginTransaction();

            foreach (json_decode($request->foods, true) as $food) {
                CtlFoodConsumption::query()
                    ->create([
                        'farm_id' => $request->farm_id,
                        'cattle_id' => $request->cattle_id,
                        'cattle_food_id' => $request->cattle_food_id,
                        'date' => $request->date,
                        'cattle_food_id' => $food['cattle_food_id'],
                        'value' => $food['value'],
                        'created_by' => $request->user()->id,
                        'created_by_type' => 'customer',
                    ]);
            }

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeManualHit(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern],
        ]);

        try {
            DB::beginTransaction();

            CtlManualHit::query()
                ->create([
                    'farm_id' => $request->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'date' => $request->date,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeImpregnation(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'manual_hit_id' => 'required',
            'pal_date' => ['required', 'regex:' . $this->datePattern],
            'pal_breed_id' => 'required',
            'pal_type' => 'required|in:প্রাকৃতিক,কৃত্রিম প্রজনন',
            'seed_company_id' => 'nullable|required_if:pal_type,কৃত্রিম প্রজনন',
            'seed_percentage' => 'nullable|required_if:pal_type,কৃত্রিম প্রজনন',
            'straw_number' => 'nullable|required_if:pal_type,কৃত্রিম প্রজনন',
            'worker_info' => 'nullable|required_if:pal_type,কৃত্রিম প্রজনন',
        ]);

        try {
            DB::beginTransaction();

            $cattle = Cattle::query()->find($request->cattle_id);

            CtlImpregnation::query()
                ->create([
                    'farm_id' => $cattle->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'manual_hit_id' => $request->manual_hit_id,
                    'pal_date' => $request->pal_date,
                    'pal_type' => $request->pal_type,
                    'pal_breed_id' => $request->pal_breed_id,
                    'seed_company_id' => $request->seed_company_id,
                    'seed_percentage' => $request->seed_percentage,
                    'straw_number' => $request->straw_number,
                    'worker_info' => $request->worker_info,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storePregnancyExam(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            "impregnation_id" => 'required',
            "is_pregnant" => "required|in:0,1",
            "expected_delivery_date" => "nullable|required_if:is_pregnant,1"
        ]);

        try {
            DB::beginTransaction();

            $cattle = Cattle::query()->find($request->cattle_id);

            CtlPregnancyExam::query()
                ->create([
                    'farm_id' => $cattle->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'impregnation_id' => $request->impregnation_id,
                    'is_pregnant' => $request->is_pregnant,
                    'expected_delivery_date' => $request->is_pregnant == 1 ? $request->expected_delivery_date : null,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeAbortionInfo(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'pregnancy_exam_id' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern]
        ]);

        try {
            DB::beginTransaction();

            $cattle = Cattle::query()->find($request->cattle_id);

            CtlAbortionInfo::query()
                ->create([
                    'farm_id' => $cattle->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'pregnancy_exam_id' => $request->pregnancy_exam_id,
                    'date' => $request->date,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeCalf(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'cattle_id' => 'required',
            'name' => 'required',
            'gender' => 'required',
            'weight' => 'required',
            'birth_date' => ['required', 'regex:' . $this->datePattern]
        ]);

        try {
            DB::beginTransaction();

            $calf = Calf::query()
                ->create([
                    'farm_id' => $request->farm_id,
                    'cattle_id' => $request->cattle_id,
                    'tag' => $request->tag,
                    'name' => $request->name,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'weight' => $request->weight,
                    'image' => $request->hasFile('image') ? $request->image->store('uploads/cattle', ['disk' => 'public_uploads']) : null,
                    'created_by' => $request->user()->id,
                    'created_by_type' => 'customer',
                ]);

            foreach (json_decode($request->calf_birth_problem_ids ?? '[]') as $calf_birth_problem_id) {
                $calf->birthProblems()->create([
                    'calf_birth_problem_id' => $calf_birth_problem_id,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeMAccountEntry(Request $request)
    {
        $request->validate([
            'farm_id' => 'required',
            'account_id' => 'required',
            'quantity' => 'required',
            'amount_per_unit' => 'required',
            'total_amount' => 'required',
            'date' => ['required', 'regex:' . $this->datePattern]
        ]);

        try {
            DB::beginTransaction();

            MAccountEntry::query()
                ->create([
                    'farm_id' => $request->farm_id,
                    'account_id' => $request->account_id,
                    'quantity' => $request->quantity,
                    'amount_per_unit' => $request->amount_per_unit,
                    'total_amount' => $request->total_amount,
                    'date' => $request->date,
                    'remark' => $request->remark
                ]);

            DB::commit();
            return response()->json(['message' => 'Data insertion successful!']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }
}
