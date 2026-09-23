<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AdminAgentTransaction;
use App\Models\AgentCustomerTransaction;
use App\Models\AppCustomer;

class AgentCustomerTransactionController extends Controller
{
    public function index()
    {
        $availablePoints = $this->totalAvailableAgentPoints(auth()->id());

        return view('customer-points.index', compact('availablePoints'));
    }

    public function create()
    {
        $availablePoints = $this->totalAvailableAgentPoints(auth()->id());

        return view('customer-points.create', compact('availablePoints'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'assigned_points' => 'required',
        ]);

        $availableAmount = $this->totalAvailableAgentPoints(auth()->id());

        if ($request->assigned_points > $availableAmount) {
            return response()->json(['error' => 'Request amount is higher than available amount'], 404);
        }

        try {
            DB::beginTransaction();

            $transaction = AgentCustomerTransaction::query()->create([
                'date' => date('Y-m-d'),
                'invoice_no' => AgentCustomerTransaction::nextInvoiceNo(),
                'type' => 'Send',
                'agent_id' => auth()->id(),
                'app_customer_id' => $request->customer_id,
                'amount' => $request->assigned_points,
                'note' => $request->note,
                'created_by' => auth()->id(),
            ]);

            $requiredAmount = $request->assigned_points;
            $adminAgentTransactions = AdminAgentTransaction::query()->where('available_amount', '>', 0)->get();

            foreach ($adminAgentTransactions as $adminAgentTransaction) {
                $amount = min($adminAgentTransaction->amount, $requiredAmount);

                $transaction->details()->create([
                    'admin_agent_transaction_id' => $adminAgentTransaction->id,
                    'amount' => $amount
                ]);

                $adminAgentTransaction->increment('loan_amount', $amount);

                $requiredAmount -= $amount;
                if ($requiredAmount == 0)
                    break;
            }

            DB::commit();

            return response()->json(['message' => 'Customer lending success!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Customer lending error!']);
        }
    }

    public function destroy($id)
    {
        $transaction = AgentCustomerTransaction::query()->findOrFail($id);

        if ($transaction->used_amount > 0) {
            return response()->json(['error' => 'Transaction exists under this record']);
        }

        $transaction->delete();
        return response()->json(['message' => 'Successful']);
    }

    public function allAsJson()
    {
        return datatables()->of(
            AgentCustomerTransaction::query()->with('customer', 'createdBy', 'updatedBy')
        )
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>" . $data->log();
                $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function totalAvailableAgentPoints($user_id)
    {
        $agent = User::query()
            ->select('id')
            ->withSum('receivedLoansFromAdmin as received_loans_from_admin', 'amount')
            ->withSum('sentLoansToCustomers as sent_loans_to_customers', 'amount')
            ->find($user_id);

        return $agent->received_loans_from_admin - $agent->sent_loans_to_customers;
    }

    public function customerFinalLoanAmount($customer_id)
    {
        return response()->json([
            'amount' => number_format(
                getCustomerFinalLoanAmount($customer_id),
                2
            )
        ]);
    }

    public function customerAvailablePoints($customer_id)
    {
        return response()->json([
            'points' => number_format(
                AgentCustomerTransaction::query()
                    ->where('app_customer_id', $customer_id)
                    ->where('type', TXN_SEND)
                    ->sum('available_amount'),
                2
            )
        ]);
    }
}
