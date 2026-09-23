<?php

namespace App\Service;

use App\Models\Account;
use App\Models\AgentStockTransferDetails;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Contact;
use App\Models\ContactPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\SellCart;
use App\Models\User;
use App\Models\VatGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSaleService extends SaleService
{

    // Sale CRUD

    public function index(Request $request)
    {
        $sales = Sale::query()
                ->with(['customer', 'user'])
                ->when($request->customer, function ($query) use ($request) {
                    $query->where('contact_id', $request->customer);
                })
                ->when($request->sale_date, function ($query) use ($request) {
                    $query->where('sale_date', $request->sale_date);
                })
                ->when($request->search, function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('customer_name', 'like', '%' . $request->search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $request->search . '%')
                        ->orWhereHas('customer', function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->search . '%');
                        });
                    });
                })
                ->when($request->user, function ($query) use ($request) {
                    $query->where("user_id", $request->user);
                })
                ->orderBy('id', 'desc')
                ->paginate(25);


        return view('sale.index', [
            'sales' => $sales,
            'users' => User::query()->get(),
            'customer' => $request->customer ? Contact::query()->findOrFail($request->customer) : null
        ]);
    }

    public function create(Request $request)
    {
        $product = $this->getDefaultProducts();

        return view('sale.pos', [
            'products' => $product,
            'categories' => Category::query()->get(),
            'brands' => Brand::query()->get(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::query()->create([
                'user_id' => auth()->id(),
                'contact_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'sale_date' => date('Y-m-d'),
                'sub_total' => $request->sub_total,
                'discount_amount' => $request->discount_amount,
                'vat' => $request->vat,
                'total_amount' => $request->total_amount,
                'paying_amount' => $request->paying_amount,
                'payment_type' => $request->payment_type,
                'deliverycharge' => $request->deliverycharge,
                'preorder' => $request->order_status,
            ]);

            $sellCarts = SellCart::query()
                ->where('user_id', auth()->id())
                ->get();

            $saleProductData = [];

            foreach ($sellCarts as $sellCart) {
                $saleProductData[] = [
                    'sale_id' => $sale->id,
                    'product_id' => $sellCart->product_id,
                    'purchase_product_id' => $sellCart->purchase_product_id,
                    'agent_stock_transfer_details_id' => $sellCart->agent_stock_transfer_details_id,
                    'batch_id' => $sellCart->batch_id,
                    'qty' => $sellCart->qty,
                    'price' => $sellCart->price,
                    'total_price' => $sellCart->total_price,
                    'created_at' => now()
                ];
            }

            SaleProduct::query()->insert($saleProductData);
            SellCart::where('user_id', auth()->id())->delete();

            if ($request->agent_stock_transfer_details_id) {
                AgentStockTransferDetails::query()
                    ->where('id', $request->agent_stock_transfer_details_id)
                    ->increment("sold_quantity", $sellCart->qty);
            }

            ContactPayment::query()->create([
                'contact_id' => $request->customer_id,
                'paying_amount' => $request->paying_amount,
                'pay_by' => $request->payment_type,
                'account_id' => $request->account_id,
                'sale_id' => $sale->id,
                'paying_date' => date('Y-m-d'),
            ]);

            DB::commit();

            return back()->with([
                'message' => "Product Sale Successfully",
                'sale_id' => route('pos.invoice', $sale),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with([
                'type' => 'error',
                'message' => "Invalid Operation",
            ]);
        }
    }

    public function view($id)
    {
        return view('sale.partial.saleDetails', [
            'sale' => Sale::query()->with('user:id,name')->with('saleProducts')->findOrFail($id)
        ]);
    }

    public function destroy($id)
    {
        $sale = Sale::query()->with('saleProducts.purchaseProduct')->findOrFail($id);
        if ($sale->saleReturn->count() > 0) {
            return back()->with([
                'type' => 'error',
                'message' => "NOT ALLOWED : Mismatch between Sale and Sale Return quantity"
            ]);
        }

        try {
            DB::beginTransaction();

            SaleProduct::query()->where('sale_id', $id)->delete();
            $sale->delete();

            DB::commit();
            return back()->with('message', 'Sale Deleted Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('type', 'error')->with('message', 'Sale Couldn\'t Be Deleted');
        }
    }

    public function invoice($sale_id)
    {
        $sale = Sale::query()->with('saleProducts.product.unit')->findOrFail($sale_id);

        return view('sale.invoice', [
            'sale' => $sale,
        ]);
    }

    public function posInvoice($id)
    {
        $sale = Sale::query()
            ->with("saleProducts.product.unit")
            ->findOrFail($id);

        return view('sale.posInvoice', [
            'sale' => $sale,
            'contact_payments' => ContactPayment::query()
                ->where('contact_id', $sale->contact_id)
                ->where('sale_id', $sale->id)
                ->get()
        ]);
    }


    // Cart CRUD

    public function cartList()
    {
        $sellCarts = SellCart::query()
            ->where('user_id', auth()->id())
            ->with([
                'product.unit',
                'product.agentStockTransferDetails.agentPurchaseProduct',
                'product.purchaseProduct' => function ($q) {
                    $q->withStockProperties()
                        ->havingRaw('available_quantity > 0');
                }
            ])
            ->get();

        return response()->json([
            'cart' => view('sale.partial.cartItems', ['sellCarts' => $sellCarts])->render(),
            'total' => $sellCarts->sum('total_price'),
            'vat' => $sellCarts->sum('vat'),
            'items' => $sellCarts->count(),
        ]);
    }

    public function cartAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $product = Product::query()
            ->with('vatGroup')
            ->findOrFail($request->product_id);

        $sellCart = SellCart::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->with(['product.purchaseProduct' => fn ($q) => $q->withStockProperties()])
            ->first();

        if ($this->isAvailableInStock($product, $sellCart ?  $sellCart->qty + 1 : 1) === false) { // Stock Check
            return response()->json([
                'error' => "This Product is Out Of Stock"
            ]);
        }
        if ($sellCart) { // increment
            $batchIds = $this->getBatchIds($sellCart);
            $batchId = $batchIds->where('batch_id', $sellCart->batch_id)->first();

            if ($batchId && $batchId->available_quantity >= $sellCart->qty + 1 && $this->isAvailableInStock($product, $sellCart->qty + 1)) {
                $sellCart->update([
                    'batch_id' => $sellCart->batch_id,
                    'qty' => $sellCart->qty + 1,
                    'total_price' => $sellCart->total_price + $product->discount_selling_price,
                    'vat' => $sellCart->vat + (($product->discount_selling_price * optional($product->vatGroup)->vat_percent) / 100)
                ]);
            } else {
                return response()->json([
                    'error' => "Batch limit reached"
                ]);
            }
        } else {
            // add product
            $sellCart = SellCart::query()
                ->create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'qty' => 1,
                    'price' => $product->discount_selling_price,
                    'total_price' => $product->discount_selling_price,
                    'vat' => ($product->discount_selling_price * optional($product->vatGroup)->vat_percent) / 100,
                    'created_at' => Carbon::now()
                ]);

            $sellCart->load(['product.purchaseProduct' => fn ($q) => $q->withSum('saleProducts as sale_product_qty', 'qty')]);
            $purchaseProducts = collect($sellCart->product->purchaseProduct)
                ->sortByDesc('expiry_date')
                ->each(function ($item) {
                    $item->max_cart_quantity = $item->quantity - $item->sale_product_qty;
                });
            if ($purchaseProducts->count()) {
                $sellCart->update([
                    'batch_id' => $purchaseProducts->first()->batch_id,
                    'purchase_product_id' => $purchaseProducts->first()->id
                ]);
            } else {

                $sellCart->product->load([
                    'agentStockTransferDetails' => function ($query) use ($sellCart) {
                        $query->where("available_quantity", ">=", $sellCart->qty)
                            ->with("agentPurchaseProduct")->limit(1);
                    },
                ]);

                $purchaseProducts = collect($sellCart->product->agentStockTransferDetails)
                    ->each(function ($item) {
                        $item->max_cart_quantity = $item->available_quantity;
                    });

                $sellCart->update([
                    'batch_id' => $purchaseProducts->first()->agentPurchaseProduct->batch_id,
                    'agent_stock_transfer_details_id' => $purchaseProducts->first()->id
                ]);
            }
        }

        return $this->cartList();
    }

    public function cartIncrement(Request $request)
    {
        $sellCart = $this->getSingleCartItem($request->id);
        $batchIds = $this->getBatchIds($sellCart);
        $product = Product::query()->findOrFail($sellCart->product_id);
        $batchId = $batchIds->where('batch_id', $sellCart->batch_id)->first();
        if (
            !$batchId
            || ($batchId->available_quantity < $sellCart->qty + 1)
            || !$this->isAvailableInStock($product, $sellCart->qty + 1)
        ) {
            return response()->json([
                'error' => "Stock Out"
            ]);
        }

        $vat = null;
        if ($product->vat_group_id) {
            $vat = VatGroup::findOrFail($product->vat_group_id)->vat_percent;
        }

        $sellCart->update([
            'qty' => $sellCart->qty + 1,
            'total_price' => $sellCart->total_price + $sellCart->price,
            'vat' => $vat ?  $sellCart->vat + (($product->discount_selling_price * $vat) / 100) : $sellCart->vat
        ]);

        return $this->cartList();
    }

    public function cartQtyUpdate(Request $request)
    {
        $sellCart = $this->getSingleCartItem($request->id);
        $batchIds = $this->getBatchIds($sellCart);
        $product = Product::query()->findOrFail($sellCart->product_id);

        $batchId = $batchIds->where('batch_id', $sellCart->batch_id)->first();
        if (
            !$batchId
            || ($batchId->available_quantity < $request->value)
            || !$this->isAvailableInStock($product, $request->value)
        ) {
            return response()->json([
                'error' => "Stock Out"
            ]);
        }

        if ($request->value <= 0) {
            $sellCart->delete();
            return $this->cartList();
        }

        $sellCart->qty = $request->value;
        $sellCart->total_price = $request->value * $sellCart->price;
        if ($product->vat_group_id) {
            $vat = VatGroup::findOrFail($product->vat_group_id)->vat_percent;
            $sellCart->vat = (($product->discount_selling_price * $vat) / 100) * $request->value;
        }

        if ($sellCart->save()) {
            return $this->cartList();
        }
    }

    public function cartDecrement(Request $request)
    {
        $sellCart = SellCart::query()->findOrFail($request->id);
        $product = Product::query()->findOrFail($sellCart->product_id);

        $vat = null;
        if ($product->vat_group_id) {
            $vat = VatGroup::findOrFail($product->vat_group_id)->vat_percent;
        }

        if ($sellCart->qty - 1 == 0) {
            $sellCart->delete();
        } else {
            $sellCart->update([
                'qty' => $sellCart->qty - 1,
                'total_price' => $sellCart->total_price - $sellCart->price,
                'vat' => $vat ? (($product->discount_selling_price * $vat) / 100) : $sellCart->vat
            ]);
        }

        return $this->cartList();
    }

    public function cartChangePrice(Request $request)
    {
        $cart = SellCart::query()->findOrFail($request->cart_id);
        $cart->price = $request->price;
        $cart->total_price = $request->price * $cart->qty;
        if ($cart->save()) {
            return true;
        }
    }

    public function cartChangeBatchId(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'batch_id' => 'required',
            'source_id' => 'required|numeric',
            'type' => 'required|in:purchase,stockTransfer'
        ]);

        $sellCart = $this->getSingleCartItem($request->id);
        $batchIds = $this->getBatchIds($sellCart);
        $product = Product::query()->findOrFail($sellCart->product_id);

        if (($purchaseProduct = ((object) $batchIds->where('batch_id', $request->batch_id)->first()))) {
            $sellCart->batch_id = $request->batch_id;
            if ($purchaseProduct->type == "purchase") {
                $sellCart->purchase_product_id = $request->source_id;
                $sellCart->agent_stock_transfer_details_id = null;
            } else {
                $sellCart->purchase_product_id = null;
                $sellCart->agent_stock_transfer_details_id = $request->source_id;
            }
            $sellCart->qty = min($sellCart->qty, $purchaseProduct->available_quantity);
        }

        if ($product->vat_group_id) {
            $vat = VatGroup::findOrFail($product->vat_group_id)->vat_percent;
            $sellCart->vat = (($product->discount_selling_price * $vat) / 100) * $sellCart->qty;
        }

        $sellCart->total_price = $sellCart->qty * $sellCart->price;
        $sellCart->save();

        return $this->cartList();
    }

    public function cartRemove(Request $request)
    {
        SellCart::query()->findOrFail($request->id)->delete();

        return $this->cartList();
    }

    public function cartClear()
    {
        SellCart::where('user_id', auth()->id())->delete();

        return $this->cartList();
    }

    protected function getSingleCartItem($id)
    {
        return SellCart::query()
            ->with([
                'product.purchaseProduct' => function ($q) {
                    $q->withStockProperties()
                        ->where('expiry_date', '>=', now()->format('Y-m-d'))
                        ->orWhereNull('expiry_date');
                },
                'product.agentStockTransferDetails' => function ($q) {
                    $q->where("available_quantity", ">", 0)
                        ->with("agentPurchaseProduct");
                }
            ])
            ->findOrFail($id);
    }

    protected function getBatchIds($sellCart)
    {
        $collection = collect([]);
        collect($sellCart->product->purchaseProduct)
            ->sortByDesc('expiry_date')
            ->each(function ($item) use ($collection) {
                $collection->push((object) [
                    'id' => $item->id,
                    'type' => "purchase",
                    'available_quantity' => $item->available_quantity,
                    'batch_id' => $item->batch_id
                ]);
            });
        collect($sellCart->product->agentStockTransferDetails)
            ->each(function ($item) use ($collection) {
                $collection->push((object) [
                    'id' => $item->id,
                    'type' => "stockTransfer",
                    'available_quantity' => $item->available_quantity,
                    'batch_id' => $item->agentPurchaseProduct->batch_id
                ]);
            });

        return $collection;
    }


    // Misc

    public function account(Request $request)
    {
        if ($request->account_type != "Cash") {
            return view('sale.partial.account', [
                'data' => Account::where('account_type', $request->account_type)->get(),
                'account_type' => $request->account_type,
            ]);
        }
    }
    
     public function customerDueClear($request)
    {
        $sale = Sale::findOrFail($request->sale_id);
        $sale->paying_amount += $request->due_amount;
        $saved = $sale->save();
        if ($saved) {
            return response()->json([
                'message' => "Due Cleared Successfully"
            ]);
        }
        return response()->json([
            'error' => "Operation Failed"
        ], 500);
    }
}
