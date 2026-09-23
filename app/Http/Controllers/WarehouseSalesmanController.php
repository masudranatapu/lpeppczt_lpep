<?php

namespace App\Http\Controllers;

use App\Exports\WarehouseSalesmanSalesReportExport;
use App\Models\Warehouse;
use App\Models\WarehouseSale;
use App\Models\WarehouseSaleItem;
use App\Models\WarehouseSalePayment;
use App\Models\WarehouseSalesman;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseSalesmanController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin', 403);
    }

    private function resolveSalesReportFilters(Request $request): array
    {
        $data = $request->validate([
            'period' => ['nullable', 'in:today,this_week,this_month,month,custom'],
            'month' => ['required_if:period,month', 'nullable', 'integer', 'between:1,12'],
            'year' => ['required_if:period,month', 'nullable', 'integer', 'between:2000,' . (now()->year + 1)],
            'start_date' => ['required_if:period,custom', 'nullable', 'date'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $period = $data['period'] ?? 'today';
        $today = Carbon::today();

        if ($period === 'this_week') {
            $startDate = $today->copy()->startOfWeek(Carbon::MONDAY);
            $endDate = $today->copy()->endOfWeek(Carbon::SUNDAY);
            $label = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
        } elseif ($period === 'this_month') {
            $startDate = $today->copy()->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            $label = $today->format('F Y');
        } elseif ($period === 'month') {
            $month = (int) $data['month'];
            $year = (int) $data['year'];
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
            $label = $startDate->format('F Y');
        } elseif ($period === 'custom') {
            $startDate = Carbon::parse($data['start_date'])->startOfDay();
            $endDate = Carbon::parse($data['end_date'])->startOfDay();
            $label = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        } else {
            $period = 'today';
            $startDate = $today->copy();
            $endDate = $today->copy();
            $label = 'Today (' . $today->format('d M Y') . ')';
        }

        return [
            'period' => $period,
            'month' => (int) ($data['month'] ?? $today->month),
            'year' => (int) ($data['year'] ?? $today->year),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'label' => $label,
        ];
    }

    private function buildSalesReportData(WarehouseSalesman $salesman, array $filters, bool $paginate): array
    {
        $applySaleFilters = function ($query) use ($salesman, $filters) {
            $query->where('warehouse_salesman_id', $salesman->id)
                ->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
        };

        $baseSalesQuery = WarehouseSale::query();
        $applySaleFilters($baseSalesQuery);

        $productSales = WarehouseSaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(total) as total_sales_amount')
            ->selectRaw('COUNT(DISTINCT warehouse_sale_id) as sale_count')
            ->whereHas('sale', $applySaleFilters)
            ->with('product.unit')
            ->groupBy('product_id')
            ->orderByDesc('total_sales_amount')
            ->get();

        $summary = (object) [
            'sale_count' => (clone $baseSalesQuery)->count(),
            'total_quantity' => (float) WarehouseSaleItem::query()
                ->whereHas('sale', $applySaleFilters)
                ->sum('quantity'),
            'total_sales_amount' => (float) WarehouseSalePayment::query()
                ->whereHas('sale', $applySaleFilters)
                ->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']])
                ->sum('amount'),
            'product_count' => $productSales->count(),
        ];

        $salesQuery = (clone $baseSalesQuery)
            ->with(['warehouse', 'items.product.unit'])
            ->withSum('items as total_quantity', 'quantity')
            ->orderByDesc('sale_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $sales = $paginate
            ? $salesQuery->paginate(20)->withQueryString()
            : $salesQuery->get();

        return compact('sales', 'productSales', 'summary');
    }

    private function salesReportPayload(Request $request, WarehouseSalesman $salesman, bool $paginate): array
    {
        $this->ensureAdmin();
        $salesman->load('warehouse');

        $filters = $this->resolveSalesReportFilters($request);
        $report = $this->buildSalesReportData($salesman, $filters, $paginate);

        $firstSaleDate = WarehouseSale::query()
            ->where('warehouse_salesman_id', $salesman->id)
            ->min('sale_date');
        $firstYear = $firstSaleDate ? Carbon::parse($firstSaleDate)->year : now()->year;
        $oldestYear = min($firstYear, $filters['year'], now()->year);
        $newestYear = max(now()->year, $filters['year']);
        $yearOptions = range($newestYear, $oldestYear);

        return array_merge($report, compact('salesman', 'filters', 'yearOptions'));
    }

    public function salesReport(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        return view('warehouse-salesman.report', $this->salesReportPayload($request, $warehouse_salesman, true));
    }

    public function salesReportPrint(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        $data = $this->salesReportPayload($request, $warehouse_salesman, false);
        $data['autoPrint'] = true;

        return view('warehouse-salesman.report-export', $data);
    }

    public function salesReportPdf(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        $data = $this->salesReportPayload($request, $warehouse_salesman, false);
        $data['autoPrint'] = false;

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('warehouse-salesman.report-export', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'salesman-sales-' . $warehouse_salesman->id . '-' . $data['filters']['start_date'] . '-to-' . $data['filters']['end_date'] . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function salesReportExcel(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        $data = $this->salesReportPayload($request, $warehouse_salesman, false);
        $filename = 'salesman-sales-' . $warehouse_salesman->id . '-' . $data['filters']['start_date'] . '-to-' . $data['filters']['end_date'] . '.xlsx';

        return Excel::download(new WarehouseSalesmanSalesReportExport($data), $filename);
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $filters = $request->validate([
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
        ]);

        $selectedWarehouse = isset($filters['warehouse_id'])
            ? Warehouse::findOrFail($filters['warehouse_id'])
            : null;

        $warehouses = Warehouse::query()
            ->orderBy('name')
            ->get();

        $salesmen = WarehouseSalesman::query()
            ->with('warehouse')
            ->when($selectedWarehouse, function ($query) use ($selectedWarehouse) {
                $query->where('warehouse_id', $selectedWarehouse->id);
            })
            ->latest()
            ->get();

        return view('warehouse-salesman.index', compact('salesmen', 'selectedWarehouse', 'warehouses'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin();

        $filters = $request->validate([
            'warehouse_id' => ['nullable', 'integer', 'exists:lpep_warehouses,id'],
        ]);

        $warehouses = Warehouse::query()->orderBy('name')->get();
        $selectedWarehouseId = $filters['warehouse_id'] ?? null;

        return view('warehouse-salesman.create', compact('warehouses', 'selectedWarehouseId'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:lpep_warehouses,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = WarehouseSalesman::query()
                        ->where(function ($query) use ($value) {
                            $query->where('email', $value)
                                ->orWhere('username', $value);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The email or username has already been taken.');
                    }
                },
            ],
            'phone' => ['nullable', 'string', 'max:50', 'unique:users,mobile'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $plainPassword = $data['password'];
        $data['password'] = Hash::make($plainPassword);
        $data['created_by'] = Auth::id();

        $applicationUser = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($plainPassword),
                'mobile' => filled($data['phone'] ?? null) ? trim($data['phone']) : null,
            ]
        );
        $data['user_id'] = $applicationUser->id;

        WarehouseSalesman::create($data);

        return redirect()->route('warehouse-salesmen.index')->with('message', 'LSP created successfully.');
    }

    public function edit(WarehouseSalesman $warehouse_salesman)
    {
        $this->ensureAdmin();

        $warehouses = Warehouse::query()->orderBy('name')->get();

        return view('warehouse-salesman.edit', [
            'salesman' => $warehouse_salesman,
            'warehouses' => $warehouses,
        ]);
    }

    public function update(Request $request, WarehouseSalesman $warehouse_salesman)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:lpep_warehouses,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($warehouse_salesman) {
                    $exists = WarehouseSalesman::query()
                        ->where('id', '!=', $warehouse_salesman->id)
                        ->where(function ($query) use ($value) {
                            $query->where('email', $value)
                                ->orWhere('username', $value);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The email or username has already been taken.');
                    }
                },
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'status' => ['nullable', 'integer'],
        ]);

        $plainPassword = $data['password'] ?? null;
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $applicationUser = $warehouse_salesman->user_id
            ? User::find($warehouse_salesman->user_id)
            : User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make($plainPassword ?: Str::random(32))]
            );
        if ($applicationUser) {
            $applicationUser->name = $data['name'];
            $applicationUser->email = $data['email'];
            $applicationUser->save();
            $data['user_id'] = $applicationUser->id;
        }

        $warehouse_salesman->update($data);

        return redirect()->route('warehouse-salesmen.index')->with('message', 'LSP updated successfully.');
    }

    public function show(WarehouseSalesman $warehouse_salesman)
    {
        $this->ensureAdmin();

        $warehouse_salesman->load('warehouse');

        return view('warehouse-salesman.show', [
            'salesman' => $warehouse_salesman,
        ]);
    }

    public function destroy(WarehouseSalesman $warehouse_salesman)
    {
        $this->ensureAdmin();

        $warehouse_salesman->delete();

        return back()->with('message', 'LSP deleted successfully.');
    }
}
