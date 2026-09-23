<?php

namespace App\Http\Controllers;

use App\Models\IncomeType;
use App\Http\Requests\StoreIncomeTypeRequest;
use App\Http\Requests\UpdateIncomeTypeRequest;
use App\Models\Income;
use App\Models\IncomeDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class IncomeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $rquest)
    {
        if ($rquest->ajax()) {
            $data = IncomeType::query()->get();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $viewLink = route('incomeType.show', $row->id);
                    $editLink = route('incomeType.edit', $row->id);
                    $deleteLink = route('incomeType.destroy', $row->id);
                    return yajraViewEditDeleteButtons($viewLink, $editLink, $deleteLink);
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('income.type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('income.type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreIncomeTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreIncomeTypeRequest $request)
    {
        try {
            IncomeType::create($request->validated());
            return redirect()->back()->with('message', "New Income Type Added");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IncomeType  $incomeType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, IncomeType $incomeType)
    {
        $filterYear = $request->year ?? Carbon::now()->year;
        $filterMonth = $request->month ?? Carbon::now()->month;

        $incomes = Income::filterByYearAndMonth($filterYear, $filterMonth)
            ->with(['details' => function ($query) {
                $query->select('income_id', 'income_types');
            }])
            ->orderBy('income_date', 'desc')
            ->get();

        return view('income.type.show', [
            'type' => $incomeType,
            'reqYear' => $filterYear,
            'reqMonth' => $filterMonth,
            'incomes' => $incomes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\IncomeType  $incomeType
     * @return \Illuminate\Http\Response
     */
    public function edit(IncomeType $incomeType)
    {
        return view('income.type.create', [
            'type' => $incomeType
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateIncomeTypeRequest  $request
     * @param  \App\Models\IncomeType  $incomeType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateIncomeTypeRequest $request, IncomeType $incomeType)
    {
        try {
            $incomeType->update($request->validated());
            return redirect(route('incomeType.index'))->with('message', "New Income Type Added");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IncomeType  $incomeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(IncomeType $incomeType)
    {
        try {
            $incomeType->delete();
            return redirect()->back()->with('message', "Income Type Deleted");
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function print(Request $request, IncomeType $incomeType)
    {
        $filterYear = $request->year ?? Carbon::now()->year;
        $filterMonth = $request->month ?? Carbon::now()->month;

        $incomes = Income::filterByYearAndMonth($filterYear, $filterMonth)
            ->with(['details' => function ($query) {
                $query->select('income_id', 'income_types');
            }])
            ->orderBy('income_date', 'asc')
            ->get();

        return view('income.type.print', [
            'type' => $incomeType,
            'incomes' => $incomes
        ]);
    }


    public function history()
    {

        return "Hello";

        $year = $request->year;
        $month = $request->month;

        return $year.'+'.$month;
        return view('income.history', [
            // 'data' => $income->load(['details', 'details.user:id,name,employee_name'])
        ]);
    }


    public function icomeTypeHistory(Request $request){

        $year = $request->year;
        $month = $request->month;

        $incomes = Income::filterByYearAndMonth($year, $month)
            ->with(['details.user'])
            ->get();

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
            $target = $workingDays * 3500; // Target = 3500 * working days
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

        return view('income.type.report', compact(
            'year', 'month', 'incomeTypes', 'employeeEarnings',
            'employeeWorkingDays', 'employeeTargets', 'employeeAA',
            'totalWorkingDays', 'totalTarget', 'averageAA', 'incomeTypeTotals', 'grandTotal'
        ));

    }
}
