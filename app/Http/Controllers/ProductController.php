<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SaleProduct;
use App\Models\Unit;
use App\Models\VatGroup;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::query()
                ->with('brand', 'category')
                ->select('*')
                ->orderByDesc('products.id')
                ->get();

            return datatables()
                ->of($data)
                ->addColumn("action", function ($row) {
                    $action = '<div class="btn-group btn-sm">
                                <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm" data-toggle="dropdown" aria-expanded="false"> Action <span class="caret"></span> </button>
                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">';
                    if (permission('p1')) {
                        $action .= '<a onclick="view(' . $row->id . ')" href="javascript:void(0);" class="dropdown-item">View</a>';
                    }
                    if (permission('p3')) {
                        $action .= '<a href="' . route('product.edit', encrypt($row->id)) . '" class="dropdown-item">Edit</a>';
                    }
                    if (permission('pad1')) {
                        if ($row->status == 1) {
                            $action .= '<a href="' . route('product.deactive', $row->id) . '" class="dropdown-item">Disable</a>';
                        } else {
                            $action .= '<a href="' . route('product.active', $row->id) . '" class="dropdown-item">Active</a>';
                        }
                    }
                    if (permission('p4')) {
                        $action .= '<a href="' . route('product.destroy', $row->id) . '" class="dropdown-item" id="delete">Delete</a>';
                    }
                    if (permission('pro')) {
                        $action .= '<a style="cursor: pointer;" onclick="printBarcode(' . $row->id . ')" class="dropdown-item" >Barcode Print</a> </div></div>';
                    }
                    return $action;
                })
                ->addColumn("status", function ($row) {
                    if ($row->status == 1)
                        return '<span class="badge badge-success">Active</span>';
                    else
                        return '<span class="badge badge-danger">Disable</span>';
                })
                ->addColumn("picture", function ($row) {
                    if ($row->image)
                        return "<img height='50px' onclick='view({$row->product_id})' src='" . asset(json_decode($row->image)) . "' alt=''>";
                    else
                        return "<img height='50px' src='" .  asset('images/nopic.png') . "' alt=''>";
                })
                ->addColumn("vat", function ($row) {
                    return $row->vat_group_id ? $row->vatGroup->vat_group_name : "";
                })
                ->addColumn("brand", function ($row) {
                    return $row->brand_id ? $row->brand->brand_name : "";
                })
                ->rawColumns(['action', 'status', 'picture', 'vat', 'brand'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('product.index');
    }

    public function create()
    {
        return view('product.create', [
            'brands' => Brand::query()->get(),
            'units' => Unit::query()->get(),
            'categories' => Category::query()->get(),
            'vat_groups' => VatGroup::query()->get(),
            'barcode' => nextProductBarcode()
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $product = new Product;

            $product->product_name = $request->name;
            $product->barcode = $request->sku;
            $product->unit_id = $request->unit_id;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->alert_quantity = $request->alert_quantity;
            $product->selling_price = $request->selling_price;
            $product->other_price = $request->other_price;
            $product->vat_group_id = $request->vat_group_id;
            $product->created_by = Auth::id();
            $product->product_description = $request->product_description;
            $product->is_medicine = $request->is_medicine ?? false;
            $product->status = 1;

            if ($request->discount) {
                if ($request->discount_type == 1) {
                    $product->discount_price = $request->discount;
                    $product->discount_selling_price = ($request->selling_price - $request->discount);
                } else {
                    $percent_amount = ($request->discount * $request->selling_price) / 100;
                    $product->discount_price = $percent_amount;
                    $product->discount_selling_price = ($request->selling_price - $percent_amount);
                }
            } else {
                $product->discount_selling_price = $request->selling_price;
            }

            if ($request->hasFile('picture')) {
                $path = $request->picture->store('uploads/products', ['disk' => 'public_uploads']);
                $product->image = json_encode($path);
            }

            $product->save();

            DB::commit();

            return redirect()->route('product.index')->with('message', "Product Added Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with([
                'type' => 'error',
                'message' => "Something is wrong"
            ]);
        }
    }

    public function view($id)
    {
        return view('product.view', [
            'product' => Product::query()
                ->withStockProperties()
                ->with('createdBy:id,name', 'updatedBy:id,name')
                ->findOrFail($id),
        ]);
    }


    public function edit($id)
    {
        $id = decrypt($id);
        $categories = Category::query()->get();
        $units = Unit::query()->get();
        $brands = Brand::query()->get();

        return view('product.edit', [
            'product' => Product::find($id),
            'brands' => $brands,
            'units' => $units,
            'categories' => $categories,
            'vat_groups' => VatGroup::query()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $product->product_name = $request->name;
        $product->barcode = $request->sku;
        $product->unit_id = $request->unit_id;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->alert_quantity = $request->alert_quantity;
        $product->selling_price = $request->selling_price;
        $product->other_price = $request->other_price;
        $product->vat_group_id = $request->vat_group_id;
        $product->product_description = $request->product_description;
        $product->is_medicine = $request->is_medicine == 'on';
        $product->updated_by = auth()->id();
        $product->status = 1;

        if ($request->discount) {
            if ($request->discount_type == 1) {
                $product->discount_price = $request->discount;
                $product->discount_selling_price = ($request->selling_price - $request->discount);
            } else {
                $percent_amount = ($request->discount * $request->selling_price) / 100;
                $product->discount_price = $percent_amount;
                $product->discount_selling_price = ($request->selling_price - $percent_amount);
            }
        } else {
            $product->discount_price = 0;
            $product->discount_selling_price = $request->selling_price;
        }

        if ($request->hasFile('picture')) {
            $path = $request->picture->store('uploads/products', ['disk' => 'public_uploads']);
            $product->image = json_encode($path);
        }

        if ($product->save()) {
            return redirect()->route('product.index')->with('message', "Product Added Successfully");
        } else {
            return back()->with([
                'type' => 'error',
                'message' => "Something is wrong"
            ]);
        }
    }


    public function customValidation(Request $request)
    {
        $field_name = $request->field_name;
        $value = $request->value;

        $product = Product::where($field_name, $value)->exists();
        if ($product) {
            return response()->json(strtoupper($field_name) . " is Already Taken");
        }
    }

    public function active($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 1;
        $product->save();
        return back()->with('message', "Product Activated");
    }

    public function deactive($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 0;
        $product->save();
        return back()->with('message', "Product Deactivated");
    }

    public function destroy($id)
    {

        $product = Product::findOrFail($id);
        if (SaleProduct::where('product_id', $product->id)->exists()) {
            return back()->with([
                'type' => 'error',
                'message' => "NOT ALLOWED : Mismatch between sold and purchase quantity"
            ]);
        } else {
            try {
                DB::beginTransaction();
                $product->delete();
                DB::commit();
                return back()->with('message', "Product Deleted Successfully");
            } catch (\Exception $e) {
                DB::rollback();
                return back()->with([
                    'type' => 'error',
                    'message' => "NOT ALLOWED : Mismatch between sold and purchase quantity"
                ]);
            }
        }
    }


    public function productJson()
    {
        $products = Product::select('*')
            ->get();
        return response()->json($products);
    }
}
