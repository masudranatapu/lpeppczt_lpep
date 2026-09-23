<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransfer;
use App\Models\PurchaseProduct;
use App\Models\Product;
use App\Models\Agent;

class AgentToAgentTransferController extends Controller
{
        
    public function createAgentToAgent(Request $request)
    {
        $fromAgentId = auth()->user()->id;
    
        if (!$fromAgentId) {
            abort(403, 'Agent not found for the current user.');
        }
    
        return view('agent-to-agent-transfer.index', [
            'fromAgentId' => $fromAgentId,
            'agents'      => getCachedAgents(), // reuse your helper
            'today'       => date('Y-m-d'),
        ]);
    }
    
    public function agentTransferAutocomplete(Request $request)
    {
        $q = trim($request->get('q', ''));
        $fromAgentId = optional(auth()->user()->agent)->id ?? (int) $request->get('from_agent_id');
        if (!$fromAgentId) return response()->json([]);
    
        // Inbound to this agent
        $inbound = DB::table('stock_transfer_details as d')
            ->join('stock_transfers as t', 't.id', '=', 'd.stock_transfer_id')
            ->select('d.purchase_product_id', DB::raw('SUM(d.quantity) as in_qty'))
            ->where('t.agent_id', $fromAgentId)
            ->groupBy('d.purchase_product_id');
    
        $outbound = DB::table('stock_transfer_details as d')
            ->join('stock_transfers as t', 't.id', '=', 'd.stock_transfer_id')
            ->select('d.purchase_product_id', DB::raw('SUM(d.quantity) as out_qty'))
            ->where('t.created_by', auth()->id())
            ->where('t.agent_id', '!=', $fromAgentId)
            ->groupBy('d.purchase_product_id');
    
        $rows = DB::table('purchase_products as pp')
            ->joinSub($inbound, 'inb', fn($j) => $j->on('inb.purchase_product_id', '=', 'pp.id'))
            ->leftJoinSub($outbound, 'outb', fn($j) => $j->on('outb.purchase_product_id', '=', 'pp.id'))
            ->join('products as p', 'p.id', '=', 'pp.product_id')
            ->select(
                'pp.id as purchase_product_id',
                'pp.product_id',
                'p.product_name as product_name',
                DB::raw('COALESCE(inb.in_qty,0) - COALESCE(outb.out_qty,0) as available_qty')
            )
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where('p.product_name', 'like', "%{$q}%");
            })
            ->whereRaw('(COALESCE(inb.in_qty,0) - COALESCE(outb.out_qty,0)) > 0')
            ->orderBy('p.product_name', 'asc')
            ->limit(30)
            ->get();
    
        $payload = $rows->map(function ($r) {
            return [
                'product_id'          => (int) $r->product_id,
                'purchase_product_id' => (int) $r->purchase_product_id,
                'label'               => $r->product_name.' — Batch: PP#'.$r->purchase_product_id,
                'product_name'        => $r->product_name,
                'available_qty'       => (float) $r->available_qty,
            ];
        });
    
        return response()->json($payload);
    }
    
    
    
    public function storeAgentToAgent(Request $request)
    {
        $request->validate([
            'to_agent_id'           => 'required|integer',
            'date'                  => 'required|date',
            'product_id'            => 'required|array|min:1',
            'product_id.*'          => 'required|integer',
            'quantity'              => 'required|array|min:1',
            'quantity.*'            => 'required|numeric|min:1',
            'purchase_product_id'   => 'required|array|min:1',
            'purchase_product_id.*' => 'required|integer',
            'from_agent_id'         => 'required|integer',
        ]);
    
        $fromAgentId = (int) $request->from_agent_id;
    
        if ($fromAgentId === (int) $request->to_agent_id) {
            return back()->with('type', 'error')->with('message', 'You cannot transfer to the same agent.');
        }
    
        // optional: ensure row counts align
        if (
            count($request->product_id) !== count($request->purchase_product_id) ||
            count($request->product_id) !== count($request->quantity)
        ) {
            return back()->with('type', 'error')->with('message', 'Mismatched line-item arrays.');
        }
    
        try {
            DB::beginTransaction();
    
            $transfer = \App\Models\StockTransfer::query()->create([
                'invoice_no'     => \App\Models\StockTransfer::nextInvoiceNo(),
                'date'           => $request->date,
                'agent_id'       => $request->to_agent_id,          // receiver
                'total_quantity' => array_sum($request->quantity),
                'created_by'     => auth()->id(),
            ]);
    
            foreach ($request->product_id as $i => $productId) {
                $batchId = (int) $request->purchase_product_id[$i];
                $moveQty = (float) $request->quantity[$i];
    
                $inQty = DB::table('stock_transfer_details as d')
                    ->join('stock_transfers as t', 't.id', '=', 'd.stock_transfer_id')
                    ->where('t.agent_id', $fromAgentId)                 // stock that ended up with this agent
                    ->where('d.purchase_product_id', $batchId)
                    ->sum(DB::raw('d.available_quantity'));             // quantity - sold_quantity
    
                $outQty = DB::table('stock_transfer_details as d')
                    ->join('stock_transfers as t', 't.id', '=', 'd.stock_transfer_id')
                    ->where('t.created_by', auth()->id())
                    ->where('t.agent_id', '!=', $fromAgentId)           // to someone else
                    ->where('d.purchase_product_id', $batchId)
                    ->sum('d.quantity');
    
                $available = (float) $inQty - (float) $outQty;
    
                if ($available < $moveQty) {
                    DB::rollBack();
                    return back()
                        ->with('type', 'error')
                        ->with('message', "Insufficient stock for batch PP#{$batchId}. Available: {$available}, requested: {$moveQty}");
                }
    
                $pp = \App\Models\PurchaseProduct::findOrFail($batchId);
                if ((int) $pp->product_id !== (int) $productId) {
                    DB::rollBack();
                    return back()
                        ->with('type', 'error')
                        ->with('message', "Batch PP#{$batchId} does not belong to the selected product.");
                }
    
                // Create detail
                $transfer->details()->create([
                    'stock_transfer_id'   => $transfer->id,
                    'product_id'          => $productId,
                    'purchase_product_id' => $batchId,
                    'quantity'            => $moveQty,
                ]);
            }
    
            DB::commit();
            return redirect()->back()->with('message', 'Agent to agent transfer successful.');
            // return redirect()
            //     ->route('stock-transfer.index', ['type' => 'transferred'])
            //     ->with('message', 'Agent to agent transfer successful.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->with('type', 'error')
                ->with('message', 'Couldn\'t Transfer: '.$e->getMessage());
        }
    }

}
