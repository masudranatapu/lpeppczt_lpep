<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        return view('expense.expanseType', [
            'items' => ExpenseType::query()->orderBy('id', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        ExpenseType::insert([
            'expense_type' => $request->expense_type,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('expense.type')->with('message', "New Expense Type Added");
    }

    public function edit($id)
    {
        return view('expense.expanseType', [
            'items' => ExpenseType::query()->orderBy('id', 'desc')->get(),
            'type' => ExpenseType::find($id),
        ]);
    }

    public function update(Request $request)
    {
        ExpenseType::find($request->id)->update([
            'expense_type' => $request->expense_type,
        ]);

        return redirect()->route('expense.type')->with('message', "Expense Type Updated");
    }

    public function destroy($id)
    {
        ExpenseType::find($id)->delete();
        return redirect()->route('expense.type')->with('message', "Expense Type Deleted");
    }
}
