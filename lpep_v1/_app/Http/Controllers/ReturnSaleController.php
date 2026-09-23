<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Account;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\Models\ReturnProduct;
use App\Models\ContactPayment;
use Illuminate\Support\Facades\DB;

class ReturnSaleController extends Controller
{
    public function index()
    {
        return view('sale.returnSaleList', [
            'return_sales' => SaleReturn::query()->orderBy('id', 'desc')->get(),
        ]);
    }

    public function saleInReturn()
    {
        return view("sale.saleinreturn");
    }

    public function saleInReturnPost(Request $request)
    {
        $order_id = substr($request->orderid, 4);

        if (Sale::where('id', $order_id)->exists()) {
            return redirect()->route("sale.return", $order_id);
        } else {
            return back()->with([
                'type' => 'error',
                'message' => "Sorry! Invoice Id Doesn't Match"
            ]);
        }
    }

    public function create($sale_id)
    {
        return view('sale.returnSale', [
            'sale' => Sale::findOrFail($sale_id)
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $sale_return = new SaleReturn();
            $sale_return->sale_id = $request->sale_id;
            $sale_return->contact_id = $request->customer_id;
            $sale_return->invoice_no = $request->invoice_no;
            $sale_return->return_date = $request->return_date;
            $sale_return->total_amount = $request->sale_return_amount;
            $sale_return->paying_amount = $request->pay_return_amount;
            $sale_return->save();

            foreach ($request->sale_product_id as $sale_product_id) {
                if ($request->return_qty[$sale_product_id] != 0) {
                    $return_product = new ReturnProduct();
                    $return_product->sale_return_id = $sale_return->id;
                    $return_product->product_id = $request->product_id[$sale_product_id];
                    $return_product->qty = $request->return_qty[$sale_product_id];
                    $return_product->price = $request->product_price[$sale_product_id];
                    $return_product->total_price = $request->returnTotalPice[$sale_product_id];
                    $return_product->save();
                }
            }

            $contact_payment = new ContactPayment();
            $contact_payment->contact_id = $request->customer_id;
            $contact_payment->sale_return_id = $sale_return->id;
            $contact_payment->paying_amount = $request->pay_return_amount;
            $contact_payment->pay_by = $request->pay_by;
            $contact_payment->account_id = $request->account_id;
            $contact_payment->paying_date = $request->return_date;
            $contact_payment->save();

            DB::commit();
            return redirect()->route('sale')->with('message', "Product Returned Successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('sale')->with('type', 'error')->with('message', "Product Return Error");
        }
    }

    public function returnDetails($id)
    {
        return view('sale.partial.returnDetails', [
            'return' => SaleReturn::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        return view('sale.returnEdit', [
            'return' => SaleReturn::findOrFail($id)
        ]);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $sale_return = SaleReturn::findOrFail($request->return_sale_id);
        $sale_return->sale_id = $request->sale_id;
        $sale_return->contact_id = $request->customer_id;
        $sale_return->invoice_no = $request->invoice_no;
        $sale_return->return_date = $request->return_date;
        $sale_return->total_amount = $request->sale_return_amount;
        $sale_return->paying_amount = $request->pay_return_amount;
        $sale_return->save();

        foreach ($request->return_product as $return_product_id) {
            if ($request->return_qty[$return_product_id] != 0) {
                $return_product = ReturnProduct::findOrFail($return_product_id);
                $return_product->sale_return_id = $sale_return->id;
                $return_product->product_id = $request->product_id[$return_product_id];
                $return_product->qty = $request->return_qty[$return_product_id];
                $return_product->price = $request->product_price[$return_product_id];
                $return_product->total_price = $request->returnTotalPice[$return_product_id];
                $return_product->save();
            }
        }

        $contact_payment = ContactPayment::findOrFail($request->contact_payment_id);
        $contact_payment->contact_id = $request->customer_id;
        $contact_payment->sale_return_id = $sale_return->id;
        $contact_payment->paying_amount = $request->pay_return_amount;
        $contact_payment->pay_by = $request->pay_by;
        $contact_payment->paying_date = $request->return_date;
        if ($contact_payment->save()) {
            return redirect()->route('return.sale.index')->with('message', "Salle Product Return Updated Successfully");
        }
    }

    public function destroy($id)
    {
        $return = SaleReturn::findOrFail($id);
        ReturnProduct::where('sale_return_id', $id)->delete();
        ContactPayment::where('sale_return_id', $id)->delete();

        if ($return->delete()) {
            return back()->with('message', "Sale Return History Deleted Successfully");
        }
    }

    public function invoice($id)
    {
        $return = SaleReturn::findOrFail($id);
        return view('sale.saleReturnInvoice', [
            'return' => $return
        ]);
    }

    public function account(Request $request)
    {
        if ($request->account_type != "Cash") {
            return view('sale.partial.returnaccount', [
                'data' => Account::query()
                    ->where('account_type', $request->account_type)
                    ->get(),
                'account_type' => $request->account_type,
                'type' => $request->type ?? ''
            ]);
        }
    }
}
