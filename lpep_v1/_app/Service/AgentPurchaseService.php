<?php

namespace App\Service;

use Illuminate\Http\Request;
use App\Models\AgentPurchase;
use Illuminate\Support\Facades\DB;
use App\Models\AgentPurchaseProduct;
use App\Models\AgentCustomerTransaction;

class AgentPurchaseService extends PurchaseService
{
  public function index(Request $request)
  {
    $purchases = AgentPurchase::query()
      ->with('customer:id,name')
      ->when($request->purchase_date, fn ($query) => $query->where('purchase_date', $request->purchase_date))
      ->when($request->search, function ($query) use ($request) {
        $query->where('invoice_no', "like", "%{$request->search}%")
          ->orWhereHas('customer', function ($query) use ($request) {
            $query->where('name', "like", "%{$request->search}%");
          });
      })
      ->orderByDesc('id')
      ->paginate(25);

    return view('agent-purchase.index', [
      'purchases' => $purchases,
    ]);
  }

  public function create()
  {
    return view('agent-purchase.create', [
      'invoice_id' => AgentPurchase::nextInvoiceNo(),
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'app_customer_id' => 'required',
      'invoice_no' => 'required',
      'purchase_date' => 'required',
      'reg_product_id' => 'required',
      'reg_purchase_quantity' => 'required',
      'reg_purchase_price' => 'required',
      'reg_purchase_total' => 'required',
      'note' => 'nullable',
    ]);

    try {
      DB::beginTransaction();
      $purchase = AgentPurchase::query()->create([
        'agent_id' => auth()->id(),
        'app_customer_id' => $request->app_customer_id,
        'purchase_date' => $request->purchase_date,
        'invoice_no' => $request->invoice_no,
        'total' => array_sum($request->reg_purchase_total),
        'created_by' => auth()->id(),
      ]);

      $purchaseProducts = [];
      foreach ($request->reg_product_id as $key => $reg_product_id) {
        $purchaseProducts[] = [
          'agent_purchase_id' => $purchase->id,
          'product_id' => $reg_product_id,
          'batch_id' => $request->reg_batch_id[$key],
          'purchase_price' => $request->reg_purchase_price[$key],
          'quantity' => $request->reg_purchase_quantity[$key],
          'total_price' => $request->reg_purchase_total[$key],
          'created_at' => date('Y-m-d')
        ];
      }
      $purchase->purchaseProducts()->insert($purchaseProducts);

      $transaction = AgentCustomerTransaction::query()->create([
        'date' => $request->purchase_date,
        'invoice_no' => AgentCustomerTransaction::nextInvoiceNo(),
        'agent_id' => auth()->id(),
        'type' => TXN_RECEIVE,
        'app_customer_id' => $request->app_customer_id,
        'amount' => $purchase->total,
        'created_by' => auth()->id()
      ]);

      $transaction->details()->create([
        'purchase_id' => $purchase->id,
        'amount' => $purchase->total,
      ]);

      DB::commit();
      return redirect()->route('purchase.index')->with('message', "Success");
    } catch (\Exception $e) {
      DB::rollback();
    }

    return  back()->with([
      'type' => 'error',
      'message' => "Something went wrong"
    ]);
  }

  public function show($id)
  {
    return view('agent-purchase.partial.details', [
      'purchase' => AgentPurchase::query()
        ->with('purchaseProducts.product', 'customer', 'createdBy', 'updatedBy')
        ->findOrFail($id)
    ]);
  }

  public function edit($id)
  {
    $purchase = AgentPurchase::query()->with('customer', 'purchaseProducts.product')->findOrFail($id);

    return view('agent-purchase.edit', [
      'purchase' => $purchase,
      'loan_amount' => getCustomerFinalLoanAmount($purchase->app_customer_id)
    ]);
  }

  public function update(Request $request)
  {
    try {
      DB::beginTransaction();

      $purchase = AgentPurchase::findOrFail($request->purchase_id);

      foreach ($request->reg_product_id ?? [] as $key => $product_id) {
        if ($request->reg_old_quantity[$key] > $request->reg_purchase_quantity[$key]) {
          if (checkStockQuantity($product_id, $request->reg_purchase_quantity[$key]) === false) {
            return back()->with([
              'type' => 'error',
              'message' => "NOT ALLOWED : Mismatch between transfer and purchase quantity"
            ]);
          }
        }
      }

      $purchase->app_customer_id = $request->app_customer_id;
      $purchase->purchase_date = $request->purchase_date;
      $purchase->invoice_no = $request->invoice_no;
      $purchase->note = $request->note;
      $purchase->total = $request->total;

      if ($request->hasFile('attachment')) {
        $path = $request->attachment->store('uploads/attachment', ['disk' => 'public_uploads']);
        $purchase->attachment = json_encode($path);
      }
      $purchase->save();

      $purchase->purchaseProducts()
        ->whereNotIn('id', array_merge($request->reg_purchase_product_id ?? [], $request->med_purchase_product_id ?? []))
        ->delete();

      foreach ($request->reg_product_id ?? [] as $product_key => $product_id) {
        if (array_key_exists($product_key, $request->reg_purchase_product_id ?? [])) {
          $purchaseProduct = AgentPurchaseProduct::query()->find($request->reg_purchase_product_id[$product_key]);
          $purchaseProduct->update([
            'product_id' => $product_id,
            'agent_purchase_id' => $request->purchase_id,
            'batch_id' => $request->reg_batch_id[$product_key],
            'quantity' => $request->reg_purchase_quantity[$product_key],
            'purchase_price' => $request->reg_purchase_price[$product_key],
            'total_price' => $request->reg_purchase_total[$product_key],
          ]);
        }
      }

      DB::commit();
      return redirect()->route('purchase.index')->with('message', "Purchase Updated Successfully");
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->route('purchase.index')->with('error', "Purchase update error");
    }
  }

  public function destroy($id)
  {
    try {
      AgentPurchase::query()
        ->where('id', $id)
        ->delete();
      return back()->with('message', 'Success');
    } catch (\Exception $e) {
      return back()->with('type', 'error')->with('message', 'Stock Mismatch');
    }
  }

  public function invoice($id)
  {
    $purchase = AgentPurchase::query()
      ->with('purchaseProducts.product')
      ->findOrFail($id);

    return view('agent-purchase.invoice', [
      'purchase' => $purchase,
    ]);
  }
}
