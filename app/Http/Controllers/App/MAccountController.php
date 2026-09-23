<?php

namespace App\Http\Controllers\App;

use App\Models\MAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MAccountController extends Controller
{
    public function index(Request $request)
    {
        $mname = $request->input('month');
        $yname = $request->input('year');

        // dd($request->all());

        $income = MAccount::query()->where('type', 'Income')->sum('total_amount');
        $expense = MAccount::query()->where('type', 'Expense')->sum('total_amount');
        if($request->input('month') != '' || $request->input('month') != null){
            $balace = $this->filterByMonthName($mname, $yname) ?? "No Data Found";

        }else{
            $balace = $income - $expense;
        }

        return view('m-accounts.index',compact('balace'));
    }
    private function filterByMonthName($monthName, $year)
    {
        // Convert the month name to a month number
        // $monthNumber = Carbon::parse($monthName)->month;

        $income = MAccount::query()
            ->whereMonth('date', $monthName)
            ->whereYear('date', $year)
            ->where('type', 'Income')->sum('total_amount');
        $expense = MAccount::query()
            ->whereMonth('date', $monthName)
            ->whereYear('date', $year)
            ->where('type', 'Expense')->sum('total_amount');
        $balace = $income - $expense;
        return $balace;
    }

    public function balance(){
        $income = MAccount::query()->where('type', 'Income')->sum('total_amount');
        $expense = MAccount::query()->where('type', 'Expense')->sum('total_amount');

        return $income - $expense;
    }
    public function create()
    {
        return view('m-accounts.create');
    }

    public function store(Request $request): JsonResponse
    {
        MAccount::query()
            ->create([
                'name' => $request->name,
                'unit' => $request->unit,
                'quantity' => $request->quantity,
                'total_amount' => $request->total_amount,
                'date' => $request->date,
                'type' => $request->type,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Account saved successfully!');
    }

    public function edit($id)
    {
        $mAccount = MAccount::query()->findOrFail($id);

        return view('m-accounts.edit', compact('mAccount'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        MAccount::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'unit' => $request->unit,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'total_amount' => $request->total_amount,
                'date' => $request->date,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Account updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        try {
            MAccount::query()->where('id', $id)->delete();
        } catch (\Exception $e) {
            return response()->json('Account couldn\'t be deleted!');
        }

        return response()->json('Account deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            MAccount::query()
                ->with('createdBy', 'updatedBy')
        )
            ->addColumn('bn_type', function ($data) {
                return $data->type == 'Income' ? 'আয়' : 'ব্যয়';
            })
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>" . $data->log();
                if (permission('uni3')) {
                    $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="tableEdit">Edit</a>';
                }
                if (permission('uni4')) {
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
