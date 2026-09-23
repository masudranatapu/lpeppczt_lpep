<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Business;
use App\Models\Contact;
use App\Models\Deposit;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function setting()
    {
        if (permission('st1')) {
            return view('setting.setting', [
                'setting' => Business::findOrFail(1)
            ]);
        } else {
            abort(403, 'You have not access in this page');
        }
    }

    public function settingUpdate(Request $request)
    {
        $request->validate([
            'logo' => 'mimes: jpg,jpeg,png'
        ]);

        $setting = Business::findOrFail($request->id);
        $setting->name = $request->name;
        $setting->email = $request->email;
        $setting->mobile = $request->mobile;
        $setting->color = $request->color;
        $setting->invoice_name = $request->invoice_name;
        $setting->deliverycharge = $request->deliverycharge ? 1 : 0;
        $setting->logo_print = $request->logo_print;

        if ($request->hasFile('logo')) {
            $path = $request->logo->store('uploads/logo', ['disk' => 'public_uploads']);
            $setting->logo = $path;
        }

        if ($request->hasFile('invoice_logo')) {
            $path = $request->invoice_logo->store('uploads/invoice_logo', ['disk' => 'public_uploads']);
            $setting->invoice_logo = $path;
        }

        if ($setting->save()) {
            cache()->forget('business-' . auth()->id());
            return back()->with('message', "Information Updated Successfully");
        }
    }

    public function changePassword()
    {
        return view('setting.changePassword');
    }

    public function changePasswordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if (Hash::check($request->old_password, Auth::user()->password)) {
            User::find(Auth::id())->update([
                'password' => Hash::make($request->password),
            ]);
            return back()->with('message', 'Password Changed Successfully');
        } else {
            return back()->with([
                'type' => 'error',
                'message' => "Old Password Does Not Mathch"
            ]);
        }
    }

    public function openingBalance()
    {
        $deposit = Deposit::query()->get();
        $withdraw = Withdraw::query()->get();
        return view('setting.openingBalance', [
            'deposits' => $deposit,
            'withdraws' => $withdraw,
            'deposit_total' => $deposit->sum('amount'),
            'withdraw_total' => $withdraw->sum('amount'),
        ]);
    }

    public function depositDelete($id)
    {
        $deposit = Deposit::findOrFail($id);
        if ($deposit->delete()) {
            return back()->with('message', "Deposit Deleted Successfully");
        }
    }

    public function withdrawDelete($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        if ($withdraw->delete()) {
            return back()->with('message', "Withdraw Deleted Successfully");
        }
    }

    public function openingBalanceStore(Request $request)
    {
        if ($request->balance_type == 1) {
            $deposit = new Deposit();
            $deposit->account_id = $request->account_id;
            $deposit->pay_by = $request->pay_by;
            $deposit->amount = $request->amount;
            $deposit->note = $request->note;
            $deposit->account_id = $request->account_id;
            if ($deposit->save()) {
                return back()->with('message', "New Deposit Amount Added Successfully");
            }
        } else {

            if ($request->balance < $request->amount) {
                return back()->with([
                    'type' => 'error',
                    'message' => "Sorry! Your Balance Is Low"
                ]);
            } else {
                $withdraw = new Withdraw();
                $withdraw->account_id = $request->account_id;
                $withdraw->pay_by = $request->pay_by;
                $withdraw->amount = $request->amount;
                $withdraw->note = $request->note;
                $withdraw->account_id = $request->account_id;
                if ($withdraw->save()) {
                    return back()->with('message', "Amount Withdraw Successfully");
                }
            }
        }
    }

    public function account(Request $request)
    {
        // return $request->all();
        if ($request->account_type != "Cash") {
            return view('setting.partial.account', [
                'data' => Account::where('account_type', $request->account_type)->get(),
                'account_type' => $request->account_type,
            ]);
        }
    }

    public function editDeposit($id)
    {
        $deposit = Deposit::query()->get();
        $withdraw = Withdraw::query()->get();
        $editDeposit = Deposit::findOrFail($id);
        return view('setting.openingBalance', [
            'deposits' => $deposit,
            'withdraws' => $withdraw,
            'deposit_total' => $deposit->sum('amount'),
            'withdraw_total' => $withdraw->sum('amount'),
            'type' => "deposit",
            'editDeposit' => $editDeposit,
            'data' => Account::where('account_type', $editDeposit->account->account_type)->get(),
        ]);
    }

    public function editWithdraw($id)
    {
        $deposit = Deposit::query()->get();
        $withdraw = Withdraw::query()->get();
        $editWithdraw = Withdraw::findOrFail($id);
        return view('setting.openingBalance', [
            'deposits' => $deposit,
            'withdraws' => $withdraw,
            'deposit_total' => $deposit->sum('amount'),
            'withdraw_total' => $withdraw->sum('amount'),
            'type' => "withdraw",
            'editWithdraw' => $editWithdraw,
            'data' => Account::where('account_type', $editWithdraw->account->account_type)->get(),
        ]);
    }

    public function depositUpdate(Request $request)
    {
        $deposit = Deposit::findOrFail($request->id);
        $deposit->account_id = $request->account_id;
        $deposit->pay_by = $request->pay_by;
        $deposit->amount = $request->amount;
        $deposit->note = $request->note;
        $deposit->account_id = $request->account_id;
        if ($deposit->save()) {
            return redirect()->route("setting.opening.balance")->with('message', "Deposit Updated Successfully");
        }
    }

    public function withdrawUpdate(Request $request)
    {
        $deposit = Withdraw::findOrFail($request->id);
        $deposit->account_id = $request->account_id;
        $deposit->pay_by = $request->pay_by;
        $deposit->amount = $request->amount;
        $deposit->note = $request->note;
        $deposit->account_id = $request->account_id;
        if ($deposit->save()) {
            return redirect()->route("setting.opening.balance")->with('message', "Withdraw Updated Successfully");
        }
    }

    public function checkBalance(Request $request)
    {
        if ($request->account_id) {
            return view('setting.partial.balance', [
                'balance' => accountBalance($request->account_id)
            ]);
        } else {
            return "cash";
        }
    }

    public function allCustomerBalance($account_id)
    {
        if ($account_id) {
            $sum = 0;
            foreach (Contact::where('type', "customer")->get() as $customer) {
                $sum += $customer->customerPayment($account_id)->sum('paying_amount');
            }
            return $sum;
        }
    }

    public function allSupplierBlance($account_id)
    {
        $sum = 0;
        foreach (Contact::where('type', "supplier")->get() as $customer) {
            $sum += $customer->customerPayment($account_id)->sum('paying_amount');
        }
        return $sum;
    }

    public function allCustomerCashBalance()
    {
        $sum = 0;
        foreach (Contact::where('type', "customer")->get() as $customer) {
            $sum += $customer->customerCashPayment()->sum('paying_amount');
        }
        return $sum;
    }

    public function allSupplierCashBalance()
    {
        $sum = 0;
        foreach (Contact::where('type', "customer")->get() as $customer) {
            $sum += $customer->customerCashPayment()->sum('paying_amount');
        }
        return $sum;
    }

    public function cashAccount(Request $request)
    {
        $deposit = Deposit::query()->whereNull('account_id')->sum('amount');
        $withdraw = Withdraw::query()->whereNull('account_id')->sum('amount');

        $expense = Expense::query()->whereNull('account_id')->sum('amount');
        $salary = Employee::join('salaries', "salaries.employee_id", "=", 'employees.id')
            ->whereNull('salaries.account_id')
            ->sum('amount');
        $balance = ($this->allCustomerCashBalance() + $deposit) - ($this->allSupplierCashBalance() + $salary + $expense + $withdraw);

        return view('setting.partial.cashaccount', [
            'balance' => $balance,
        ]);
    }
}
