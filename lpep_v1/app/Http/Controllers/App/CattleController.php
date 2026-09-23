<?php

namespace App\Http\Controllers\App;

use App\Models\Cattle;
use App\Models\CattleBreed;
use App\Models\CattleGroup;
use App\Models\DiseaseHistory;
use App\Models\Farm;
use App\Models\HealthInfo;
use App\Models\InsuranceCompany;
use App\Models\InsuranceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CattleController extends Controller
{
    public function index()
    {
        return view('cattle.index');
    }

    public function create()
    {
        $data = [
            'cattleGroups' => CattleGroup::query()
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'cattleBreeds' => CattleBreed::query()
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'insuranceCompanies' => InsuranceCompany::query()->select('id', 'name')->pluck('name', 'id'),
            'insuranceTypes' => InsuranceType::query()->select('id', 'name')->pluck('name', 'id'),
            'diseaseHistories' => DiseaseHistory::query()->select('id', 'name')->pluck('name', 'id'),
            'healthInfos' => HealthInfo::query()->select('id', 'name')->pluck('name', 'id'),
        ];
        return view('cattle.create', $data);
    }

    public function store(Request $request): JsonResponse
    {
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

                'created_by_type' => 'staff',
                'created_by' => auth()->id(),
                'created_at' => date('Y-m-d H:i:s')
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
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return response()->json('Cattle saved successfully!');
    }

    public function edit($id)
    {
        $data = [
            'cattleGroups' => CattleGroup::query()
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'cattleBreeds' => CattleBreed::query()
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'insuranceCompanies' => InsuranceCompany::query()->select('id', 'name')->pluck('name', 'id'),
            'insuranceTypes' => InsuranceType::query()->select('id', 'name')->pluck('name', 'id'),
            'diseaseHistories' => DiseaseHistory::query()->select('id', 'name')->pluck('name', 'id'),
            'healthInfos' => HealthInfo::query()->select('id', 'name')->pluck('name', 'id'),
            'cattle' => Cattle::query()->with('diseaseHistories', 'healthInfos', 'farm.customer')->findOrFail($id),
        ];

        $data['farms'] = Farm::query()->select('id', 'name')->pluck('name', 'id');

        return view('cattle.edit', $data);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $cattle = Cattle::query()->find($id);

        try {
            DB::beginTransaction();

            $cattle->update([
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

                'updated_by_type' => 'staff',
                'updated_by' => auth()->id(),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $cattle->diseaseHistories()->delete();
            foreach (json_decode($request->disease_history_ids ?? '[]') as $disease_history_id) {
                $cattle->diseaseHistories()->create([
                    'disease_history_id' => $disease_history_id,
                ]);
            }

            $cattle->healthInfos()->delete();
            foreach (json_decode($request->health_info_ids ?? '[]') as $health_info_id) {
                $cattle->healthInfos()->create([
                    'health_info_id' => $health_info_id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return response()->json('Cattle updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            Cattle::query()->where('id', $id)->delete();
            DB::commit();

            return response()->json('Cattle deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json('Cattle couldn\'t be deleted!');
        }
    }

    public function allAsJson(Request $request)
    {
        return datatables()->of(
            Cattle::query()
                ->when($request->agent_id || isRole(ROLE_AGENT), fn ($q) => $q->whereRelation('farm.customer', 'agent_id', $request->agent_id ?? auth()->id()))
                ->with('farm:id,name', 'createdBy', 'updatedBy', 'createdByCustomer', 'updatedByCustomer')
        )
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>" . $data->log();
                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="tableEdit">Edit</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function findByFarmAsJson($id)
    {
        return response()->json(['data' => Cattle::query()->where('farm_id', $id)->select('id', 'name')->get()]);
    }
}
