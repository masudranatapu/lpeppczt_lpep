<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Purchase;
use App\Models\Salary;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;

class AccountController extends Controller
{
    public function salaryReport(Request $request)
    {

        if ($request->month) {
            $employees = Employee::join('salaries', 'salaries.employee_id', 'employees.id')
                ->where('salary_month', $request->month)
                ->get();
        } else {
            $employees = Employee::join('salaries', 'salaries.employee_id', 'employees.id')
                ->where('salary_month', date("F"))
                ->get();
        }
        return view('account.salaryReport', [
            'employees' => $employees,
        ]);
    }

    /**
     * @void receiveAmount
     * Show all sale Total Amount
     */
    public function receiveAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Sale::with('customer')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->get();
        } else {
            $sales = Sale::with('customer')->get();
        }
        $customers = Contact::where('type', 'customer')->get();

        return view('account.receiveAmountReport', [
            'customers' => $customers,
            'sales' => $sales,
        ]);
    }


    /**
     * @void receivedAmount
     * Show all sale Total received amount
     */
    public function receivedAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Sale::with('customer')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->get();
        } else {
            $sales = Sale::with('customer')->get();
        }
        $customers = Contact::where('type', 'customer')->get();

        return view('account.receivedAmountReport', [
            'customers' => $customers,
            'sales' => $sales,
        ]);
    }


    /**
     * @void receiveableAmount
     * Show all sale Total due amount
     */
    public function receiveableAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Sale::with('customer')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Sale::with('customer')->whereBetween('sale_date', [$request->start, $request->end])->get();
        } else {
            $sales = Sale::with('customer')->get();
        }
        $customers = Contact::where('type', 'customer')->get();

        return view('account.receiveableAmountReport', [
            'customers' => $customers,
            'sales' => $sales,
        ]);
    }

    /**
     * @void paymentAmount
     * Show all purchase
     */
    public function paymentAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Purchase::with('supplier')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->get();
        } else {
            $sales = Purchase::with('supplier')->get();
        }
        $customers = Contact::where('type', 'supplier')->get();

        return view('account.paymentAmount', [
            'suppliers' => $customers,
            'purchases' => $sales,
        ]);
    }

    /**
     * @void paid
     * Show all purchase paid amount
     */
    public function paidAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Purchase::with('supplier')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->get();
        } else {
            $sales = Purchase::with('supplier')->get();
        }
        $customers = Contact::where('type', 'supplier')->get();

        return view('account.paidAmount', [
            'suppliers' => $customers,
            'purchases' => $sales,
        ]);
    }

    /**
     * @void paid
     * Show all purchase due amount
     */
    public function payableAmount(Request $request)
    {
        if ($request->contact_id && $request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->where('contact_id', $request->contact_id)->get();
        } elseif ($request->contact_id) {
            $sales = Purchase::with('supplier')->where('contact_id', $request->contact_id)->get();
        } elseif ($request->start && $request->end) {
            $sales = Purchase::with('supplier')->whereBetween('purchase_date', [$request->start, $request->end])->get();
        } else {
            $sales = Purchase::with('supplier')->get();
        }
        $customers = Contact::where('type', 'supplier')->get();

        return view('account.payableAmount', [
            'suppliers' => $customers,
            'purchases' => $sales,
        ]);
    }

    public function expenseReport(Request $request)
    {
        if ($request->start && $request->end && $request->expense_category) {
            $expenses = Expense::with('expenseType')->whereBetween('expanse_date', [$request->start, $request->end])->where('expense_type_id', $request->expense_category)->get();
        } elseif ($request->start && $request->end) {
            $expenses = Expense::with('expenseType')->whereBetween('expanse_date', [$request->start, $request->end])->get();
        } elseif ($request->expense_category) {
            $expenses = Expense::with('expenseType')->where('expense_type_id', $request->expense_category)->get();
        } else {
            $expenses = Expense::with('expenseType')->get();
        }
        $expense_types = ExpenseType::query()->get();

        return view("account.expenseReport", [
            'expnese_types' => $expense_types,
            'expenses' => $expenses,
        ]);
    }
}
