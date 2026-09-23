<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('income.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('income.create', [
            'income_types' => IncomeType::all(),
            'users' => User::active()->agent()->get(['id', 'name', 'employee_name'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreIncomeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $income = Income::create(['income_date' => $request->income_date]);
            foreach ($request->income_types as $employeeId => $incomeData) {
                $total = $incomeData['total'] ?? 0;
                $isAbsent = $incomeData['is_absent'] ?? 0;
                $note = $incomeData['note'];
                unset($incomeData['total'], $incomeData['note'],$incomeData['is_absent']);

                $income->details()->create([
                    'user_id' => $employeeId,
                    'income_types' => $incomeData,
                    'total' => $total,
                    'is_absent' => $isAbsent,
                    'note' => $note,
                ]);
            }
            DB::commit();
            return redirect(route('income.index'))->with('message', "Income Added");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        return view('income.view', [
            'data' => $income->load(['details', 'details.user:id,name,employee_name'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function edit(Income $income)
    {
        return view('income.edit', [
            'income' => $income->load(['details', 'details.user:id,name,employee_name'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateIncomeRequest  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {

        DB::beginTransaction();
        try {
            $income->update(['income_date' => $request->income_date]);
            foreach ($request->income_types as $employeeId => $incomeData) {
                $total = $incomeData['total'] ?? 0;
                $isAbsent = $incomeData['is_absent'] ?? 0;
                $note = $incomeData['note'];
                unset($incomeData['total'], $incomeData['note'],$incomeData['is_absent']);

                $incomeDetail = $income->details()->where('user_id', $employeeId)->first();
                if ($incomeDetail) {
                    $incomeDetail->update([
                        'income_types' => $incomeData,
                        'total' => $total,
                        'is_absent'=> $isAbsent,
                        'note' => $note,
                    ]);
                }
            }
            DB::commit();
            return redirect(route('income.index'))->with('message', "Income Updated");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {
        try {
            $income->delete();
            return response()->json([
                'message' => "Income Deleted"
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function allIncomeJson(Request $request)
    {
        try {

            $year = $request->year;
            $month = $request->month;

            $totalIncome = Income::filterByYearAndMonth($year, $month)
                ->withSum('details', 'total')
                ->whereHas('details')
                ->get()
                ->sum('details_sum_total');

            // Fetch expense data with relationships
            $incomes = Income::with('details')
                ->filterByYearAndMonth($year, $month)
                ->orderBy('income_date', 'desc')
                ->withSum('details', 'total')
                ->get();


            $datatable = datatables()->of($incomes)

                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>";
                    if (permission('uni3')) {
                        $btn .= '<a href="' . route("income.show", $data->id) . '" class="btn btn-secondary btn-sm">View</a>';
                    }

                    if (permission('uni3')) {
                        $btn .= '<a href="' . route("income.edit", $data->id) . '" class="btn btn-primary btn-sm">Edit</a>';
                    }
                    if (permission('uni4')) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->toArray();
            $datatable['total_income'] = $totalIncome;
            return response()->json($datatable);
        } catch (\Exception $e) {
            Log::error('Error in allExpenseJson method: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function history(Request $request)
    {
        $filterType = $request->input('filter_type');

        // Keep existing history links (for example ?year=2026&month=06)
        // working while supporting the new filter modes.
        if (!$filterType) {
            if ($request->filled('from_date') || $request->filled('to_date')) {
                $filterType = 'custom';
            } elseif ($request->filled('month')) {
                $filterType = 'month_year';
            } elseif ($request->filled('year')) {
                $filterType = 'year';
            } else {
                $filterType = 'all';
            }
        }

        $request->merge(['filter_type' => $filterType]);

        $filters = $request->validate([
            'filter_type' => ['required', 'in:all,month_year,year,custom'],
            'area_office' => ['nullable', 'string', 'max:255', 'exists:users,name'],
            'year' => ['required_if:filter_type,month_year,year', 'nullable', 'integer', 'between:2000,2100'],
            'month' => ['required_if:filter_type,month_year', 'nullable', 'integer', 'between:1,12'],
            'from_date' => ['required_if:filter_type,custom', 'nullable', 'date'],
            'to_date' => ['required_if:filter_type,custom', 'nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $year = isset($filters['year']) ? (int) $filters['year'] : null;
        $month = isset($filters['month']) ? (int) $filters['month'] : null;
        $areaOffice = $filters['area_office'] ?? null;
        $fromDate = $filters['from_date'] ?? null;
        $toDate = $filters['to_date'] ?? null;

        $incomes = Income::query()
            ->when($areaOffice, function ($query) use ($areaOffice) {
                $query->whereHas('details.user', fn ($users) => $users->where('name', $areaOffice));
            })
            ->when($filterType === 'month_year', function ($query) use ($year, $month) {
                $query->whereYear('income_date', $year)
                    ->whereMonth('income_date', $month);
            })
            ->when($filterType === 'year', function ($query) use ($year) {
                $query->whereYear('income_date', $year);
            })
            ->when($filterType === 'custom', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('income_date', [$fromDate, $toDate]);
            })
            ->with(['details' => function ($query) use ($areaOffice) {
                $query->when($areaOffice, function ($details) use ($areaOffice) {
                    $details->whereHas('user', fn ($users) => $users->where('name', $areaOffice));
                })
                    ->with('user');
            }])
            ->get();

        if ($filterType === 'month_year') {
            $filterLabel = Carbon::create($year, $month, 1)->format('F Y');
        } elseif ($filterType === 'year') {
            $filterLabel = (string) $year;
        } elseif ($filterType === 'custom') {
            $filterLabel = Carbon::parse($fromDate)->format('d M Y')
                . ' to ' . Carbon::parse($toDate)->format('d M Y');
        } else {
            $filterLabel = 'Life Time';
        }

        $firstIncomeDate = Income::min('income_date');
        $lastIncomeDate = Income::max('income_date');
        $oldestYear = $firstIncomeDate ? Carbon::parse($firstIncomeDate)->year : now()->year;
        $newestYear = $lastIncomeDate ? Carbon::parse($lastIncomeDate)->year : now()->year;
        $oldestYear = min($oldestYear, $year ?? now()->year);
        $newestYear = max($newestYear, $year ?? now()->year, now()->year);
        $yearOptions = range($newestYear, $oldestYear);

        $areaOfficeOptions = User::query()
            ->whereHas('incomeDetails')
            ->orderBy('name')
            ->distinct()
            ->pluck('name');

        if ($areaOffice) {
            $filterLabel .= ' - Area Office: ' . $areaOffice;
        }

        $incomeTypes = $incomes->flatMap->details->flatMap->income_types->keys()->unique();

        $employeeEarnings = [];
        $employeeWorkingDays = [];
        $employeeTargets = [];
        $employeeAA = [];
        $incomeTypeTotals = array_fill_keys($incomeTypes->toArray(), 0);

        $totalWorkingDays = 0;
        $totalTarget = 0;
        $totalAA = 0;
        $grandTotal = 0;
        $totalEmployees = 0;

        foreach ($incomes as $income) {
            foreach ($income->details as $detail) {
                $employee = $detail->user->employee_name . ' - ' . $detail->user->name;

                // Count total records (i.e., working days) per employee
                $employeeWorkingDays[$employee] = isset($employeeWorkingDays[$employee])
                    ? $employeeWorkingDays[$employee] + 1
                    : 1;

                // Reduce working days if the employee was absent
                if ($detail->is_absent == 1 && $employeeWorkingDays[$employee] > 0) {
                    $employeeWorkingDays[$employee]--;
                }

                if (!isset($employeeEarnings[$employee])) {
                    $employeeEarnings[$employee] = array_fill_keys($incomeTypes->toArray(), 0);
                    $totalEmployees++; // Count unique employees
                }

                // Sum up income types for each employee
                foreach ($detail->income_types as $type => $amount) {
                    $employeeEarnings[$employee][$type] += $amount;
                    $incomeTypeTotals[$type] += $amount;
                }
            }
        }

        // Calculate Target, A/A%, and other totals
        foreach ($employeeEarnings as $employee => $earnings) {
            $workingDays = max(0, $employeeWorkingDays[$employee] ?? 0); // Ensure non-negative value
            $target = $workingDays * 2800; // Target = 3500 * working days
            $employeeTargets[$employee] = $target;

            $rowTotal = array_sum($earnings); // Total earnings for the employee

            // Calculate A/A% (Actual Earnings / Target) * 100
            $employeeAA[$employee] = ($target > 0) ? ($rowTotal / $target) * 100 : 0;

            // Accumulate total working days, target, and A/A% for averaging
            $totalWorkingDays += $workingDays;
            $totalTarget += $target;
            $totalAA += $employeeAA[$employee];

            // Add rowTotal to grandTotal
            $grandTotal += $rowTotal;
        }

        $averageAA = ($totalEmployees > 0) ? ($totalAA / $totalEmployees) : 0;

        return view('income.history', compact(
            'filterType', 'filterLabel', 'areaOfficeOptions', 'areaOffice', 'yearOptions', 'year', 'month', 'fromDate', 'toDate',
            'incomeTypes', 'employeeEarnings',
            'employeeWorkingDays', 'employeeTargets', 'employeeAA',
            'totalWorkingDays', 'totalTarget', 'averageAA', 'incomeTypeTotals', 'grandTotal'
        ));
    }


}
