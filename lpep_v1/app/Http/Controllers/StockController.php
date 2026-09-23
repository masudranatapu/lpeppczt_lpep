<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    
    public function index(Request $request)
{
    $agentId = $request->input('agent_id') ?: null;

    // per_page: 25, 50, 100, all (default 40)
    $perPageInput = $request->input('per_page', 40);

    if ($perPageInput === 'all') {
        $perPage = 100000; // safe big number (no count() needed)
    } else {
        $perPage = (int) $perPageInput;
        if (!in_array($perPage, [25, 40, 50, 100], true)) {
            $perPage = 40;
        }
    }

    $products = Product::query()
        ->when($request->product_name, function ($q) use ($request) {
            $q->where('products.product_name', 'like', '%' . $request->product_name . '%');
        })
        ->addSelect(['purchase_price' => function ($q) {
            $q->select('purchase_price')
                ->from('purchase_products')
                ->whereColumn('purchase_products.product_id', 'products.id')
                ->orderByDesc('purchase_products.id')
                ->limit(1);
        }])
        ->withStockProperties($agentId)
        ->paginate($perPage)
        ->withQueryString(); // keep product_name, agent_id, per_page in links

    $agents = [];
    if (!function_exists('isRole') || !isRole(ROLE_AGENT)) {
        $agents = User::whereHas('userPermission', function ($q) {
            $q->where('role_id', ROLE_AGENT);
        })
        ->where('status', 1)
        ->orderBy('name')
        ->get();
    }

    // return unchanged
    return view('stock.index', [
        'products' => $products,
        'search' => $request->product_name ?? null,
        'agents' => $agents,
        'selected_agent' => $agentId,
    ]);
}

}
