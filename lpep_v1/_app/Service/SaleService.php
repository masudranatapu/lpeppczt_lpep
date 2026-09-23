<?php

namespace App\Service;

use App\Models\Product;
use Illuminate\Http\Request;

abstract class SaleService
{
  public abstract function index(Request $request);
  public abstract function create(Request $request);
  public abstract function store(Request $request);
  public abstract function view($id);
  public abstract function destroy($id);
  public abstract function invoice($id);
  public abstract function posInvoice($id);

  public abstract function cartList();
  public abstract function cartAdd(Request $request);
  public abstract function cartIncrement(Request $request);
  public abstract function cartQtyUpdate(Request $request);
  public abstract function cartDecrement(Request $request);
  public abstract function cartChangePrice(Request $request);
  public abstract function cartChangeBatchId(Request $request);
  public abstract function cartRemove(Request $request);
  public abstract function cartClear();

  protected abstract function getSingleCartItem($id);
  protected abstract function getBatchIds($cartItem);

  protected function isAvailableInStock($product, $checkQty = 1)
  {
    return loadCurrentStockQuantityFromProduct($product) >= $checkQty;
  }

  public function checkProductStock(Request $request)
  {
    $product = Product::query()->findOrFail($request->id);

    return $this->isAvailableInStock($product) != false ? $product : false;
  }

  public function filterProductCategory(Request $request)
  {
    return view("sale.partial.filterProduct", [
      'products' => $this->getDefaultProducts($request->category_id),
    ]);
  }

  public function filterProductBrand(Request $request)
  {
    return view("sale.partial.filterProduct", [
      'products' => $this->getDefaultProducts('all', $request->brand_id),
    ]);
  }

  protected function getDefaultProducts($category_id = 'all', $brand_id = 'all')
  {
    return Product::query()
      ->when($category_id != 'all', function ($q) use ($category_id) {
        $q->where('category_id', $category_id);
      })
      ->when($brand_id != 'all', function ($q) use ($brand_id) {
        $q->where('brand_id', $brand_id);
      })
      ->withStockProperties()
      ->where('status', 1)
      ->get();
  }
}
