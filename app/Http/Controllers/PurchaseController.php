<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AgentPurchaseProduct;
use App\Models\PurchaseProduct;
use App\Service\AdminPurchaseService;
use App\Service\AgentPurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    private function service()
    {
        return isRole(ROLE_AGENT) ? new AgentPurchaseService : new AdminPurchaseService;
    }

    public function index(Request $request)
    {
        return $this->service()->index($request);
    }

    public function create()
    {
        return $this->service()->create();
    }

    public function store(Request $request)
    {
        return $this->service()->store($request);
    }

    public function show($id)
    {
        return $this->service()->show($id);
    }

    public function edit($id)
    {
        return $this->service()->edit($id);
    }

    public function update(Request $request)
    {
        return $this->service()->update($request);
    }

    public function destroy($id)
    {
        return $this->service()->destroy($id);
    }

    public function invoice($id)
    {
        return $this->service()->invoice($id);
    }

    public function account(Request $request)
    {
        if ($request->account_type != "Cash") {
            return view('purchase.partial.account', [
                'data' => Account::where('account_type', $request->account_type)->get(),
                'account_type' => $request->account_type,
            ]);
        }
    }

    public function doesBatchIdExist(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
            'except' => 'nullable'
        ]);

        return response()->json([
            'exists' => (PurchaseProduct::query()
                ->when($request->except, function ($q) use ($request) {
                    $q->where('batch_id', '<>', $request->batch_id);
                })
                ->when(!$request->except, function ($q) use ($request) {
                    $q->where('batch_id', $request->batch_id);
                })
                ->count() +
                AgentPurchaseProduct::query()
                ->when($request->except, function ($q) use ($request) {
                    $q->where('batch_id', '<>', $request->batch_id);
                })
                ->when(!$request->except, function ($q) use ($request) {
                    $q->where('batch_id', $request->batch_id);
                })
                ->count()) > 0
        ]);
    }
}
