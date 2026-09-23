<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodeController extends Controller
{
    public function barcode()
    {
        return view('product.barcode');
    }

    public function printBarcode(Request $request)
    {
        $data = [];
        $printData = [];
        foreach ($request->product_id as $key => $product_id) {
            $product = Product::query()
                ->with([
                    'category',
                ])
                ->findOrFail($product_id);

            $filter = [
                'product_name' => $product->product_name,
                'category_name' => $product->category->category_name,
                'price' => $product->discount_selling_price,
                'barcode' => $product->barcode,
                'qty' => $request->qty[$key],
            ];
            array_push($data, $filter);
        }

        if ($request->category_name) {
            array_push($printData, 'category');
        }

        if ($request->action) {
            array_push($printData, $request->action);
        }

        if ($request->product_price) {
            array_push($printData, 'price');
        }

        if ($request->product_name) {
            array_push($printData, 'product_name');
        }

        return view("product.partial.barcodePrint", [
            'data' => json_encode($data),
            'printData' => $request->action,
        ]);
    }

    public function printBarcodeBack()
    {
        return redirect()->route('product.barcode');
    }

    public function test()
    {
        return view('product.test');
    }

    public function autocompletePurchase(Request $request)
    {
        $request->validate(['q' => 'required']);

        return Product::query()
            ->where(function ($q) use ($request) {
                $q->where('barcode', "like", "%{$request->q}%")
                    ->orWhere('product_name', "like", "%{$request->q}%");
            })
            ->when(isRole(ROLE_AGENT), fn ($q) => $q->where('is_medicine', false))
            ->where('status', 1)
            ->get()
            ->map(function ($item) {
                $ob = new \stdClass;
                $ob->id = $item->id;
                $ob->is_medicine = $item->is_medicine;
                $ob->product_name = $item->product_name;
                $ob->barcode = $item->barcode;
                $ob->purchase_price = 0;
                return $ob;
            });
    }

    public function autocompleteSale(Request $request)
    {
        $request->validate(['q' => 'required']);

        return Product::query()
            ->where(function ($q) use ($request) {
                $q->where('barcode', "like", "%{$request->q}%")
                    ->orWhere('product_name', "like", "%{$request->q}%");
            })
            ->where('status', 1)
            ->select('id', 'product_name', 'barcode', 'is_medicine')
            ->withStockProperties()
            ->where(function ($q) use ($request) {
                $q->where('barcode', "like", "%{$request->q}%")
                    ->orWhere('product_name', "like", "%{$request->q}%");
            })
            ->get()
            ->map(function ($item) {
                $ob = new \stdClass;
                $ob->product_id = $item->id;
                $ob->is_medicine = $item->is_medicine;
                $ob->product_name = $item->product_name;
                $ob->barcode = $item->barcode;
                $ob->quantity = getCurrentStockQuantityFromProduct($item);
                return $ob;
            });
    }

    public function autocompleteBarcode(Request $request)
    {
        $request->validate(['q' => 'required']);

        return Product::query()
            ->where(function ($q) use ($request) {
                $q->where('barcode', "like", "%{$request->q}%")
                    ->orWhere('product_name', "like", "%{$request->q}%");
            })
            ->where('status', 1)
            ->select('id', 'product_name', 'barcode', 'is_medicine')
            ->get()
            ->map(function ($item) {
                $ob = new \stdClass;
                $ob->product_id = $item->id;
                $ob->is_medicine = $item->is_medicine;
                $ob->product_name = $item->product_name;
                $ob->barcode = $item->barcode;
                $ob->quantity = 1;
                return $ob;
            });
    }

    
    public function autocompleteTransfer(Request $request)
    {
        $request->validate(['q' => 'required']);
    
        $products = Product::query()
            ->where(function ($q) use ($request) {
                $q->where('products.barcode', 'like', "%{$request->q}%")
                  ->orWhere('products.product_name', 'like', "%{$request->q}%");
            })
            ->select('products.id', 'products.product_name', 'products.barcode')
            ->with([
                // Load purchase lots ("batches") that still have transferable qty
                'purchaseProduct' => function ($q) {
                    // total transferred OUT per purchase lot
                    $transferOutSub = DB::table('stock_transfer_details')
                        ->select('purchase_product_id', DB::raw('SUM(quantity) AS transferred_out_qty'))
                        ->groupBy('purchase_product_id');
    
                    $q->from('purchase_products')
                      ->select([
                          'purchase_products.id',
                          'purchase_products.product_id',
                          'purchase_products.purchase_id', 
                          'purchase_products.quantity',
                          DB::raw('COALESCE(purchase_products.quantity,0) - COALESCE(std.transferred_out_qty,0) AS available_quantity'),
                      ])
                      ->leftJoinSub($transferOutSub, 'std', 'std.purchase_product_id', '=', 'purchase_products.id')
                      ->having('available_quantity', '>', 0)
                      ->orderByDesc('purchase_products.id'); 
                }
            ])
            ->get();
    
        return $products
            ->filter(fn ($p) => $p->purchaseProduct->count() > 0)
            ->map(function ($item) {
                $ob = new \stdClass();
                $ob->id = $item->id;
                $ob->product_name = $item->product_name;
                $ob->barcode = $item->barcode;
                $ob->batches = $item->purchaseProduct->map(fn ($lot) => [
                    'id' => $lot->id, // this is purchase_products.id
                    'batch_id' => $lot->purchase_id, 
                    'available_quantity' => (float) $lot->available_quantity,
                ])->values();
    
                return $ob;
            })->values();
    }

    public function autocompleteAgentTransfer(Request $request)
    {
        $request->validate(['q' => 'required']);

        return Product::query()
            ->where(function ($q) use ($request) {
                $q->where('barcode', "like", "%{$request->q}%")
                    ->orWhere('product_name', "like", "%{$request->q}%");
            })
            ->select('id', 'product_name', 'barcode')
            ->with([
                'agentPurchaseProducts' => function ($q) {
                    $q->withStockProperties()
                        ->havingRaw('available_quantity > 0');
                }
            ])
            ->whereHas('agentPurchaseProducts', function ($q) {
                $q->withStockProperties()
                    ->havingRaw('available_quantity > 0');
            })
            ->get()
            ->filter(fn ($item) => $item->agentPurchaseProducts->count())
            ->map(function ($item) {
                $ob = new \stdClass;
                $ob->id = $item->id;
                $ob->product_name = $item->product_name;
                $ob->barcode = $item->barcode;
                $ob->batches = collect($item->agentPurchaseProducts)
                    ->reduce(function ($c, $it) {
                        $c[] = [
                            'id' => $it->id,
                            'batch_id' => $it->batch_id,
                            'available_quantity' => $it->available_quantity,
                        ];
                        return $c;
                    }, []);
                return $ob;
            });
    }
}
