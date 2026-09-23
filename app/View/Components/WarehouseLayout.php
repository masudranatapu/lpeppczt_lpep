<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class WarehouseLayout extends Component
{
    public string $title;

    public $portalUser;

    public string $warehouseName;

    public string $warehouseEmail;

    public bool $isSalesman;

    public function __construct(string $title = 'Warehouse Portal')
    {
        $this->title = $title;
        $warehouseUser = Auth::guard('warehouse')->user();
        $salesmanUser = Auth::guard('warehouse_salesman')->user();

        $this->isSalesman = (bool) $salesmanUser;
        $this->portalUser = $salesmanUser ?? $warehouseUser;
        $this->warehouseName = $this->portalUser?->name ?? 'Warehouse User';
        $this->warehouseEmail = $this->portalUser?->email ?? '';
    }

    public function render()
    {
        return view('warehouse-portal.layouts.warehouse-layout');
    }
}
