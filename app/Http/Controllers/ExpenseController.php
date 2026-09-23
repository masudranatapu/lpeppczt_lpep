<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expense.allExpense');
}

    public function create()
    {
        return view('expense.partial.expenseCreate', [
            'expense_types' => ExpenseType::all()
        ]);
    }

    public function store(Request $request)
    {
        Expense::insert([
            'expense_type_id' => $request->expense_type_id,
            'account_id' => $request->account_id,
            'pay_by' => $request->pay_by,
            'expanse_date' => $request->expanse_date,
            'amount' => $request->amount,
            'note' => $request->note,
            'created_at' => Carbon::now(),
        ]);

        return response()->json("New Expense Added");
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        return view('expense.partial.expenseEdit', [
            'expense_types' => ExpenseType::all(),
            'expense' => $expense,
            'data' => Account::where('account_type', $expense->pay_by)->get(),
        ]);
    }

    public function update(Request $request)
    {
        Expense::findOrFail($request->id)->update([
            'expense_type_id' => $request->expense_type_id,
            'account_id' => $request->account_id,
            'pay_by' => $request->pay_by,
            'expanse_date' => $request->expanse_date,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        return response()->json([
            'message' => "Information Updated"
        ]);
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return response()->json([
            'message' => "Expense Deleted"
        ]);
    }

   public function allExpenseJson(Request $request)
    {
        try {
            $total_expense = 0;
            $expensesQuery = Expense::join('expense_types', 'expenses.expense_type_id', '=', 'expense_types.id')
                ->select('expenses.*', 'expense_types.expense_type');

            if ($request->year != "" || $request->year != null) {

                $year = $request->year;
                $month = $request->month;
                $total_expense = Expense::whereYear('expanse_date', $year)
                                        ->whereMonth('expanse_date', $month)
                                        ->sum('amount');
                $expensesQuery->whereYear('expenses.expanse_date', $year)
                              ->whereMonth('expenses.expanse_date', $month);
            } else {
                $total_expense = Expense::sum('amount');
            }

            $expense = $expensesQuery->orderBy('id', 'desc')->get();

            $datatable = datatables()->of($expense)
                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>";
                    if (permission('ex3')) {
                        $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit">Edit</a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>";
                    if (permission('uni3')) {
                        $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit">Edit</a>';
                    }
                    if (permission('uni4')) {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->toArray();
            $datatable['total_expense'] = $total_expense;
            return response()->json($datatable);
        } catch (\Exception $e) {
            Log::error('Error in allExpenseJson method: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function account(Request $request)
    {
        // return $request->all();
        if ($request->account_type != "Cash") {
            return view('expense.partial.account', [
                'data' => Account::where('account_type', $request->account_type)->get(),
                'account_type' => $request->account_type,
            ]);
        }
    }
}
