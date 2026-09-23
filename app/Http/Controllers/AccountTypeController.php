<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTypeController extends Controller
{
    public function index()
    {
        return view('accountType.index', []);
    }

    public function accountTypCheck(Request $request)
    {
        if ($request->type == "Mobile Banking") {
            return view('accountType.partial.mobileBank');
        } elseif ($request->type == "Card") {
            return view('accountType.partial.card', [
                'bank_lists' => BankList::all()
            ]);
        } elseif ($request->type == "Bank Account") {
            return view('accountType.partial.bank', [
                'bank_lists' => BankList::all()
            ]);
        } elseif ($request->type == "Cash") {
            return view('accountType.partial.cash');
        }
    }

    public function store(Request $request)
    {
        $account = new Account();

        $account->account_type = $request->account_type;
        $account->mobile_bank_name = $request->mobile_bank_name;
        $account->mobile_number = $request->mobile_number;
        $account->bank_list_id = $request->bank_list_id;
        $account->bank_account_type = $request->bank_account_type;
        $account->bank_account_name = $request->bank_account_name;
        $account->bank_account_number = $request->bank_account_number;
        $account->bank_account_branch = $request->bank_account_branch;
        $account->card_type = $request->card_type;
        $account->card_holder_name = $request->card_holder_name;
        $account->card_number = $request->card_number;
        $account->valid_thru_month = $request->valid_thru_month;
        $account->valid_thru_year = $request->valid_thru_year;
        $account->cvv_code = $request->cvv_code;

        if ($account->save()) {
            return back()->with('message', "New Account Added Successfully");
        }
    }

    public function update(Request $request)
    {
        $account = Account::findOrFail($request->id);

        $account->account_type = $request->account_type;
        $account->mobile_bank_name = $request->mobile_bank_name;
        $account->mobile_number = $request->mobile_number;
        $account->bank_list_id = $request->bank_list_id;
        $account->bank_account_type = $request->bank_account_type;
        $account->bank_account_name = $request->bank_account_name;
        $account->bank_account_number = $request->bank_account_number;
        $account->bank_account_branch = $request->bank_account_branch;
        $account->card_type = $request->card_type;
        $account->card_holder_name = $request->card_holder_name;
        $account->card_number = $request->card_number;
        $account->valid_thru_month = $request->valid_thru_month;
        $account->valid_thru_year = $request->valid_thru_year;
        $account->cvv_code = $request->cvv_code;

        if ($account->save()) {
            return back()->with('message', "Account Updated Successfully");
        }
    }

    public function edit($id)
    {
        return view('accountType.edit', [
            'account' => Account::findOrFail($id),
            'bank_lists' => BankList::all(),
        ]);
    }

    public function list()
    {
        return view('accountType.list', [
            'lists' => Account::query()->get()
        ]);
    }

    public function active($id)
    {
        $account = Account::findOrFail($id);
        $account->status = 1;
        if ($account->save()) {
            return back()->with('message', "Account Active");
        }
    }

    public function deactive($id)
    {
        $account = Account::findOrFail($id);
        $account->status = 0;
        if ($account->save()) {
            return back()->with('message', "Account Deactived");
        }
    }
}
