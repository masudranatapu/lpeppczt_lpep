<?php

namespace App\Http\Controllers;

use App\Models\ContactPayment;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnProduct;
use App\Models\PurchaseReturnType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchaseReturn = PurchaseReturn::query()
                ->with("purchaseReturnType", "business", "createdBy", "updatedBy")
                ->get();

            return datatables()
                ->of($purchaseReturn)
                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>";
                    $btn = $btn . '<a title="View" href="' . route("purchase-return.view", $data->id) . '" class="btn btn-success btn-sm" ><i class="fa fa-eye"></i></a>';
                    $btn = $btn . '<a title="Edit" href="' . route("purchase-return.edit", $data->id) . '" class="btn btn-primary btn-sm" ><i class="fa fa-edit"></i></a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->id . '" id="deleteReturnPurchase" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created', function ($data) {
                    return $data->createdBy->name ?? '';
                })
                ->addColumn('updated', function ($data) {
                    return $data->updatedBy->name ?? '';
                })
                ->rawColumns(['action', "created", "updated"])
                ->addIndexColumn()
                ->make(true);
        }

        return view("purchase.return.index");
    }

    public function purchaseReturn($purchaseId)
    {

        $purchase = Purchase::query()
            ->with([
                "purchaseProducts.product" => function ($query) {
                    $query->withSum('purchaseProduct as purchase_qty', 'quantity')
                        ->withSum('purchaseReturnProduct as purchase_return_qty', 'quantity')
                        ->withSum('saleProduct as sale_qty', 'qty')
                        ->withSum('saleReturnProduct as sale_return_qty', 'qty');
                },
                'purchaseProducts.boxPattern',
                "supplier"
            ])
            ->find($purchaseId);




        return view("purchase.return.create", [
            'purchase' => $purchase,
            'purchase_return_types' => PurchaseReturnType::query()
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "purchase_id" => 'required',
            "return_type_id" => "required",
            "supplier_id" => "required",
            "return_date" => "required"
        ], [
            "return_type_id.required" => "Return type can't be null",
            "supplier_id.required" => "Supplier id can't be null"
        ]);

        $returnProducts = [];
        foreach ($request->return_quantity as $key => $quantity) {
            if ($request->return_quantity[$key]) {
                $explode_key = explode("-", $key);
                $returnProducts[] = [
                    "product_id" => $request->product_id[$explode_key[0]],
                    "purchase_product_id" => $explode_key[0],
                    "quantity" => $request->return_quantity[$key],
                    "price" => $request->purchase_price[$explode_key[0]],
                    "total" => $request->total[$key],
                ];
            }
        }

        if (!$this->purchaseReturnStore($request, $returnProducts))
            return back()->with([
                "type" => "error",
                "message" => "Failed To Return"
            ]);

        return redirect()->route('purchase-return.index')->with("message", "Purchase Return Successfully");
    }

    public function purchaseReturnStore(Request $request, array $returnProducts)
    {

       dd($returnProducts);


        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturn::query()
                ->create([
                    "purchase_id" => $request->purchase_id,
                    "purchase_return_type_id" => $request->return_type_id,
                    "contact_id" => $request->supplier_id,
                    "total" => $request->sale_return_amount,
                    "total_pay" => $request->pay_return_amount,
                    "date" => $request->return_date,
                    "note" => $request->note,
                    "created_by" => auth()->id(),
                ]);

            $returnProducts = array_map(function ($array) use ($purchaseReturn) {
                $array["purchase_return_id"] = $purchaseReturn->id;
                return $array;
            }, $returnProducts);

            PurchaseReturnProduct::query()->insert($returnProducts);

            ContactPayment::query()->create([
                "contact_id" => $request->supplier_id,
                "purchase_return_id" => $purchaseReturn->id,
                "paying_amount" => $request->pay_return_amount,
                "pay_by" => $request->pay_by,
                "account_id" => $request->account_id,
                "paying_date" => date("Y-m-d"),
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function show($id)
    {
        $purchaseReturn = PurchaseReturn::query()
            ->with([
                "purchaseReturnType",
                "business",
                "createdBy",
                "updatedBy",
                "purchaseReturnProducts.product",
                "contactPayment.account",
                "supplier"
            ])
            ->findOrFail($id);

        return view("purchase.return.show", [
            'purchaseReturn' => $purchaseReturn
        ]);
    }

    public function edit($id)
    {
        $purchaseReturn = PurchaseReturn::query()
            ->with("purchaseReturnType", "business", "createdBy", "updatedBy", "purchaseReturnProducts", "contactPayment")
            ->findOrFail($id);
        $purchase = Purchase::query()
            ->with([
                "purchaseProducts.product" => function ($query) {
                    $query->withSum('purchaseProduct as purchase_qty', 'quantity')
                        ->withSum('purchaseReturnProduct as purchase_return_qty', 'quantity')
                        ->withSum('saleProduct as sale_qty', 'qty')
                        ->withSum('saleReturnProduct as sale_return_qty', 'qty');
                },
                "purchaseProducts.purchaseReturnProduct",
                "supplier"
            ])
            ->find($purchaseReturn->purchase_id);
        return view("purchase.return.edit", [
            'purchaseReturn' => $purchaseReturn,
            'purchase' => $purchase,
            'purchase_return_types' => PurchaseReturnType::query()
                ->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturn::query()->find($id);
            $purchaseReturn->update([
                "purchase_id" => $request->purchase_id,
                "purchase_return_type_id" => $request->return_type_id,
                "contact_id" => $request->supplier_id,
                "total" => $request->sale_return_amount,
                "total_pay" => $request->pay_return_amount,
                "date" => $request->return_date,
                "note" => $request->note,
                "created_by" => auth()->id(),
            ]);

            foreach ($request->return_quantity as $key => $quantity) {
                $explode_key = explode("-", $key);

                if ($request->return_quantity[$key]) {
                    PurchaseReturnProduct::query()->updateOrCreate([
                        "purchase_return_id" => $id,
                        "product_id" => $request->product_id[$explode_key[0]],
                        "purchase_product_id" => $explode_key[0],
                    ], [
                        "quantity" => $request->return_quantity[$key],
                        "price" => $request->purchase_price[$explode_key[0]],
                        "total" => $request->total[$key]
                    ]);
                } else {
                    PurchaseReturnProduct::query()
                        ->where("purchase_return_id", $id)
                        ->where("product_id", $request->product_id[$explode_key[0]])
                        // ->where('product_model_id', $modelId)
                        ->delete();
                }
            }

            $purchaseReturn->contactPayment()->update([
                "paying_amount" => $request->pay_return_amount,
                "pay_by" => $request->pay_by,
                "account_id" => $request->account_id,
                "paying_date" => date("Y-m-d"),
            ]);

            DB::commit();
            return redirect()->route('purchase-return.index')->with("message", "Purchase Return Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with([
                'type' => "error",
                "message" => "Failed to purchase return"
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();
            $purchaseReturn = PurchaseReturn::query()
                ->with('purchaseReturnProducts')
                ->find($request->id);
            // ProductModel::query()
            //     ->whereHas("purchaseReturnProducts", function ($query) use ($request) {
            //         $query->where("purchase_return_id", $request->id);
            //     })
            //     ->update(['is_available' => true]);
            $purchaseReturn->purchaseReturnProducts()->delete();
            $purchaseReturn->contactPayment()->delete();
            $purchaseReturn->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false], 500);
        }
    }
}
