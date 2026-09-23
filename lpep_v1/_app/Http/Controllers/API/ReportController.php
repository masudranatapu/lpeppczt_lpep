<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calf;
use App\Models\Cattle;
use App\Models\CtlAbortionInfo;
use App\Models\CtlDiseaseInfo;
use App\Models\CtlFoodConsumption;
use App\Models\CtlImpregnation;
use App\Models\CtlPregnancyExam;
use App\Models\CtlVaccineInfo;
use App\Models\CtlMilkProduction;
use App\Models\CtlWeightInfo;
use App\Models\Farm;
use App\Models\MAccountEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $datePattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";

    public function cattleReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "gender" => "required|in:গাভী,দামড়া,সমস্ত",
        ]);

        $data = Cattle::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_group_id', $request->cattle_group_id);
            })
            ->when($request->gender != 'সমস্ত', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            })
            ->get();

        return [
            'cattle_count' => $data->count(),
            'data' => $data,
        ];
    }


    public function milkProductionReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(CtlMilkProduction::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'mlik_count' => $data->sum('value'),
            'data' => $data
        ];
    }


    public function vaccineReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(CtlVaccineInfo::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->when($request->cattle_disease_ids, function ($q) use ($request) {
                $q->whereIn('cattle_disease_id', json_decode($request->cattle_disease_ids));
            })
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }

    public function vaccineCalenderReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_disease_id" => "required",
        ]);

        $data = collect(CtlVaccineInfo::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_disease_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_disease_id', $request->cattle_disease_id);
            })
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data->groupBy('cattle_id'),
        ];
    }

    public function diseaseReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(CtlDiseaseInfo::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }

    public function impregnationReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "pal_breed_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(CtlImpregnation::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('pal_breed_id', $request->pal_breed_id)
            ->where('pal_date', '>=', $request->start_date)
            ->where('pal_date', '<=', $request->end_date)
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }

    public function calfReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "gender" => "required|in:এঁড়ে বাছুর,বকনা",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = Calf::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('gender', $request->gender)
            ->where('birth_date', '>=', $request->start_date)
            ->where('birth_date', '<=', $request->end_date)
            ->exclude([
                'app_customer_id', 'image',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get();

        return [
            'calf_count' => $data->count(),
            'data' => $data,
        ];
    }

    public function pregnancyExamReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
        ]);

        $data = collect(CtlPregnancyExam::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }


    public function abortionReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
        ]);

        $data = collect(CtlAbortionInfo::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }


    public function foodConsumptionAndWeightReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "cattle_group_id" => "required",
            "cattle_id" => "required",
            "day_count" => "required",
        ]);

        $foodConsumptions = collect(CtlFoodConsumption::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('date', '>=', Carbon::today()->addDays(-1 * $request->day_count)->format('Y-m-d'))
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        $weightInfo = collect(CtlWeightInfo::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->cattle_group_id != 'সমস্ত', function ($q) use ($request) {
                $q->whereHas('cattle', function ($q) use ($request) {
                    $q->where('cattle_group_id', $request->cattle_group_id);
                });
            })
            ->when($request->cattle_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('cattle_id', $request->cattle_id);
            })
            ->where('date', '>=', Carbon::today()->addDays(-1 * $request->day_count)->format('Y-m-d'))
            ->exclude([
                'app_customer_id',
                'created_at', 'created_by', 'created_by_type',
                'updated_at', 'updated_by', 'updated_by_type'
            ])
            ->get());

        $dates = array_unique(array_merge($foodConsumptions->pluck('date')->toArray(), $weightInfo->pluck('date')->toArray()));

        $data = collect([]);

        foreach ($dates as $key => $date) {
            $cfc = $foodConsumptions->where('date', $date)->first();
            $cwi = $weightInfo->where('date', $date)->first();

            $data->push([
                'sl' => $key + 1,
                'farm_id' => $cfc != null ? $cfc->farm_id : $cwi->farm_id,
                'cattle_id' => $cfc != null ? $cfc->cattle_id : $cwi->cattle_id,
                'date' => $date,
                'has_consumed_food' => $cfc != null,
                'has_measured_weight' => $cwi != null,
            ]);
        }

        return [
            'cattle_count' => $data->groupBy('cattle_id')->count(),
            'data' => $data,
        ];
    }


    public function mAccountEntryReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "type" => "required|in:Income,Expense",
            "account_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(MAccountEntry::query()
            ->where('farm_id', $request->farm_id)
            ->when($request->account_id != 'সমস্ত', function ($q) use ($request) {
                $q->where('account_id', $request->account_id);
            })
            ->whereHas('account', function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->exclude([
                'farm_id', 'created_at', 'updated_at'
            ])
            ->get());

        return [
            'total_quantity' => $data->sum('quantity'),
            'total_amount' => $data->sum('total_amount'),
            'data' => $data,
        ];
    }


    public function mAccountEntryReportByDateRange(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "start_date" => ['required', 'regex:' . $this->datePattern],
            "end_date" => ['required', 'regex:' . $this->datePattern],
        ]);

        $data = collect(MAccountEntry::query()
            ->with('account')
            ->where('farm_id', $request->farm_id)
            ->where('date', '>=', $request->start_date)
            ->where('date', '<=', $request->end_date)
            ->exclude([
                'farm_id', 'created_at', 'updated_at'
            ])
            ->orderBy('date')
            ->get());

        $total_income = $data->filter(function ($item) {
            return $item->account->type == 'Income';
        })->sum('total_amount');

        $total_expense = $data->filter(function ($item) {
            return $item->account->type == 'Expense';
        })->sum('total_amount');

        $output = collect([]);
        $data->groupBy('date')->each(function ($group) use ($output) {
            $total_income = $group->filter(function ($item) {
                return $item->account->type == 'Income';
            })->sum('total_amount');

            $total_expense = $group->filter(function ($item) {
                return $item->account->type == 'Expense';
            })->sum('total_amount');

            $group->each(function ($gi) {
                $gi->name = $gi->account->name;
                $gi->unit = $gi->account->unit;
                unset($gi->account);
                unset($gi->account_id);
            });

            $date = [
                'date' => $group->first()->date,
                'balance' => round($total_income - $total_expense, 2),
                'entries' => $group->toArray(),
            ];

            $output->push($date);
        });

        return [
            'total_income' => $total_income,
            'total_expense' => $total_expense,
            'balance' => round($total_income - $total_expense, 2),
            'data' => $output,
        ];
    }


    public function farmReport(Request $request)
    {
        $request->validate([
            "farm_id" => "required",
            "division_id" => "required",
            "district_id" => "required",
            "upazila_id" => "required",
        ]);

        $data = collect(Farm::query()
            ->where('division_id', $request->division_id)
            ->where('district_id', $request->district_id)
            ->where('upazila_id', $request->upazila_id)
            ->withCount('cattle')
            ->withCount('calves')
            ->get());

        return [
            'total_cattle_count' => $data->sum('cattle_count'),
            'total_calves_count' => $data->sum('calves_count'),
            'data' => $data,
        ];
    }
}
