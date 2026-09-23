<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\AssetCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        return view('asset.allAssets');
    }

    public function create()
    {
        return view('asset.partial.assetCreate', [
            'expense_types' => AssetCategory::query()->get(),
        ]);
    }

    public function store(Request $request)
    {
        Asset::insert([
            'asset_category_id' => $request->expense_type_id,
            'account_id' => $request->account_id,
            'pay_by' => $request->pay_by,
            'asset_date' => $request->expanse_date,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'note' => $request->note,
            'created_at' => Carbon::now(),
        ]);

        return response()->json("New Expense Added");
    }

    public function edit($id)
    {
        $expense = Asset::findOrFail($id);
        return view('asset.partial.assetEdit', [
            'expense_types' => AssetCategory::all(),
            'expense' => $expense,
            'data' => Account::where('account_type', $expense->pay_by)->get(),
        ]);
    }

    public function update(Request $request)
    {
        
        Asset::findOrFail($request->id)->update([
            'asset_category_id' => $request->expense_type_id,
            'account_id' => $request->account_id,
            'pay_by' => $request->pay_by,
            'asset_date' => $request->expanse_date,
            'amount' => $request->amount,
            'quantity' => $request->quantity,
            'note' => $request->note,
        ]);

        return response()->json([
            'message' => "Information Updated"
        ]);
    }

    public function destroy($id)
    {
        Asset::findOrFail($id)->delete();
        return response()->json([
            'message' => "Asset Deleted"
        ]);
    }

    public function allAssetJson()
    {
        $asset = Asset::join('asset_categories', 'assets.asset_category_id', "=", 'asset_categories.id')
            ->select('assets.*', 'asset_categories.asset_category')
            ->orderBy('id','desc')
            ->get();
        
        $totalAmount = $asset->sum('amount');
        
        return datatables()->of($asset)
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>";
               
                    $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit"><i class="fa fa-edit"></i> Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>';
                
                $btn .= "</div>";
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->with(['total_amount' => number_format($totalAmount, 2)])
            ->make(true);
    }

    public function account(Request $request)
    {
        // return $request->all();
        if ($request->account_type != "Cash") {
            return view('asset.partial.account', [
                'data' => Account::where('account_type', $request->account_type)->get(),
                'account_type' => $request->account_type,
            ]);
        }
    }
}
