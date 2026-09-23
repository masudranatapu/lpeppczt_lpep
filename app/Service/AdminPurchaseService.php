<?php

namespace App\Service;

use Exception;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\BoxPattern;
use Illuminate\Http\Request;
use App\Models\ContactPayment;
use App\Models\PurchaseProduct;
use Illuminate\Support\Facades\DB;

class AdminPurchaseService extends PurchaseService
{
  public function index(Request $request)
  {

    $data = Purchase::query()
      ->with(['supplier', 'purchaseReturn','products'])
      ->when($request->purchase_date, fn ($query) => $query->where('purchase_date', $request->purchase_date))
      ->when($request->search, function ($query) use ($request) {
        $query->where('invoice_no', "like", "%{$request->search}%")
          ->orWhereHas('supplier', function ($query) use ($request) {
            $query->where('name', "like", "%{$request->search}%");
          })
          ->orWhereHas('products', function ($query) use ($request) {
            $query->where('product_name', 'like', "%{$request->search}%");
        });

      });

      $purchases = $data->orderByDesc('id')->paginate(40);

        return view('purchase.index', [
            'purchases' => $purchases,
            // 'data' =>  $data
        ]);
    }

  public function create()
  {
     
    return view('purchase.create', [
      'invoice_id' => Purchase::nextInvoiceNo(),
      'contacts' => Contact::query()->where('type', 'supplier')->get(),
      'box_patterns' => BoxPattern::query()->get()->each(function ($b) {
        $b->name .= " ({$b->quantity})";
      }),
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'contact_id' => 'required',
      'invoice_no' => 'required',
      'purchase_date' => 'required',
      'total' => 'required',
      'pay_by' => 'required',
      'paying_amount' => 'required'
    ]);

    if (isset($request->reg_product_id) || isset($request->med_product_id)) {
      try {
        DB::beginTransaction();

        $purchase = new Purchase();
        $purchase->contact_id = $request->contact_id;
        $purchase->purchase_date = $request->purchase_date;
        $purchase->invoice_no = $request->invoice_no;
        $purchase->note = $request->note;
        $purchase->total = $request->total;
        $purchase->total_pay = $request->paying_amount;
        $purchase->created_by = auth()->id();
        if ($request->hasFile('attachment')) {
          $path = $request->attachment->store('uploads/attachment', ['disk' => 'public_uploads']);
          $purchase->attachment = json_encode($path);
        }
        $purchase->save();

        foreach ($request->reg_product_id ?? [] as $product_key => $product_id) {
          $purchase->purchaseProducts()
            ->create([
              'product_id' => $product_id,
              'batch_id' => $request->reg_batch_id[$product_key],
              'quantity' => $request->reg_purchase_quantity[$product_key],
              'purchase_price' => $request->reg_purchase_price[$product_key],
              'subtotal_price' => $request->reg_purchase_subtotal[$product_key],
              'other_cost' => $request->reg_purchase_other_cost[$product_key],
              'total_price' => $request->reg_purchase_total[$product_key],
            ]);
        }

        $boxPatterns = collect(BoxPattern::query()->whereIn('id', $request->med_box_pattern ?? [])->get());
        foreach ($request->med_product_id ?? [] as $product_key => $product_id) {
          $purchase->purchaseProducts()
            ->create([
              'product_id' => $product_id,
              'batch_id' => $request->med_batch_id[$product_key],
              'expiry_date' => $request->med_expiry_date[$product_key],
              'box_pattern_id' => $request->med_box_pattern[$product_key],
              'box_pattern_quantity' => $request->med_box_pattern_quantity[$product_key],
              'quantity' => $request->med_box_pattern_quantity[$product_key] * $boxPatterns->where('id', $request->med_box_pattern[$product_key])->first()->quantity,
              'purchase_price' => $request->med_purchase_price[$product_key],
              'subtotal_price' => $request->med_purchase_subtotal[$product_key],
              'other_cost' => $request->med_purchase_other_cost[$product_key],
              'total_price' => $request->med_purchase_total[$product_key],
            ]);
        }

        if ($request->paying_amount) {
          $contact_payment = new ContactPayment();
          $contact_payment->contact_id = $request->contact_id;
          $contact_payment->purchase_id = $purchase->id;
          $contact_payment->paying_amount = $request->paying_amount;
          $contact_payment->pay_by = $request->pay_by;
          $contact_payment->account_id = $request->account_id;
          $contact_payment->paying_date = $request->purchase_date;
          $contact_payment->save();
        }

        DB::commit();
        return redirect()->route('purchase.index')->with('message', "Product Purchase Successfully");
      } catch (Exception $e) {
        DB::rollBack();
        return  back()->with([
          'type' => 'error',
          'message' => "Something went wrong"
        ]);
      }
    } else {
      return  back()->with([
        'type' => 'error',
        'message' => "Product Select First"
      ]);
    }
  }

  public function show($id)
  {
    return view('purchase.partial.details', [
      'purchase' => Purchase::query()
        ->with('purchaseProducts.product', 'purchaseProducts.boxPattern', 'createdBy', 'updatedBy')
        ->findOrFail($id)
    ]);
  }

  public function edit($id)
  {
    $purchase = Purchase::query()->with('purchaseProducts.product')->findOrFail($id);

    $data = ['box_patterns' => BoxPattern::query()->get()];
    if (isset($purchase->supplierPayment->pay_by)) {
      $data['data'] = Account::where('account_type', $purchase->supplierPayment->pay_by)->get();
    }

    return view('purchase.edit', array_merge([
      'contacts' => Contact::where('type', '=', 'supplier')->get(),
      'purchase' => $purchase,
    ], $data));
  }

