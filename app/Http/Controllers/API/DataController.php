<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalfBirthProblem;
use App\Models\Cattle;
use App\Models\CattleBreed;
use App\Models\CattleGroup;
use App\Models\CattleDisease;
use App\Models\CattleFood;
use App\Models\CattleVaccine;
use App\Models\CtlImpregnation;
use App\Models\CtlManualHit;
use App\Models\CtlPregnancyExam;
use App\Models\DiseaseHistory;
use App\Models\Farm;
use App\Models\HealthInfo;
use App\Models\InsuranceCompany;
use App\Models\InsuranceType;
use App\Models\MAccount;
use App\Models\SeedCompany;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $accounts = collect(MAccount::query()->select('id', 'type', 'name')->get());
        return response()->json([
            'farms' => Farm::query()
                ->where('app_customer_id', auth()->user()->id)
                ->get()
                ->map(function ($farm) {
                    return [
                        'id' => (int) $farm->id,
                        'app_customer_id' => (int) $farm->app_customer_id,
                        'name' => $farm->name,
                        'created_by' => (int) $farm->created_by ?? null,
                        'updated_by' => (int) $farm->updated_by ?? null,
                        'division_id' => (int) $farm->division_id ?? null,
                        'district_id' => (int) $farm->district_id ?? null,
                        'upazila_id' => (int) $farm->upazila_id ?? null,
                        'union_id' => (int) $farm->union_id ?? null,
                    ];
                }),
            'insurance_types' => InsuranceType::query()->select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::query()->select('id', 'name')->get(),
            'cattle_groups' => CattleGroup::query()->select('id', 'name')->get(),
            'cattle_breeds' => CattleBreed::query()->select('id', 'name')->get(),
            'cattle_diseases' => CattleDisease::query()->select('id', 'name')->get(),
            'cattle_vaccines' => CattleVaccine::query()->select('id', 'disease_id', 'name')->get(),
            'seed_companies' => SeedCompany::query()->select('id', 'name')->get(),
            'disease_histories' => DiseaseHistory::query()->select('id', 'name')->get(),
            'health_info' => HealthInfo::query()->select('id', 'name')->get(),
            'cattle' => Cattle::query()
                ->whereHas('farm', function ($q) {
                    $q->where('app_customer_id', auth()->user()->id);
                })->get(),
            'manual_hits' => CtlManualHit::query()
                ->whereHas('farm', function ($q) {
                    $q->where('app_customer_id', auth()->user()->id);
                })
                ->select('id', 'cattle_id', 'date')
                ->get(),
            'impregnations' => CtlImpregnation::query()
                ->whereHas('farm', function ($q)  {
                    $q->where('app_customer_id', auth()->user()->id);
                })
                ->exclude([
                    'app_customer_id',
                    'created_by', 'created_at',  'created_by_type',
                    'updated_by', 'updated_at', 'updated_by_type'
                ])
                ->get(),
            'pregnancy_exams' => CtlPregnancyExam::query()
                ->whereHas('farm', function ($q) {
                    $q->where('app_customer_id', auth()->user()->id);
                })
                ->exclude([
                    'app_customer_id',
                    'created_by', 'created_at',  'created_by_type',
                    'updated_by', 'updated_at', 'updated_by_type'
                ])
                ->get(),
            'calf_birth_problems' => CalfBirthProblem::query()->select('id', 'name')->get(),
            'cattle_foods' => CattleFood::query()->select('id', 'name')->get(),
            'income_accounts' => $accounts->where('type', 'Income')->flatten(1),
            'expense_accounts' => $accounts->where('type', 'Expense')->flatten(1),
        ]);
    }

    public function locations()
    {
        return [
            'divisions' => Division::query()->select('id', 'bn_name')->get(),
            'districts' => District::query()->select('id', 'division_id', 'bn_name')->get(),
            'upazilas' => Upazila::query()->select('id', 'district_id', 'bn_name')->get(),
        ];
    }

    public function summary(Request $request)
    {
        $user = auth()->user()
            ->loadCount([
                'agentCustomerTransactions as loan_taken' => fn ($q) => $q->where('type', TXN_SEND)->select(DB::raw('sum(amount)')),
                'agentCustomerTransactions as loan_paid_by_sale' => fn ($q) => $q->whereRelation('details', 'purchase_id', '>', 0)->where('type', TXN_RECEIVE)->select(DB::raw('sum(amount)')),
                'agentCustomerTransactions as loan_paid_by_cash' => fn ($q) => $q->whereRelation('details', 'purchase_id', null)->where('type', TXN_RECEIVE)->select(DB::raw('sum(amount)')),
                'agentSales as product_purchased' => fn ($q) => $q->select(DB::raw('sum(total_amount)')),
            ]);

        return response()->json([
            'total_loan_points' => round($user->loan_taken - $user->loan_paid, 2),
            'total_purchase_points' => round($user->product_purchased, 2),
            'total_sale_points' => round($user->loan_paid_by_sale, 2),
            'total_hand_cash' => round($user->loan_paid_by_cash, 2),
            'total_paid_points' => round($user->loan_paid_by_sale + $user->loan_paid_by_cash, 2),
            'total_available_points' => round($user->loan_taken - $user->product_purchased - $user->loan_paid_by_sale - $user->loan_paid_by_cash, 2),
        ]);
    }
}
