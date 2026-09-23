<?php

namespace App\Http\Controllers;

use App\Models\AdminDeposit;
use App\Models\Deposit;
use Exception;
use Illuminate\Http\Request;

class AdminDepositController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->json($request);
        }

        return view('admin-deposits.index');
    }

    public function json(Request $request)
    {
        return datatables()->of(
            AdminDeposit::query()
                ->orderByDesc('id')
                ->get()
        )
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn btn-group'>" . $data->log();
                $btn .= "<button type='button' class='btn btn-sm btn-primary' title='Edit' id='editDeposit' data-id='{$data->id}'><i class='fa fa-edit'></i></button>";
                $btn .= "<button type='button' class='btn btn-sm btn-danger' title='Delete' id='deleteData' data-id='{$data->id}'><i class='fa fa-trash'></i></button>";
                $btn .= "</div>";
                return $btn;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function create(Request $request)
    {
        return view('admin-deposits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'amount' => 'required',
            'note' => 'nullable'
        ]);

        AdminDeposit::query()
            ->create([
                'date' => $request->date,
                'invoice_no' => AdminDeposit::nextInvoiceNo(),
                'amount' => $request->amount,
                'created_by' => auth()->id(),
                'note' => $request->note
            ]);

        return response()->json([
            'message' => "Success"
        ]);
    }

    public function edit($id)
    {
        return view('admin-deposits.edit', [
            'deposit' => AdminDeposit::query()->find($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required',
            'amount' => 'required',
            'note' => 'nullable'
        ]);

        AdminDeposit::query()
            ->find($id)
            ->update([
                'date' => $request->date,
                'amount' => $request->amount,
                'updated_by' => auth()->id(),
                'note' => $request->note
            ]);

        return response()->json([
            'message' => "Success"
        ]);
    }


    public function destroy($id)
    {
        $deposit = AdminDeposit::query()->find($id);

        if ($deposit->loan_amount > 0) {
            return response()->json([
                'error' => "Error. Collect " . $deposit->loan_amount . " first!"
            ]);
        }

        $deposit->delete();

        return response()->json([
            'message' => "Success"
        ]);
    }
}
