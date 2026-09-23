<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\AdminAgentTransaction;
use App\Models\AdminDeposit;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminAgentTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(
                AdminAgentTransaction::query()->with('agent:id,name')->get()
            )
                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>" . $data->log();
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'
                        . $data->id
                        . '" id="deleteData" class="btn btn-danger btn-sm '
                        . ($data->customer_point_details_count > 0 ? 'disabled' : '')
                        . '">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('agent-points.index');
    }

    public function create()
    {
        $agents = User::query()
            ->whereHas('userPermission', function ($q) {
                $q->where('role_id', ROLE_AGENT);
            })
            ->select('id', 'name')
            ->get();

        return view('agent-points.create', compact('agents'));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $adminDeposits = AdminDeposit::query()
                ->where("available_amount", ">", 0)
                ->get();

            if ($adminDeposits->sum('available_amount') < $request->assigned_points)
                return response()->json(['error' => 'Request amount is higher than deposit amount'], 404);

            $AdminAgentTransaction = AdminAgentTransaction::query()->create([
                'date' => date("Y-m-d"),
                'invoice_no' => AdminAgentTransaction::nextInvoiceNo(),
                'type' => "Send",
                'agent_id' => $request->agent_id,
                'amount' => $request->assigned_points,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            $agentPoint = $request->assigned_points;

            foreach ($adminDeposits as $adminDeposit) {
                $amount = min($adminDeposit->available_amount, $agentPoint);

                $AdminAgentTransaction->details()
                    ->create([
                        "admin_deposit_id" => $adminDeposit->id,
                        "amount" => $amount
                    ]);

                AdminDeposit::query()
                    ->find($adminDeposit->id)
                    ->increment("loan_amount", $amount);

                $agentPoint -= $amount;
                if ($agentPoint == 0) break;
            }
            DB::commit();
            return response()->json('Transaction successful!');
        } catch (Exception $e) {
            DB::rollBack();
        }

        return response()->json(['error' => 'Transaction error!']);
    }

    public function destroy($id)
    {
        AdminAgentTransaction::query()->where('id', $id)->delete();
        return response()->json('Success');
    }
}
