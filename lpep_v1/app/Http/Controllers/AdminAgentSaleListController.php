<?php

namespace App\Http\Controllers;

use App\Models\AgentSale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminAgentSaleListController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::active()->agent()->get(['id', 'name', 'employee_name']);
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $viewLink = route('agentsalelist.show', $row->id);
                    return yajraViewEditDeleteButtons($viewLink);
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('sale.agent-sale-list.index');
    }


    public function create() {}


    public function show(Request $request, User $user)
    {
        $filterYear = $request->year ?? Carbon::now()->year;
        $filterMonth = $request->month ?? Carbon::now()->month;

        $user->load(['agentSales' => function ($query) use ($filterYear, $filterMonth) {
            $query->selectRaw('sale_date, agent_id, SUM(total_amount) as total_amount_sum')
                ->whereYear('sale_date', $filterYear)
                ->whereMonth('sale_date', $filterMonth)
                ->groupBy('sale_date', 'agent_id')
                ->orderBy('sale_date', 'desc');
        }]);

        return view('sale.agent-sale-list.show', [
            'user' => $user,
            'reqYear' => $filterYear,
            'reqMonth' => $filterMonth,

        ]);
    }
}
