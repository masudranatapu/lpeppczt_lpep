<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentStockTransfer;
use Illuminate\Support\Facades\DB;
use App\Models\AgentPurchaseProduct;
use App\Http\Requests\AgentStockTransferRequest;

class AgentStockTransferController extends Controller
{

    public function index(Request $request)
    {
        $transfers = AgentStockTransfer::query()
            ->where('agent_id', auth()->id())
            ->when($request->date, fn ($q) => $q->where('date', $request->date))
            ->when($request->search, fn ($q) => $q->where('invoice_no', $request->search)->orWhere('total_quantity', $request->search))
            ->with('agent:id,name')
            ->orderByDesc('id')
            ->paginate(20);

        $totalQuantity = AgentStockTransfer::query()
            ->where('agent_id', auth()->id())
            ->when($request->date, fn ($q) => $q->where('date', $request->date))
            ->when($request->search, fn ($q) => $q->where('invoice_no', $request->search)->orWhere('total_quantity', $request->search))
            ->sum('total_quantity');

        return view("stock-transfer.agent.index", compact('transfers', 'totalQuantity'));
    }

    public function create()
    {
        return view("stock-transfer.agent.create");
    }

    public function store(AgentStockTransferRequest $request)
    {
        try {
            DB::beginTransaction();

            $transfer = AgentStockTransfer::query()
                ->create([
                    'invoice_no' => AgentStockTransfer::nextInvoiceNo(),
                    'date' => date("Y-m-d"),
                    'agent_id' => auth()->id(),
                    'total_quantity' => array_sum($request->quantity),
                ]);

            foreach ($request->product_id as $key => $product_id) {
                $purchaseProduct = AgentPurchaseProduct::query()
                    ->where('id', $request->purchase_product_id[$key])
                    ->withSum('agentStockTransferDetails as transfer_qty', 'quantity')
                    ->addSelect([
                        'final_quantity' => function ($q) {
                            $q->selectRaw('(quantity) - COALESCE(transfer_qty, 0)');
                        }
                    ])
                    ->havingRaw('final_quantity >= ' . $request->quantity[$key])
                    ->first();

                $transfer->details()->create([
                    'agent_stock_transfer_id' => $transfer->id,
                    'product_id' => $product_id,
                    'agent_purchase_product_id' => $purchaseProduct->id,
                    'quantity' => $request->quantity[$key]
                ]);
            }

            DB::commit();
            return redirect()->route('agent-stock-transfer.index')->with('message', 'Success');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }

        return back()->with('type', 'error')->with('message', 'Couldn\'t Transfer');
    }

    public function show(Request $request, $id)
    {
        $transfer = AgentStockTransfer::query()
            ->with('details.product', 'details.agentPurchaseProduct')
            ->findOrFail($id);
        return view('stock-transfer.agent.show', compact('transfer'));
    }
}
