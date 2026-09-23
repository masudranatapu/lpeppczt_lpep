<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\AdminSaleService;
use App\Service\AgentSaleService;

class SaleController extends Controller
{

    private function service()
    {
        return isRole(ROLE_AGENT) ? new AgentSaleService : new AdminSaleService;
    }


    // Sale CRUD

    public function index(Request $request)
    {
        return $this->service()->index($request);
    }

    public function pos(Request $request)
    {
        return $this->service()->create($request);
    }

    public function store(Request $request)
    {

        // dd($request->all());

        return $this->service()->store($request);
    }

    public function view($id)
    {
        return $this->service()->view($id);
    }

    public function destroy($id)
    {
        return $this->service()->destroy($id);
    }

    public function invoice($id)
    {
        return $this->service()->invoice($id);
    }

    public function posInvoice($id)
    {
        return $this->service()->posInvoice($id);
    }


    // Cart CRUD

    public function cartList()
    {
        return $this->service()->cartList();
    }

    public function cartAdd(Request $request)
    {
        return $this->service()->cartAdd($request);
    }

    public function cartIncrement(Request $request)
    {
        return $this->service()->cartIncrement($request);
    }

    public function cartQtyUpdate(Request $request)
    {
        return $this->service()->cartQtyUpdate($request);
    }

    public function cartDecrement(Request $request)
    {
        return $this->service()->cartDecrement($request);
    }

    public function cartChangePrice(Request $request)
    {
        return $this->service()->cartChangePrice($request);
    }

    public function cartChangeBatchId(Request $request)
    {
        return $this->service()->cartChangeBatchId($request);
    }

    public function cartRemove(Request $request)
    {
        return $this->service()->cartRemove($request);
    }

    public function cartClear()
    {
        return $this->service()->cartClear();
    }


    // Filters

    public function filterProductCategory(Request $request)
    {
        return $this->service()->filterProductCategory($request);
    }

    public function filterProductBrand(Request $request)
    {
        return $this->service()->filterProductBrand($request);
    }


    // Misc

    public function checkProductStock(Request $request)
    {
        return $this->service()->checkProductStock($request);
    }

    public function account(Request $request)
    {
        return $this->service()->account($request);
    }
    
    public function customerDueClear(Request $request)
    {
        return $this->service()->customerDueClear($request);
    }
}
