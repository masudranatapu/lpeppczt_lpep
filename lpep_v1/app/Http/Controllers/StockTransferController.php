<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseProduct;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'type' => 'required|in:transferred,received',
            'agent_id' => 'nullable',
            'start' => 'nullable',
            'end' => 'nullable',
            'search' => 'nullable',
        ]);



        $transfers = StockTransfer::filterTransfers($request)
            ->with(['agent:id,name', 'products'])
            ->orderByDesc('id')
            ->paginate(40);

        $totalQuantity = StockTransfer::filterTransfers($request)->sum('total_quantity');


        // $transfers = StockTransfer::query()
        //             ->when($request->type == 'received', fn ($q) => $q->where('agent_id', auth()->id()))
        //             ->when(!is_null($request->agent_id), function ($q) use ($request) {
        //                 $q->where('agent_id', $request->agent_id);
        //             })
        //             ->when($request->start && $request->end, function ($q) use ($request) {
        //                 $q->filterByDate($request->start, $request->end);
        //             })
        //             ->when($request->search, function ($q) use ($request) {
        //                 $q->where(function ($subQuery) use ($request) {
        //                     $subQuery->where('invoice_no', 'like', "%{$request->search}%")
        //                             ->orWhere('total_quantity', 'like', "%{$request->search}%")
        //                             ->orWhereHas('products', function ($q2) use ($request) {
        //                                 $q2->where('product_name', 'like', "%{$request->search}%");
        //                             });
        //                 });
        //             })
        //             ->with(['agent:id,name', 'products'])
        //             ->orderByDesc('id')
        //             ->paginate(40);


        // $totalQuantity = StockTransfer::query()
        //                 ->when($request->type == 'received', fn ($q) => $q->where('agent_id', auth()->id()))
        //                 ->when(!is_null($request->agent_id), function ($q) use ($request) {
        //                     $q->where('agent_id', $request->agent_id);
        //                 })
        //                 ->when($request->start && $request->end, function ($q) use ($request) {
        //                     $q->filterByDate($request->start, $request->end);
        //                 })
        //                 ->when($request->search, function ($q) use ($request) {
        //                     $q->where(function ($subQuery) use ($request) {
        //                         $subQuery->where('invoice_no', 'like', "%{$request->search}%")
        //                                 ->orWhere('total_quantity', 'like', "%{$request->search}%")
        //                                 ->orWhereHas('products', function ($q2) use ($request) {
        //                                     $q2->where('product_name', 'like', "%{$request->search}%");
        //                                 });
        //                     });
        //                 })->sum('total_quantity');

        return view('stock-transfer.index', compact('transfers', 'totalQuantity'));
    }

    public function create(Request $request)
    {
        return view('stock-transfer.create');
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'agent_id' => 'required',
            'date' => 'required',
            'product_id' => 'required',
            'product_id.*' => 'required',
            'quantity' => 'required',
            'quantity.*' => 'required|numeric|min:1',
            'purchase_product_id' => 'required',
            'purchase_product_id.*' => 'required',
        ], [
            'product_id' => 'Product is required.',
            'product_id.*' => 'Product is required.',
            'quantity' => 'Quantity is required.',
            'quantity.*' => 'Transfer Quantity is required.',
            'purchase_product_id' => 'Batch is required.',
            'purchase_product_id.*' => 'Batch is required.',
        ]);

        try {
            DB::beginTransaction();

            $transfer = StockTransfer::query()
                ->create([
                    'invoice_no' => StockTransfer::nextInvoiceNo(),
                    'date' => $request->date,
                    'agent_id' => $request->agent_id,
                    'total_quantity' => array_sum($request->quantity),
                    'created_by' => auth()->user()->id,
                ]);

            foreach ($request->product_id as $key => $product_id) {
                $purchaseProduct = PurchaseProduct::query()
                                ->where('id', $request->purchase_product_id[$key])
                                ->withSum('stockTransferDetails as transfer_qty', 'quantity')
                                ->addSelect([
                                    'final_quantity' => function ($q) {
                                        $q->selectRaw('COALESCE(quantity) - COALESCE(transfer_qty, 0)');
                                    }
                                ])
                                ->havingRaw('final_quantity >= ' . $request->quantity[$key])
                                //old one == ->havingRaw('final_quantity > ' . $request->quantity[$key])
                                ->firstOrFail();


                $transfer->details()->create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $product_id,
                    'purchase_product_id' => $purchaseProduct->id,
                    'quantity' => $request->quantity[$key]
                ]);
            }

            DB::commit();
            return redirect()->route('stock-transfer.index', ['type' => 'transferred'])->with('message', 'Success');
        } catch (\Exception $e) {
            DB::rollback();
        }

        return back()->with('type', 'error')->with('message', 'Couldn\'t Transfer');
    }


    public function edit(StockTransfer $stockTransfer)
    {

        $data =  $stockTransfer->load(['details','details.product','details.purchaseProduct']);
        return $data;
        return view('stock-transfer.edit',[
            'transfer' => $data
        ]);
    }

    public function show(Request $request, $id)
    {
        $transfer = StockTransfer::query()
            ->with('details.product', 'details.purchaseProduct')
            ->findOrFail($id);

        return view('stock-transfer.show', compact('transfer'));
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transfer = StockTransfer::query()->with('details')->find($id);

            foreach ($transfer->details as $detail) {
                $product = Product::query()
                    ->select('id')
                    ->withStockProperties($transfer->agent_id)
                    ->find($detail->product_id);

                $stockQuantity = getCurrentStockQuantityFromProduct($product, true);

                if ($stockQuantity < $detail->quantity) {
                    DB::rollBack();
                    return back()->with('type', 'error')->with('message', 'Receiver Product Stock Quantity is Insufficient');
                } else {
                    $detail->delete();
                }
            }

            $transfer->delete();
            DB::commit();
            return back()->with('message', 'Success');
        } catch (\Exception $e) {
            DB::rollback();
        }

        return back()->with('type', 'error')->with('message', 'An unknown error occurred');
    }
}