  public function update(Request $request)
  {
    try {
      DB::beginTransaction();

      if ($request->contact_payment_id) {
        $contact_payment = ContactPayment::findOrFail($request->contact_payment_id);
      } else {
        $contact_payment = new ContactPayment();
      }

      $purchase = Purchase::findOrFail($request->purchase_id);

      foreach ($request->reg_product_id ?? [] as $key => $product_id) {
        if (isset($request->reg_old_quantity[$key]) > isset($request->reg_purchase_quantity[$key])) {
          if (checkStockQuantity($product_id, $request->reg_purchase_quantity[$key]) === false) {
            return back()->with([
              'type' => 'error',
              'message' => "NOT ALLOWED : Mismatch between sold and purchase quantity"
            ]);
          }
        }
      }

      $boxPatterns = collect(BoxPattern::query()->whereIn('id', $request->med_box_pattern ?? [])->get());
      foreach ($request->med_product_id ?? [] as $key => $product_id) {
        if (isset($request->med_old_quantity[$key]) > isset($request->med_box_pattern_quantity[$key])) {
          if (checkStockQuantity(
            $product_id,
            $request->med_box_pattern_quantity[$key] * $boxPatterns->where('id', $request->med_box_pattern[$key])->first()->quantity
          ) === false) {
            return back()->with([
              'type' => 'error',
              'message' => "NOT ALLOWED : Mismatch between sold and purchase quantity"
            ]);
          }
        }
      }

      $purchase->contact_id = $request->contact_id;
      $purchase->purchase_date = $request->purchase_date;
      $purchase->invoice_no = $request->invoice_no;
      $purchase->note = $request->note;
      $purchase->total = $request->total;
      $purchase->total_pay = $request->paying_amount;
      $purchase->updated_by = auth()->id();

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
          $purchaseProduct = PurchaseProduct::query()->find($request->reg_purchase_product_id[$product_key]);
          $purchaseProduct->update([
            'product_id' => $product_id,
            'purchase_id' => $request->purchase_id,
            'batch_id' => $request->reg_batch_id[$product_key],
            'quantity' => $request->reg_purchase_quantity[$product_key],
            'purchase_price' => $request->reg_purchase_price[$product_key],
            'subtotal_price' => $request->reg_purchase_subtotal[$product_key],
            'other_cost' => $request->reg_purchase_other_cost[$product_key],
            'total_price' => $request->reg_purchase_total[$product_key],
          ]);
        }
      }

      foreach ($request->med_product_id ?? [] as $product_key => $product_id) {
        if (array_key_exists($product_key, $request->med_purchase_product_id ?? [])) {
          $purchaseProduct = PurchaseProduct::query()->find($request->med_purchase_product_id[$product_key]);
          $purchaseProduct->update([
            'product_id' => $product_id,
            'purchase_id' => $request->purchase_id,
            'batch_id' => $request->med_batch_id[$product_key],
            'expiry_date' => $request->med_expiry_date[$product_key],
            'box_pattern_id' => $request->med_box_pattern[$product_key],
            'box_pattern_quantity' => $request->med_box_pattern_quantity[$product_key],
            'quantity' => $request->med_box_pattern_quantity[$product_key] * $boxPatterns->where('id', $request->med_box_pattern[$key])->first()->quantity,
            'purchase_price' => $request->med_purchase_price[$product_key],
            'subtotal_price' => $request->med_purchase_subtotal[$product_key],
            'other_cost' => $request->med_purchase_other_cost[$product_key],
            'total_price' => $request->med_purchase_total[$product_key],
          ]);
        }
      }

      $contact_payment->contact_id = $request->contact_id;
      $contact_payment->purchase_id = $request->purchase_id;
      $contact_payment->paying_amount = $request->paying_amount;
      $contact_payment->pay_by = $request->pay_by;
      $contact_payment->account_id = $request->account_id;
      $contact_payment->paying_date = $request->purchase_date;
      $contact_payment->save();

      DB::commit();
      return redirect()->route('purchase.index')->with('message', "Purchase Updated Successfully");
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->route('purchase.index')->with('error', "Purchase update error");
    }
  }

  public function destroy($id)
  {
    $purchase = Purchase::query()
      ->with(['purchaseProducts' => fn ($q) => $q->withStockProperties()])
      ->findOrFail($id);

    foreach ($purchase->purchaseProducts as $purchaseProduct) {
      if ($purchaseProduct->sale_product_qty > 0) {
        return back()->with([
          'type' => 'error',
          'message' => "Product has been sold from this purchase"
        ]);
      }
      if ($purchaseProduct->transferred_out_qty > 0) {
        return back()->with([
          'type' => 'error',
          'message' => "Product has been transferred from this purchase"
        ]);
      }
    }

    try {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB::beginTransaction();

      PurchaseProduct::where('purchase_id', $id)->delete();
      ContactPayment::where('purchase_id', $id)->delete();
      $purchase->delete();

      DB::commit();
      return back()->with('message', "Purchase deleted successfully");
    } catch (\Exception $e) {
      DB::rollback();
      return back()->with('type', 'error')->with('message', "Error");
    }
  }

  public function invoice($id)
  {
    $purchase = Purchase::query()
      ->with('purchaseProducts.product', 'purchaseProducts.boxPattern')
      ->findOrFail($id);

    return view('purchase.invoice', [
      'purchase' => $purchase,
      'contact_payments' => ContactPayment::query()
        ->where('contact_id', $purchase->contact_id)
        ->where('purchase_id', $purchase->id)
        ->get()
    ]);
  }
}
