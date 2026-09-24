<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WarehouseSale;
use App\Models\WarehouseSaleItem;
use App\Models\WarehouseSalePayment;
use App\Models\WarehouseSalesman;
use App\Models\WarehouseSalesmanAssignment;
use App\Models\WarehouseStockTransfer;
use App\Models\RenewableEnergy;
use App\Models\AppCustomer;
use App\Models\User;
use App\Models\Farm;
use App\Models\Cattle;
use App\Models\CattleGroup;
use App\Models\CattleBreed;
use App\Models\InsuranceCompany;
use App\Models\InsuranceType;
use App\Models\DiseaseHistory;
use App\Models\HealthInfo;
use App\Models\Calf;
use App\Models\CalfBirthProblem;
use App\Models\Union;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use App\Models\VisitInfo;
use App\Models\VisitFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Service\WarehouseInventoryService;
use App\Service\WarehouseSaleAdminSyncService;
use App\Exports\AppCustomerExport;
use App\Imports\BeneficiaryImport;
use Maatwebsite\Excel\Facades\Excel;

class WarehousePortalController extends Controller
{
    private function warehouseStock(int $warehouseId)
    {
        return Product::query()
            ->select('products.*')
            ->with('unit')
            ->warehouseStock($warehouseId)
            ->get()
            ->sort(function ($first, $second) {
                $firstAvailable = (float) ($first->warehouse_purchase_qty ?? 0) + (float) ($first->warehouse_transfer_qty ?? 0) - (float) ($first->warehouse_sale_qty ?? 0);
                $secondAvailable = (float) ($second->warehouse_purchase_qty ?? 0) + (float) ($second->warehouse_transfer_qty ?? 0) - (float) ($second->warehouse_sale_qty ?? 0);
                $firstIsOut = $firstAvailable <= 0;
                $secondIsOut = $secondAvailable <= 0;

                if ($firstIsOut !== $secondIsOut) {
                    return $firstIsOut ? 1 : -1;
                }

                if ($firstAvailable !== $secondAvailable) {
                    return $secondAvailable <=> $firstAvailable;
                }

                return strcasecmp((string) $first->product_name, (string) $second->product_name);
            })
            ->values();
    }

    private function currentWarehouse()
    {
        $warehouse = Auth::guard('warehouse')->user();

        if ($warehouse) {
            return $warehouse;
        }

        $salesman = Auth::guard('warehouse_salesman')->user();

        abort_unless($salesman && $salesman->warehouse, 403);

        return $salesman->warehouse;
    }

    private function currentPortalUser()
    {
        return Auth::guard('warehouse_salesman')->user()
            ?? Auth::guard('warehouse')->user();
    }

    private function ensureWarehouseAccount(): void
    {
        abort_unless(Auth::guard('warehouse')->check(), 403);
    }

    private function ensureSalesmanAccount(): void
    {
        abort_unless(Auth::guard('warehouse_salesman')->check(), 403, 'Only LSP accounts can create sales.');
    }

    private function ensureSaleAccess(WarehouseSale $sale, $warehouse): void
    {
        abort_unless((int) $sale->warehouse_id === (int) $warehouse->id, 403);

        $salesman = Auth::guard('warehouse_salesman')->user();

        if (!Auth::guard('warehouse')->check() && $salesman) {
            abort_unless((int) $sale->warehouse_salesman_id === (int) $salesman->id, 403);
        }
    }

    public function dashboard(WarehouseInventoryService $inventory)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $isSalesman = $portalUser instanceof WarehouseSalesman;

        $stock = $isSalesman
            ? Product::query()->select('products.*')->active()->with('unit')->salesmanStock($portalUser->id)->get()->map(function ($product) {
                $product->warehouse_purchase_qty = $product->salesman_assigned_qty;
                $product->warehouse_transfer_qty = 0;
                $product->warehouse_sale_qty = (float) ($product->salesman_sale_qty ?? 0) + (float) ($product->salesman_return_qty ?? 0);
                $product->company_stock_qty = max((float) ($product->warehouse_purchase_qty ?? 0) - (float) ($product->warehouse_sale_qty ?? 0), 0);
                $product->warehouse_available_qty = max((float) ($product->warehouse_purchase_qty ?? 0) - (float) ($product->warehouse_sale_qty ?? 0), 0);
                $product->salesman_stock_qty = max((float) ($product->warehouse_purchase_qty ?? 0) - (float) ($product->warehouse_sale_qty ?? 0), 0);
                return $product;
            })
            : Product::query()->select('products.*')->active()->with('unit')->warehouseStock($warehouse->id)->get()->map(function ($product) use ($inventory, $warehouse) {
                $companyStock = max(
                    $inventory->warehouseReceived($warehouse->id, $product->id) - $inventory->warehouseSold($warehouse->id, $product->id),
                    0
                );
                $warehouseStock = max($inventory->warehouseUnassignedAvailable($warehouse->id, $product->id), 0);
                $product->company_stock_qty = round($companyStock, 2);
                $product->warehouse_available_qty = round($warehouseStock, 2);
                $product->salesman_stock_qty = round(max($companyStock - $warehouseStock, 0), 2);
                return $product;
            });

        $todaySalesQuery = WarehouseSale::query()
            ->whereDate('sale_date', today())
            ->where('warehouse_id', $warehouse->id)
            ->when($isSalesman, fn ($query) => $query->where('warehouse_salesman_id', $portalUser->id));
        $todaySaleAmount = (clone $todaySalesQuery)->sum('total_amount');
        $todayCollectedAmount = (clone $todaySalesQuery)->sum('paid_amount');
        $todayDueAmount = (clone $todaySalesQuery)->sum('due_amount');

        $todaySales = WarehouseSalePayment::query()
            ->whereDate('payment_date', today())
            ->whereHas('sale', function ($query) use ($warehouse, $isSalesman, $portalUser) {
                $query->where('warehouse_id', $warehouse->id)
                    ->when($isSalesman, function ($query) use ($portalUser) {
                        $query->where('warehouse_salesman_id', $portalUser->id);
                    });
            })
            ->sum('amount');

        $portalSoldQuantity = $isSalesman
            ? (float) WarehouseSaleItem::query()
                ->whereHas('sale', function ($query) use ($warehouse, $portalUser) {
                    $query->where('warehouse_id', $warehouse->id)
                        ->where('warehouse_salesman_id', $portalUser->id);
                })
                ->sum('quantity')
            : null;

        return view('warehouse-portal.dashboard', compact('warehouse', 'stock', 'todaySales', 'todaySaleAmount', 'todayCollectedAmount', 'todayDueAmount', 'isSalesman', 'portalSoldQuantity'));
    }

    public function stock(Request $request)
    {
        $portalUser = $this->currentPortalUser();
        if ($portalUser instanceof WarehouseSalesman) {
            $warehouse = $portalUser->warehouse;
            $search = trim((string) $request->query('search', ''));
            $status = (string) $request->query('status', 'all');
            $perPage = (string) $request->query('per_page', 'all');
            if (!in_array($status, ['all', 'in', 'out'], true)) $status = 'all';
            if (!in_array($perPage, ['all', '10', '25', '50', '100'], true)) $perPage = 'all';

            $products = Product::query()->select('products.*')->active()->with('unit')->salesmanStock($portalUser->id)
                ->when($search !== '', fn ($query) => $query->where('products.product_name', 'like', '%' . $search . '%'))
                ->orderBy('product_name')->get()->map(function ($product) {
                    $received = (float) ($product->salesman_assigned_qty ?? 0);
                    $sold = (float) ($product->salesman_sale_qty ?? 0);
                    $returned = (float) ($product->salesman_return_qty ?? 0);
                    $netReceived = max($received - $returned, 0);
                    $stock = max($netReceived - $sold, 0);
                    $product->warehouse_purchase_qty = $netReceived;
                    $product->warehouse_transfer_qty = 0;
                    $product->warehouse_sale_qty = $sold;
                    $product->warehouse_available_qty = $stock;
                    $product->salesman_stock_qty = $stock;
                    return $product;
                });
            if ($status === 'in') $products = $products->filter(fn ($product) => $product->salesman_stock_qty > 0)->values();
            if ($status === 'out') $products = $products->filter(fn ($product) => $product->salesman_stock_qty <= 0)->values();
            $total = $products->count();
            $summary = (object) ['product_count' => $total, 'received_qty' => $products->sum('warehouse_purchase_qty'), 'sold_qty' => $products->sum(fn ($p) => (float) $p->salesman_sale_qty), 'stock_qty' => $products->sum('salesman_stock_qty')];
            if ($perPage === 'all') {
                $stock = new LengthAwarePaginator($products, $total, max($total, 1), 1, ['path' => $request->url(), 'query' => $request->query()]);
            } else {
                $stock = new LengthAwarePaginator($products->forPage((int) $request->query('page', 1), (int) $perPage)->values(), $total, (int) $perPage, (int) $request->query('page', 1), ['path' => $request->url(), 'query' => $request->query()]);
            }
            $isSalesman = true;
            $perPageOptions = ['all', 10, 25, 50, 100];
            return view('warehouse-portal.stock', compact('warehouse', 'stock', 'summary', 'search', 'status', 'perPage', 'perPageOptions', 'isSalesman'));
        }
        $this->ensureWarehouseAccount();
        return view('warehouse-portal.stock', $this->buildWarehouseStockPayload($request, true) + ['isSalesman' => false]);
    }

    public function receivedStock(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $isSalesman = $portalUser instanceof WarehouseSalesman;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'string', 'in:all,10,25,50,100'],
        ]);
        $search = trim((string) ($filters['search'] ?? ''));
        $fromDate = $filters['from_date'] ?? now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? now()->endOfMonth()->toDateString();
        $perPage = (string) ($filters['per_page'] ?? 'all');
        $transfers = WarehouseStockTransfer::query()
            ->where('warehouse_id', $warehouse->id)
            ->with('items.product')
            ->whereDate('transfer_date', '>=', $fromDate)
            ->whereDate('transfer_date', '<=', $toDate)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('product_name', 'like', '%' . $search . '%'));
                });
            })
            ->latest('transfer_date')
            ->latest('id');
        $filteredTotalQuantity = (clone $transfers)->sum('total_quantity');
        $transfers = $perPage === 'all'
            ? $transfers->get()
            : $transfers->paginate((int) $perPage)->withQueryString();

        return view('warehouse-portal.stock.received', [
            'title' => 'Received List',
            'warehouse' => $warehouse,
            'warehouseName' => $portalUser?->name ?? $warehouse->name,
            'warehouseEmail' => $portalUser?->email ?? null,
            'isSalesman' => $isSalesman,
            'transfers' => $transfers,
            'search' => $search,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'perPage' => $perPage,
            'filteredTotalQuantity' => $filteredTotalQuantity,
        ]);
    }

    public function receivedStockShow(WarehouseStockTransfer $transfer)
    {
        $warehouse = $this->currentWarehouse();
        abort_unless((int) $transfer->warehouse_id === (int) $warehouse->id, 403);
        $transfer->load('items.product');

        return view('warehouse-portal.stock.received-show', compact('transfer'));
    }

    public function salesmanReceivedStock(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'string', 'in:all,10,25,50,100'],
        ]);
        $search = trim((string) ($filters['search'] ?? ''));
        $fromDate = ($filters['from_date'] ?? '') ?: now()->startOfMonth()->toDateString();
        $toDate = ($filters['to_date'] ?? '') ?: now()->endOfMonth()->toDateString();
        $perPage = (string) ($filters['per_page'] ?? 'all');
        $query = WarehouseSalesmanAssignment::query()
            ->with('items.product')
            ->where('warehouse_salesman_id', $salesman->id)
            ->whereDate('assignment_date', '>=', $fromDate)
            ->whereDate('assignment_date', '<=', $toDate)
            ->when($search !== '', fn ($assignmentQuery) => $assignmentQuery->where(fn ($subQuery) => $subQuery
                ->where('invoice_no', 'like', "%{$search}%")
                ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('product_name', 'like', "%{$search}%"))));
        $filteredTotalQuantity = (float) (clone $query)->sum('total_quantity');
        $assignments = $perPage === 'all' ? $query->latest('assignment_date')->latest('id')->get() : $query->latest('assignment_date')->latest('id')->paginate((int) $perPage)->withQueryString();
        return view('warehouse-portal.stock.salesman-received', compact('assignments', 'search', 'fromDate', 'toDate', 'perPage', 'filteredTotalQuantity'));
    }

    public function salesmanReceivedStockShow(WarehouseSalesmanAssignment $assignment)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless((int) $assignment->warehouse_salesman_id === (int) $salesman->id, 403);

        $assignment->load('items.product');

        return view('warehouse-portal.stock.salesman-received-show', compact('assignment'));
    }

    public function renewableEnergyIndex(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $search = trim((string) $request->query('search', ''));
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $perPage = (string) $request->query('per_page', '10');
        if (! in_array($perPage, ['10', '25', '50', '100', 'all'], true)) {
            $perPage = '10';
        }
        $entries = RenewableEnergy::query()
            ->where('agent_id', $salesman->user_id)
            ->with(['area', 'agent', 'division', 'district', 'upazila', 'union'])
            ->when($fromDate, fn ($query) => $query->whereDate('visit_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('visit_date', '<=', $toDate))
            ->when($search !== '', fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('client_name', 'like', '%' . $search . '%')
                    ->orWhere('client_number', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhere('village', 'like', '%' . $search . '%');
            }))
            ->latest('visit_date')
            ->latest('id');
        $entries = $perPage === 'all' ? $entries->get() : $entries->paginate((int) $perPage)->withQueryString();

        return view('warehouse-portal.renewable-energy.index', $this->renewableEnergyViewData($salesman) + compact('entries', 'search', 'fromDate', 'toDate', 'perPage'));
    }

    public function renewableEnergyCreate()
    {
        $salesman = $this->renewableEnergySalesman();
        $divisions = Division::query()->orderBy('name')->pluck('name', 'id');
        return view('warehouse-portal.renewable-energy.create', $this->renewableEnergyViewData($salesman) + compact('divisions'));
    }

    public function renewableEnergyEdit(RenewableEnergy $renewableEnergy)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless((int) $renewableEnergy->agent_id === (int) $salesman->user_id, 404);
        $divisions = Division::query()->orderBy('name')->pluck('name', 'id');
        $districts = $renewableEnergy->division_id ? District::where('division_id', $renewableEnergy->division_id)->orderBy('name')->pluck('name', 'id') : collect();
        $upazilas = $renewableEnergy->district_id ? Upazila::where('district_id', $renewableEnergy->district_id)->orderBy('name')->pluck('name', 'id') : collect();
        $unions = $renewableEnergy->upazila_id ? Union::where('upazila_id', $renewableEnergy->upazila_id)->orderBy('name')->pluck('name', 'id') : collect();
        return view('warehouse-portal.renewable-energy.edit', $this->renewableEnergyViewData($salesman) + compact('renewableEnergy', 'divisions', 'districts', 'upazilas', 'unions'));
    }

    public function renewableEnergyUpdate(Request $request, RenewableEnergy $renewableEnergy)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless((int) $renewableEnergy->agent_id === (int) $salesman->user_id, 404);
        $data = $request->validate(['type'=>['required','in:biogas,solar'],'client_name'=>['required','string','max:255'],'client_number'=>['nullable','string','max:50'],'village'=>['nullable','string','max:255'],'livestock_details'=>['nullable','string'],'plant_size'=>['nullable','numeric','min:0'],'plant_size_unit'=>['nullable','string','max:20'],'plant_start_date'=>['nullable','date'],'plant_end_date'=>['nullable','date','after_or_equal:plant_start_date'],'po_name'=>['nullable','string','max:255'],'contribution_condition'=>['nullable','string'],'remarks'=>['nullable','string'],'document'=>['nullable','file','max:5120','mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar']]);
        foreach (['division_id','district_id','upazila_id','union_id'] as $field) $data[$field] = $request->input($field) ?: null;
        if ($request->hasFile('document')) { $file=$request->file('document'); $name=time().'_'.preg_replace('/[^A-Za-z0-9._-]/','_',$file->getClientOriginalName()); $file->move(public_path('uploads/renewable_energy'),$name); $data['document']='uploads/renewable_energy/'.$name; }
        $renewableEnergy->update($data);
        return redirect()->route('warehouse.renewable-energy.index')->with('message','Renewable Energy entry updated successfully.');
    }

    public function renewableEnergyDestroy(RenewableEnergy $renewableEnergy)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless((int) $renewableEnergy->agent_id === (int) $salesman->user_id, 404);
        $renewableEnergy->delete();
        return redirect()->route('warehouse.renewable-energy.index')->with('message', 'Renewable Energy entry deleted successfully.');
    }

    public function renewableEnergyStore(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $data = $request->validate([
            'type' => ['required', 'in:biogas,solar'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_number' => ['nullable', 'string', 'max:50'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'union_id' => ['nullable', 'integer', 'exists:unions,id'],
            'village' => ['nullable', 'string', 'max:255'],
            'livestock_details' => ['nullable', 'string'],
            'plant_size' => ['nullable', 'numeric', 'min:0'],
            'plant_size_unit' => ['nullable', 'string', 'max:20'],
            'plant_start_date' => ['nullable', 'date'],
            'plant_end_date' => ['nullable', 'date', 'after_or_equal:plant_start_date'],
            'po_name' => ['nullable', 'string', 'max:255'],
            'contribution_condition' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar'],
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $uploadPath = public_path('uploads/renewable_energy');
            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $fileName);
            $data['document'] = 'uploads/renewable_energy/' . $fileName;
        }

        RenewableEnergy::create($data + [
            'area_id' => $salesman->user_id,
            'agent_id' => $salesman->user_id,
            'visit_date' => now()->toDateString(),
            'plant_size_unit' => $data['plant_size_unit'] ?? 'm³',
            'created_by' => $salesman->user_id,
        ]);

        return redirect()->route('warehouse.renewable-energy.index')->with('message', 'Renewable Energy entry saved successfully.');
    }

    public function renewableEnergyDistricts(int $division)
    {
        $this->renewableEnergySalesman();
        return response()->json($this->banglaLocationOptions(District::query()->where('division_id', $division)->orderBy('name')->get(['id', 'name', 'bn_name'])));
    }

    public function renewableEnergyUpazilas(int $district)
    {
        $this->renewableEnergySalesman();
        return response()->json($this->banglaLocationOptions(Upazila::query()->where('district_id', $district)->orderBy('name')->get(['id', 'name', 'bn_name'])));
    }

    public function renewableEnergyUnions(int $upazila)
    {
        $this->renewableEnergySalesman();
        return response()->json($this->banglaLocationOptions(Union::query()->where('upazila_id', $upazila)->orderBy('name')->get(['id', 'name', 'bn_name'])));
    }

    private function banglaLocationOptions($locations)
    {
        return $locations->map(fn ($location) => [
            'id' => $location->id,
            'name' => $location->bn_name ?: $location->name,
            'bn_name' => $location->bn_name ?: $location->name,
            'en_name' => $location->name,
        ])->values();
    }

    private function renewableEnergySalesman(): WarehouseSalesman
    {
        $this->ensureSalesmanAccount();
        $salesman = Auth::guard('warehouse_salesman')->user();
        if ($salesman && !$salesman->user_id && $salesman->email) {
            $applicationUser = User::firstOrCreate(
                ['email' => $salesman->email],
                [
                    'name' => $salesman->name,
                    'password' => Hash::make(Str::random(32)),
                ]
            );
            $salesman->forceFill(['user_id' => $applicationUser->id])->save();
        }
        abort_unless($salesman?->user_id, 403, 'This LSP account is not linked to an application user.');
        return $salesman;
    }

    private function renewableEnergyViewData(WarehouseSalesman $salesman): array
    {
        return [
            'title' => 'Renewable Energy',
            'warehouseName' => $salesman->name,
            'warehouseEmail' => $salesman->email,
            'isSalesman' => true,
            'warehouse' => $salesman->warehouse,
        ];
    }

    public function productLspDistribution(Product $product)
    {
        $this->ensureWarehouseAccount();
        $warehouse = $this->currentWarehouse();

        $assigned = DB::table('lpep_warehouse_salesman_assignment_items as items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'items.warehouse_salesman_assignment_id')
            ->where('assignments.warehouse_id', $warehouse->id)
            ->where('items.product_id', $product->id)
            ->groupBy('assignments.warehouse_salesman_id')
            ->selectRaw('assignments.warehouse_salesman_id, SUM(items.quantity) as quantity');

        $sold = DB::table('lpep_warehouse_sale_items as items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->whereNotNull('sales.warehouse_salesman_id')
            ->where('items.product_id', $product->id)
            ->groupBy('sales.warehouse_salesman_id')
            ->selectRaw('sales.warehouse_salesman_id, SUM(items.quantity) as quantity');

        $returned = Schema::hasTable('lpep_warehouse_salesman_return_items')
            ? DB::table('lpep_warehouse_salesman_return_items as items')
                ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'items.warehouse_salesman_return_id')
                ->where('returns.warehouse_id', $warehouse->id)
                ->where('items.product_id', $product->id)
                ->groupBy('returns.warehouse_salesman_id')
                ->selectRaw('returns.warehouse_salesman_id, SUM(items.quantity) as quantity')
            : DB::query()->selectRaw('NULL as warehouse_salesman_id, 0 as quantity')->whereRaw('1 = 0');

        $distribution = WarehouseSalesman::query()
            ->where('lpep_warehouse_salesmen.warehouse_id', $warehouse->id)
            ->leftJoinSub($assigned, 'assigned_stock', fn ($join) => $join->on('assigned_stock.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id'))
            ->leftJoinSub($sold, 'sold_stock', fn ($join) => $join->on('sold_stock.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id'))
            ->leftJoinSub($returned, 'returned_stock', fn ($join) => $join->on('returned_stock.warehouse_salesman_id', '=', 'lpep_warehouse_salesmen.id'))
            ->select('lpep_warehouse_salesmen.*')
            ->selectRaw('COALESCE(assigned_stock.quantity, 0) as assigned_quantity')
            ->selectRaw('COALESCE(sold_stock.quantity, 0) as sold_quantity')
            ->selectRaw('COALESCE(returned_stock.quantity, 0) as returned_quantity')
            ->selectRaw('COALESCE(assigned_stock.quantity, 0) - COALESCE(returned_stock.quantity, 0) as net_assigned_quantity')
            ->selectRaw('COALESCE(assigned_stock.quantity, 0) - COALESCE(sold_stock.quantity, 0) - COALESCE(returned_stock.quantity, 0) as current_quantity')
            ->where(function ($query) {
                $query->whereRaw('COALESCE(assigned_stock.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(sold_stock.quantity, 0) > 0')
                    ->orWhereRaw('COALESCE(returned_stock.quantity, 0) > 0');
            })
            ->orderByDesc('current_quantity')
            ->orderBy('lpep_warehouse_salesmen.name')
            ->get();

        $totals = (object) [
            'assigned' => $distribution->sum('net_assigned_quantity'),
            'sold' => $distribution->sum('sold_quantity'),
            'returned' => $distribution->sum('returned_quantity'),
            'current' => $distribution->sum('current_quantity'),
        ];

        $product->load('unit');

        return view('warehouse-portal.stock-lsp-distribution', compact('warehouse', 'product', 'distribution', 'totals'));
    }

    private function buildWarehouseStockPayload(Request $request, bool $paginate): array
    {
        $warehouse = $this->currentWarehouse();
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $perPage = (string) $request->query('per_page', 'all');
        $salesmanId = (int) $request->query('salesman_id', 0);
        $salesmen = WarehouseSalesman::query()->where('warehouse_id', $warehouse->id)->whereNotNull('user_id')->orderBy('name')->get(['id', 'user_id', 'name', 'email']);
        if ($salesmanId > 0 && !$salesmen->pluck('id')->contains($salesmanId)) $salesmanId = 0;
        $statusOptions = ['all', 'in', 'out'];
        $perPageOptions = ['all', 10, 25, 50, 100];

        if (!in_array($status, $statusOptions, true)) {
            $status = 'all';
        }

        if (!in_array($perPage, array_map('strval', $perPageOptions), true)) {
            $perPage = 'all';
        }

        $purchased = DB::table('lpep_warehouse_purchase_items as purchase_items')
            ->join('lpep_warehouse_purchases as purchases', 'purchases.id', '=', 'purchase_items.warehouse_purchase_id')
            ->where('purchases.warehouse_id', $warehouse->id)
            ->groupBy('purchase_items.product_id')
            ->selectRaw('purchase_items.product_id, SUM(purchase_items.quantity) as quantity');

        $transferred = DB::table('lpep_warehouse_stock_transfer_items as transfer_items')
            ->join('lpep_warehouse_stock_transfers as transfers', 'transfers.id', '=', 'transfer_items.warehouse_stock_transfer_id')
            ->where('transfers.warehouse_id', $warehouse->id)
            ->groupBy('transfer_items.product_id')
            ->selectRaw('transfer_items.product_id, SUM(transfer_items.quantity) as quantity');

        $sold = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->when($salesmanId > 0, fn ($query) => $query->where('sales.warehouse_salesman_id', $salesmanId))
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as quantity');

        $assigned = DB::table('lpep_warehouse_salesman_assignment_items as assignment_items')
            ->join('lpep_warehouse_salesman_assignments as assignments', 'assignments.id', '=', 'assignment_items.warehouse_salesman_assignment_id')
            ->where('assignments.warehouse_id', $warehouse->id)
            ->when($salesmanId > 0, fn ($query) => $query->where('assignments.warehouse_salesman_id', $salesmanId))
            ->groupBy('assignment_items.product_id')
            ->selectRaw('assignment_items.product_id, SUM(assignment_items.quantity) as quantity');

        $returned = Schema::hasTable('lpep_warehouse_salesman_return_items')
            ? DB::table('lpep_warehouse_salesman_return_items as return_items')
                ->join('lpep_warehouse_salesman_returns as returns', 'returns.id', '=', 'return_items.warehouse_salesman_return_id')
                ->where('returns.warehouse_id', $warehouse->id)
                ->when($salesmanId > 0, fn ($query) => $query->where('returns.warehouse_salesman_id', $salesmanId))
                ->groupBy('return_items.product_id')
                ->selectRaw('return_items.product_id, SUM(return_items.quantity) as quantity')
            : DB::query()->selectRaw('NULL as product_id, 0 as quantity')->whereRaw('1 = 0');

        $directSold = DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->whereNull('sales.warehouse_salesman_id')
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, SUM(sale_items.quantity) as quantity');

        $stockQuery = Product::query()
            ->leftJoinSub($purchased, 'warehouse_purchases', fn ($join) => $join->on('warehouse_purchases.product_id', '=', 'products.id'))
            ->leftJoinSub($transferred, 'warehouse_transfers', fn ($join) => $join->on('warehouse_transfers.product_id', '=', 'products.id'))
            ->leftJoinSub($sold, 'warehouse_sales', fn ($join) => $join->on('warehouse_sales.product_id', '=', 'products.id'))
            ->leftJoinSub($assigned, 'warehouse_assigned', fn ($join) => $join->on('warehouse_assigned.product_id', '=', 'products.id'))
            ->leftJoinSub($returned, 'warehouse_returned', fn ($join) => $join->on('warehouse_returned.product_id', '=', 'products.id'))
            ->leftJoinSub($directSold, 'warehouse_direct_sales', fn ($join) => $join->on('warehouse_direct_sales.product_id', '=', 'products.id'))
            ->active();

        $companyStockExpr = 'COALESCE(warehouse_purchases.quantity, 0) + COALESCE(warehouse_transfers.quantity, 0) - COALESCE(warehouse_sales.quantity, 0)';
        $warehouseAvailableExpr = 'COALESCE(warehouse_purchases.quantity, 0) + COALESCE(warehouse_transfers.quantity, 0) - COALESCE(warehouse_assigned.quantity, 0) + COALESCE(warehouse_returned.quantity, 0) - COALESCE(warehouse_direct_sales.quantity, 0)';
        $salesmanStockExpr = '(' . $companyStockExpr . ') - (' . $warehouseAvailableExpr . ')';

        $summary = (clone $stockQuery)
            ->selectRaw('COUNT(products.id) as product_count')
            ->selectRaw('COALESCE(SUM(COALESCE(warehouse_purchases.quantity, 0) + COALESCE(warehouse_transfers.quantity, 0)), 0) as purchased_qty')
            ->selectRaw('COALESCE(SUM(GREATEST(' . $companyStockExpr . ', 0)), 0) as company_stock_qty')
            ->selectRaw('COALESCE(SUM(GREATEST(' . $warehouseAvailableExpr . ', 0)), 0) as warehouse_available_qty')
            ->selectRaw('COALESCE(SUM(GREATEST(' . $salesmanStockExpr . ', 0)), 0) as salesman_stock_qty')
            ->first();

        $summary->received_qty = (float) ($summary->purchased_qty ?? 0);
        $summary->assigned_qty = (float) (clone $assigned)->get()->sum('quantity');
        $summary->returned_qty = (float) (clone $returned)->get()->sum('quantity');
        $summary->direct_sale_qty = (float) (clone $directSold)->get()->sum('quantity');
        $summary->lsp_sale_qty = max((float) DB::table('lpep_warehouse_sale_items as sale_items')
            ->join('lpep_warehouse_sales as sales', 'sales.id', '=', 'sale_items.warehouse_sale_id')
            ->where('sales.warehouse_id', $warehouse->id)
            ->whereNotNull('sales.warehouse_salesman_id')
            ->sum('sale_items.quantity'), 0);
        $summary->office_stock_qty = (float) ($summary->warehouse_available_qty ?? 0);
        $summary->lsp_stock_qty = (float) ($summary->salesman_stock_qty ?? 0);
        $summary->total_remaining_qty = (float) ($summary->company_stock_qty ?? 0);

        $stockQuery = $stockQuery
            ->select('products.*')
            ->selectRaw('COALESCE(warehouse_purchases.quantity, 0) as warehouse_purchase_qty')
            ->selectRaw('COALESCE(warehouse_transfers.quantity, 0) as warehouse_transfer_qty')
            ->selectRaw('COALESCE(warehouse_sales.quantity, 0) as warehouse_sale_qty')
            ->selectRaw('COALESCE(warehouse_assigned.quantity, 0) as warehouse_assigned_qty')
            ->selectRaw('COALESCE(warehouse_returned.quantity, 0) as warehouse_return_qty')
            ->selectRaw('COALESCE(warehouse_direct_sales.quantity, 0) as warehouse_direct_sale_qty')
            ->selectRaw('GREATEST(' . $companyStockExpr . ', 0) as company_stock_qty')
            ->selectRaw('GREATEST(' . $warehouseAvailableExpr . ', 0) as warehouse_available_qty')
            ->selectRaw('GREATEST(' . $salesmanStockExpr . ', 0) as salesman_stock_qty')
            ->with('unit')
            ->when($search !== '', fn ($query) => $query->where('products.product_name', 'like', '%' . $search . '%'))
            ->when($status === 'in', fn ($query) => $query->whereRaw('GREATEST(' . $warehouseAvailableExpr . ', 0) > 0'))
            ->when($status === 'out', fn ($query) => $query->whereRaw('GREATEST(' . $warehouseAvailableExpr . ', 0) <= 0'))
            ->orderByRaw('CASE WHEN GREATEST(' . $warehouseAvailableExpr . ', 0) <= 0 THEN 1 ELSE 0 END')
            ->orderByRaw('GREATEST(' . $warehouseAvailableExpr . ', 0) DESC')
            ->orderBy('products.product_name');

        $formatProduct = function ($product) {
            $receivedQty = (float) ($product->warehouse_purchase_qty ?? 0) + (float) ($product->warehouse_transfer_qty ?? 0);
            $lspSaleQty = (float) ($product->warehouse_sale_qty ?? 0) - (float) ($product->warehouse_direct_sale_qty ?? 0);
            $product->received_qty = round($receivedQty, 2);
            $product->assigned_qty = round((float) ($product->warehouse_assigned_qty ?? 0), 2);
            $product->returned_qty = round((float) ($product->warehouse_return_qty ?? 0), 2);
            $product->direct_sale_qty = round((float) ($product->warehouse_direct_sale_qty ?? 0), 2);
            $product->lsp_sale_qty = round(max($lspSaleQty, 0), 2);
            $product->office_stock_qty = round((float) ($product->warehouse_available_qty ?? 0), 2);
            $product->lsp_stock_qty = round((float) ($product->salesman_stock_qty ?? 0), 2);
            $product->total_remaining_qty = round((float) ($product->company_stock_qty ?? 0), 2);
            $product->in_qty = round((float) ($product->warehouse_transfer_qty ?? 0) + (float) ($product->warehouse_return_qty ?? 0), 2);
            $product->out_qty = round((float) ($product->warehouse_direct_sale_qty ?? 0) + (float) ($product->warehouse_assigned_qty ?? 0), 2);
            $product->stock_qty = round((float) ($product->warehouse_available_qty ?? 0), 2);
            $product->stock_sale_price = round($product->total_remaining_qty * (float) ($product->selling_price ?? 0), 2);
            $product->stock_purchase_price = round($product->total_remaining_qty * (float) ($product->purchase_price ?? 0), 2);
            return $product;
        };

        $pageSize = $perPage === 'all'
            ? max(1, (clone $stockQuery)->count())
            : (int) $perPage;

        $stock = $paginate
            ? $stockQuery->paginate($pageSize)->withQueryString()
            : $stockQuery->get();

        if ($paginate) {
            $stock->getCollection()->transform($formatProduct);
        } else {
            $stock = $stock->map($formatProduct)->values();
        }

        return [
            'warehouse' => $warehouse,
            'stock' => $stock,
            'summary' => $summary,
            'search' => $search,
            'status' => $status,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'salesmanId' => $salesmanId,
            'salesmen' => $salesmen,
            'reportTitle' => 'Area Office Stock',
            'warehouseLabel' => $warehouse->name . ($warehouse->code ? ' (' . $warehouse->code . ')' : ''),
            'salesmanLabel' => 'All LSPs',
        ];
    }

    public function stockPrint(Request $request)
    {
        $this->ensureWarehouseAccount();

        return view('warehouse.stock-export', array_merge($this->buildWarehouseStockPayload($request, false), [
            'reportType' => 'print',
        ]));
    }

    public function stockPdf(Request $request)
    {
        $this->ensureWarehouseAccount();

        $data = array_merge($this->buildWarehouseStockPayload($request, false), [
            'reportType' => 'pdf',
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('warehouse.stock-export', $data)->render());
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="warehouse-stock-report.pdf"',
        ]);
    }

    public function stockExcel(Request $request)
    {
        $this->ensureWarehouseAccount();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\WarehouseStockExport($this->buildWarehouseStockPayload($request, false)),
            'warehouse-stock-report.xlsx'
        );
    }

    public function index(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $isSalesman = $portalUser instanceof WarehouseSalesman;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'payment_status' => ['nullable', 'in:all,paid,due'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'string', 'in:all,10,25,50,100'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $salesmanId = isset($filters['salesman_id']) ? (int) $filters['salesman_id'] : null;
        $paymentStatus = $filters['payment_status'] ?? 'all';
        $fromDate = ($filters['from_date'] ?? '') ?: now()->startOfMonth()->toDateString();
        $toDate = ($filters['to_date'] ?? '') ?: now()->endOfMonth()->toDateString();
        $perPage = (string) ($filters['per_page'] ?? 'all');
        if (!in_array($perPage, ['all', '10', '25', '50', '100'], true)) {
            $perPage = 'all';
        }

        $salesmanOptions = WarehouseSalesman::query()
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $salesQuery = WarehouseSale::query()
            ->with(['salesman', 'payments'])
            ->where('warehouse_id', $warehouse->id)
            ->when($isSalesman, function ($query) use ($portalUser) {
                $query->where('warehouse_salesman_id', $portalUser->id);
            })
            ->when($salesmanId, function ($query) use ($salesmanId, $isSalesman, $portalUser) {
                if (! $isSalesman) {
                    $query->where('warehouse_salesman_id', $salesmanId);
                }
            })
            ->when($fromDate, fn ($query) => $query->whereDate('sale_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('sale_date', '<=', $toDate))
            ->when($paymentStatus === 'paid', fn ($query) => $query->where('due_amount', '<=', 0))
            ->when($paymentStatus === 'due', fn ($query) => $query->where('due_amount', '>', 0))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
                        ->orWhere('customer_address', 'like', '%' . $search . '%')
                        ->orWhereDate('sale_date', $search);
                });
            });

        $filteredSaleIds = (clone $salesQuery)->pluck('id');

        $filteredTotalAmount = (float) WarehouseSalePayment::query()
            ->whereIn('warehouse_sale_id', $filteredSaleIds)
            ->when($fromDate, fn ($query) => $query->whereDate('payment_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('payment_date', '<=', $toDate))
            ->sum('amount');
        $filteredDueAmount = (float) (clone $salesQuery)->sum('due_amount');
        $filteredSalesAmount = (float) (clone $salesQuery)->sum('total_amount');

        if ($isSalesman) {
            $salesQuery->orderByDesc('sale_date')
                ->orderByDesc('created_at')
                ->orderByDesc('id');
        } else {
            $salesQuery->orderByDesc('sale_date')
                ->orderByDesc('created_at')
                ->orderByDesc('id');
        }
        $sales = $perPage === 'all'
            ? $salesQuery->get()
            : $salesQuery->paginate((int) $perPage)->withQueryString();

        return view('warehouse-portal.sales.index', compact(
            'warehouse',
            'sales',
            'search',
            'salesmanId',
            'paymentStatus',
            'fromDate',
            'toDate',
            'salesmanOptions',
            'filteredTotalAmount',
            'filteredSalesAmount',
            'filteredDueAmount',
            'perPage'
        ));
    }

    public function dueAmount()
    {
        return redirect()->route('warehouse.sales.index', ['payment_status' => 'due']);
    }

    public function wallet(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $search = trim((string) $request->query('search', ''));
        $perPage = (string) $request->query('per_page', '10');
        if (!in_array($perPage, ['10', '25', '50', '100', 'all'], true)) $perPage = '10';
        $customerQuery = AppCustomer::query()->where('agent_id', $salesman->user_id)
            ->whereHas('agentCustomerTransactions', fn ($query) => $query->where('type', TXN_SEND))
            ->when($search !== '', fn ($query) => $query->where(fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%")->orWhere('beneficiary_number', 'like', "%{$search}%")))
            ->withSum(['agentCustomerTransactions as amount' => fn ($query) => $query->where('type', TXN_SEND)], 'amount')
            ->withSum(['agentCustomerTransactions as available_amount' => fn ($query) => $query->where('type', TXN_SEND)], 'available_amount')
            ->orderByDesc('id');
        $customers = $perPage === 'all' ? $customerQuery->get() : $customerQuery->paginate((int) $perPage)->withQueryString();
        $user = $salesman->user;
        $myPoints = $user ? (float) $user->receivedLoansFromAdmin()->sum('amount') - (float) $user->sentLoansToCustomers()->sum('amount') : 0;
        return view('warehouse-portal.wallet.index', compact('customers', 'search', 'perPage', 'myPoints'));
    }

    public function farms(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $search = trim((string) $request->query('search', ''));
        $perPage = (string) $request->query('per_page', '10');
        $perPage = in_array($perPage, ['10', '25', '50', '100'], true) ? $perPage : '10';
        $farms = Farm::query()->with('customer')->withCount(['cattle', 'calves'])
            ->whereHas('customer', fn ($query) => $query->where('agent_id', $salesman->user_id))
            ->when($search !== '', fn ($query) => $query->where(fn ($subQuery) => $subQuery->where('name', 'like', "%{$search}%")->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"))))
            ->latest('id')->paginate((int) $perPage)->withQueryString();
        $customers = AppCustomer::where('agent_id', $salesman->user_id)->orderBy('name')->get(['id', 'name', 'mobile']);
        $divisions = Division::orderBy('name')->get(['id', 'name', 'bn_name'])
            ->mapWithKeys(fn ($division) => [$division->id => $division->bn_name ?: $division->name]);
        return view('warehouse-portal.farms.index', compact('farms', 'search', 'perPage', 'customers', 'divisions'));
    }

    public function storeFarm(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $data = $request->validate(['app_customer_id' => ['required', 'exists:app_customers,id'], 'name' => ['required', 'string', 'max:255'], 'division_id' => ['nullable', 'exists:divisions,id'], 'district_id' => ['nullable', 'exists:districts,id'], 'upazila_id' => ['nullable', 'exists:upazilas,id'], 'union_id' => ['nullable', 'exists:unions,id']]);
        abort_unless(AppCustomer::where('id', $data['app_customer_id'])->where('agent_id', $salesman->user_id)->exists(), 403);
        Farm::create($data + ['created_by' => $salesman->user_id]);
        return back()->with('message', 'Farm added successfully.');
    }

    public function updateFarm(Request $request, Farm $farm)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless($farm->customer && (int) $farm->customer->agent_id === (int) $salesman->user_id, 403);
        $data = $request->validate(['app_customer_id' => ['required', 'exists:app_customers,id'], 'name' => ['required', 'string', 'max:255'], 'division_id' => ['nullable', 'exists:divisions,id'], 'district_id' => ['nullable', 'exists:districts,id'], 'upazila_id' => ['nullable', 'exists:upazilas,id'], 'union_id' => ['nullable', 'exists:unions,id']]);
        abort_unless(AppCustomer::where('id', $data['app_customer_id'])->where('agent_id', $salesman->user_id)->exists(), 403);
        $farm->update($data + ['updated_by' => $salesman->user_id]);
        return back()->with('message', 'Farm updated successfully.');
    }

    public function destroyFarm(Farm $farm)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless($farm->customer && (int) $farm->customer->agent_id === (int) $salesman->user_id, 403);
        $farm->delete();
        return back()->with('message', 'Farm deleted successfully.');
    }

    public function cattle(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $search = trim((string) $request->query('search', ''));
        $perPage = in_array((string) $request->query('per_page', '10'), ['10', '25', '50', '100'], true) ? (string) $request->query('per_page', '10') : '10';
        $farmScope = fn ($query) => $query->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id));
        $cattle = Cattle::query()->with('farm.customer')->whereHas('farm', $farmScope)
            ->when($search !== '', fn ($query) => $query->where(fn ($sub) => $sub->where('tag', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhereHas('farm', fn ($farm) => $farm->where('name', 'like', "%{$search}%"))))
            ->latest('id')->paginate((int) $perPage)->withQueryString();
        $options = $this->cattleOptions($salesman);
        return view('warehouse-portal.cattle.index', compact('cattle', 'search', 'perPage', 'options'));
    }

    public function createCattle()
    {
        $salesman = $this->renewableEnergySalesman();
        $options = $this->cattleOptions($salesman);
        return view('warehouse-portal.cattle.create', compact('options'));
    }

    private function cattleOptions(WarehouseSalesman $salesman): array
    {
        return [
            'farms' => Farm::query()->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->orderBy('name')->pluck('name', 'id'),
            'groups' => CattleGroup::orderBy('name')->pluck('name', 'id'),
            'breeds' => CattleBreed::orderBy('name')->pluck('name', 'id'),
            'companies' => InsuranceCompany::orderBy('name')->pluck('name', 'id'),
            'types' => InsuranceType::orderBy('name')->pluck('name', 'id'),
            'diseaseHistories' => DiseaseHistory::orderBy('name')->pluck('name', 'id'),
            'healthInfos' => HealthInfo::orderBy('name')->pluck('name', 'id'),
        ];
    }

    public function calves(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $search = trim((string) $request->query('search', ''));
        $perPage = in_array((string) $request->query('per_page', '10'), ['10', '25', '50', '100'], true) ? (string) $request->query('per_page', '10') : '10';
        $calves = Calf::query()->with(['farm', 'cattle'])->whereHas('farm.customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))
            ->when($search !== '', fn ($query) => $query->where(fn ($sub) => $sub->where('tag', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhereHas('farm', fn ($farm) => $farm->where('name', 'like', "%{$search}%"))->orWhereHas('cattle', fn ($cattle) => $cattle->where('name', 'like', "%{$search}%"))))
            ->latest('id')->paginate((int) $perPage)->withQueryString();
        return view('warehouse-portal.calves.index', compact('calves', 'search', 'perPage'));
    }

    public function createCalf()
    {
        $salesman = $this->renewableEnergySalesman();
        $farms = Farm::query()->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->orderBy('name')->pluck('name', 'id');
        $cattle = Cattle::query()->whereHas('farm.customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->orderBy('name')->pluck('name', 'id');
        $birthProblems = CalfBirthProblem::orderBy('name')->pluck('name', 'id');
        return view('warehouse-portal.calves.create', compact('farms', 'cattle', 'birthProblems'));
    }

    public function storeCalf(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $data = $request->validate(['farm_id'=>['required','exists:farms,id'],'cattle_id'=>['required','exists:cattle,id'],'tag'=>['required','string','max:255'],'name'=>['required','string','max:255'],'birth_date'=>['required','date'],'weight'=>['required','string','max:255'],'gender'=>['required','string','max:255'],'image'=>['nullable','image','max:5120'],'calf_birth_problem_ids'=>['nullable','array'],'calf_birth_problem_ids.*'=>['integer','exists:calf_birth_problems,id']]);
        abort_unless(Farm::whereKey($data['farm_id'])->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->exists(), 403);
        abort_unless(Cattle::whereKey($data['cattle_id'])->where('farm_id', $data['farm_id'])->exists(), 422, 'Please select a cattle from the selected farm.');
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('calves', 'public');
        $calf = Calf::create(collect($data)->except('calf_birth_problem_ids')->all() + ['created_by'=>$salesman->user_id,'created_by_type'=>'warehouse_salesman']);
        $calf->birthProblems()->createMany(collect($data['calf_birth_problem_ids'] ?? [])->map(fn ($id) => ['calf_birth_problem_id' => $id])->all());
        return redirect()->route('warehouse.calves')->with('message', 'Calf added successfully.');
    }

    public function storeCattle(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $data = $request->validate(['farm_id'=>['required','exists:farms,id'],'tag'=>['required','string','max:255'],'name'=>['required','string','max:255'],'cattle_group_id'=>['required','exists:cattle_groups,id'],'cattle_breed_id'=>['required','exists:cattle_breeds,id'],'birth_date'=>['required','date'],'weight'=>['required','string','max:255'],'gender'=>['required','string','max:255'],'health_problem'=>['required','string','max:255'],'avg_milk_production'=>['nullable','string','max:255'],'milk_production_status'=>['nullable','string','max:255'],'calf_count'=>['nullable','string','max:255'],'last_calf_birth_date'=>['nullable','date'],'genetic_percentage'=>['nullable','string','max:255'],'insurance_company_id'=>['required','exists:insurance_companies,id'],'insurance_type_id'=>['required','exists:insurance_types,id'],'insurance_no'=>['required','string','max:255'],'disease_history_ids'=>['nullable','array'],'disease_history_ids.*'=>['integer','exists:disease_histories,id'],'health_info_ids'=>['nullable','array'],'health_info_ids.*'=>['integer','exists:health_infos,id']]);
        abort_unless(Farm::whereKey($data['farm_id'])->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->exists(), 403);
        $cattle = Cattle::create(collect($data)->except(['disease_history_ids', 'health_info_ids'])->all() + ['created_by'=>$salesman->user_id,'created_by_type'=>'warehouse_salesman']);
        $cattle->diseaseHistories()->createMany(collect($data['disease_history_ids'] ?? [])->map(fn ($id) => ['disease_history_id' => $id])->all());
        $cattle->healthInfos()->createMany(collect($data['health_info_ids'] ?? [])->map(fn ($id) => ['health_info_id' => $id])->all());
        return back()->with('message', 'Cattle added successfully.');
    }

    public function updateCattle(Request $request, Cattle $cattle)
    {
        $salesman = $this->renewableEnergySalesman();
        abort_unless($cattle->farm && $cattle->farm->customer && (int) $cattle->farm->customer->agent_id === (int) $salesman->user_id, 403);
        $request->merge(['_method' => 'PUT']);
        return $this->storeOrUpdateCattle($request, $cattle, $salesman);
    }

    private function storeOrUpdateCattle(Request $request, Cattle $cattle, WarehouseSalesman $salesman)
    {
        $data = $request->validate(['farm_id'=>['required','exists:farms,id'],'tag'=>['required','string','max:255'],'name'=>['required','string','max:255'],'cattle_group_id'=>['required','exists:cattle_groups,id'],'cattle_breed_id'=>['required','exists:cattle_breeds,id'],'birth_date'=>['required','date'],'weight'=>['required','string','max:255'],'gender'=>['required','string','max:255'],'health_problem'=>['required','string','max:255'],'avg_milk_production'=>['nullable','string','max:255'],'milk_production_status'=>['nullable','string','max:255'],'calf_count'=>['nullable','string','max:255'],'last_calf_birth_date'=>['nullable','date'],'genetic_percentage'=>['nullable','string','max:255'],'insurance_company_id'=>['required','exists:insurance_companies,id'],'insurance_type_id'=>['required','exists:insurance_types,id'],'insurance_no'=>['required','string','max:255'],'disease_history_ids'=>['nullable','array'],'disease_history_ids.*'=>['integer','exists:disease_histories,id'],'health_info_ids'=>['nullable','array'],'health_info_ids.*'=>['integer','exists:health_infos,id']]);
        abort_unless(Farm::whereKey($data['farm_id'])->whereHas('customer', fn ($customer) => $customer->where('agent_id', $salesman->user_id))->exists(), 403);
        $cattle->update(collect($data)->except(['disease_history_ids', 'health_info_ids'])->all() + ['updated_by'=>$salesman->user_id,'updated_by_type'=>'warehouse_salesman']);
        $cattle->diseaseHistories()->delete();
        $cattle->healthInfos()->delete();
        $cattle->diseaseHistories()->createMany(collect($data['disease_history_ids'] ?? [])->map(fn ($id) => ['disease_history_id' => $id])->all());
        $cattle->healthInfos()->createMany(collect($data['health_info_ids'] ?? [])->map(fn ($id) => ['health_info_id' => $id])->all());
        return back()->with('message', 'Cattle updated successfully.');
    }

    public function dailyVisits(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $isSalesman = $portalUser instanceof WarehouseSalesman;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page' => ['nullable', 'string', 'in:all,10,25,50,100'],
        ]);
        $search = trim((string) ($filters['search'] ?? ''));
        $salesmanId = $isSalesman ? $portalUser->id : ($filters['salesman_id'] ?? null);
        $fromDate = ($filters['from_date'] ?? '') ?: now()->startOfMonth()->toDateString();
        $toDate = ($filters['to_date'] ?? '') ?: now()->endOfMonth()->toDateString();
        $perPage = (string) ($filters['per_page'] ?? 'all');
        $salesmen = WarehouseSalesman::where('warehouse_id', $warehouse->id)->whereNotNull('user_id')->when($isSalesman, fn ($query) => $query->whereKey($portalUser->id))->orderBy('name')->get(['id', 'name', 'user_id']);
        $salesmanIds = $salesmen->when($salesmanId, fn ($items) => $items->where('id', $salesmanId))->pluck('user_id')->all();

        $visitsQuery = VisitInfo::query()
            ->with(['appCustomer', 'agent', 'fees'])
            ->whereIn('agent_id', $salesmanIds)
            ->when($fromDate, fn ($query) => $query->whereDate('visit_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('visit_date', '<=', $toDate))
            ->when($search !== '', fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('customer_number', 'like', '%' . $search . '%')
                    ->orWhere('beneficiary_number', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('appCustomer', fn ($customer) => $customer->where('name', 'like', '%' . $search . '%'));
            }))
            ;

        if ($isSalesman) {
            $visitsQuery->latest('visit_date')
                ->latest('created_at')
                ->latest('id');
        } else {
            $visitsQuery->latest('visit_date')
                ->latest('created_at')
                ->latest('id');
        }

        $totalAmount = (clone $visitsQuery)->get()->sum(fn ($visit) => (float) $visit->fees->sum('amount'));
        $visits = $perPage === 'all'
            ? $visitsQuery->get()
            : $visitsQuery->paginate((int) $perPage)->withQueryString();

        $isPaginated = $visits instanceof \Illuminate\Pagination\AbstractPaginator;
        $visitRows = $isPaginated ? $visits->getCollection() : $visits;
        $visitRows->transform(function ($visit) {
            $visit->display_visit_date = $visit->visit_date
                ? $visit->visit_date->format('d-m-Y') . ' ' . ($visit->created_at?->format('h:i A') ?? '')
                : '-';
            return $visit;
        });

        return view('warehouse-portal.visits.index', compact('warehouse', 'visits', 'isSalesman', 'salesmen', 'search', 'salesmanId', 'fromDate', 'toDate', 'perPage', 'isPaginated', 'totalAmount'));
    }

    public function dailyVisitsPrint(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $isSalesman = $portalUser instanceof WarehouseSalesman;
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'salesman_id' => ['nullable', 'integer', 'exists:lpep_warehouse_salesmen,id'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);
        $fromDate = ($filters['from_date'] ?? '') ?: now()->startOfMonth()->toDateString();
        $toDate = ($filters['to_date'] ?? '') ?: now()->endOfMonth()->toDateString();
        $search = trim((string) ($filters['search'] ?? ''));
        $salesmanId = $isSalesman ? $portalUser->id : ($filters['salesman_id'] ?? null);
        $salesmen = WarehouseSalesman::where('warehouse_id', $warehouse->id)->whereNotNull('user_id')->when($isSalesman, fn ($query) => $query->whereKey($portalUser->id))->orderBy('name')->get(['id', 'name', 'user_id']);
        $salesmanIds = $salesmen->when($salesmanId, fn ($items) => $items->where('id', $salesmanId))->pluck('user_id')->all();
        $visits = VisitInfo::with(['appCustomer', 'agent', 'fees'])->whereIn('agent_id', $salesmanIds)
            ->whereDate('visit_date', '>=', $fromDate)->whereDate('visit_date', '<=', $toDate)
            ->when($search !== '', fn ($query) => $query->where(function ($subQuery) use ($search) {
                $subQuery->where('customer_number', 'like', '%' . $search . '%')->orWhere('beneficiary_number', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%')->orWhereHas('appCustomer', fn ($customer) => $customer->where('name', 'like', '%' . $search . '%'));
            }))->latest('visit_date')->latest('created_at')->latest('id')->get();
        $visits->transform(function ($visit) {
            $visit->display_visit_date = $visit->visit_date
                ? $visit->visit_date->format('d-m-Y') . ' ' . ($visit->created_at?->format('h:i A') ?? '')
                : '-';
            return $visit;
        });
        $totalAmount = $visits->sum(fn ($visit) => (float) $visit->fees->sum('amount'));
        $workingDays = collect(\Carbon\CarbonPeriod::create($fromDate, $toDate))->filter(fn ($date) => $date->dayOfWeek !== \Carbon\Carbon::FRIDAY)->count();
        $areaNames = $visits->map(fn ($visit) => $visit->appCustomer?->village ?: $visit->appCustomer?->unions)->filter()->unique()->values();
        $groupNumbers = $visits->map(fn ($visit) => $visit->appCustomer?->group_number)->filter()->unique()->values();
        $visitTypeCounts = $visits->groupBy('visit_type')->map->count();
        $feeSummary = $visits->flatMap->fees->groupBy('fee_type')->map(fn ($fees) => (float) $fees->sum('amount'));
        return view('warehouse-portal.visits.print', compact('warehouse', 'visits', 'salesmen', 'fromDate', 'toDate', 'search', 'salesmanId', 'totalAmount', 'workingDays', 'areaNames', 'groupNumbers', 'visitTypeCounts', 'feeSummary'));
    }

    public function dailyVisitsReport(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $fromDate = $request->input('visit_date_from', now()->startOfMonth()->toDateString());
        $toDate = $request->input('visit_date_to', now()->endOfMonth()->toDateString());
        $visits = ($fromDate && $toDate)
            ? VisitInfo::with(['appCustomer', 'agent', 'fees'])->where('agent_id', $salesman->user_id)->whereBetween('visit_date', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->latest('visit_date')->latest('created_at')->latest('id')->get()
            : collect();
        $workingDays = ($fromDate && $toDate) ? collect(\Carbon\CarbonPeriod::create($fromDate, $toDate))->filter(fn ($date) => $date->dayOfWeek !== \Carbon\Carbon::FRIDAY)->count() : 0;
        $areaNames = $visits->map(fn ($visit) => $visit->appCustomer?->village ?: $visit->appCustomer?->unions)->filter()->unique()->values();
        $visitTypeCounts = $visits->groupBy('visit_type')->map->count();
        $feeSummary = $visits->flatMap->fees->groupBy('fee_type')->map(fn ($fees) => (float) $fees->sum('amount'));
        return view('warehouse-portal.visits.report', compact('salesman', 'fromDate', 'toDate', 'visits', 'workingDays', 'areaNames', 'visitTypeCounts', 'feeSummary'));
    }

    public function areaDailyVisitsReport(Request $request)
    {
        $warehouse = $this->currentWarehouse();
        $this->ensureWarehouseAccount();
        $fromDate = $request->input('visit_date_from', now()->startOfMonth()->toDateString());
        $toDate = $request->input('visit_date_to', now()->endOfMonth()->toDateString());
        $salesmen = WarehouseSalesman::where('warehouse_id', $warehouse->id)->whereNotNull('user_id')->orderBy('name')->get(['id', 'name', 'user_id']);
        $salesmanId = $request->input('salesman_id', 'all');
        $salesmanIds = $salesmen->when($salesmanId !== 'all', fn ($items) => $items->where('id', (int) $salesmanId))->pluck('user_id');
        $visits = VisitInfo::with(['appCustomer', 'agent', 'fees'])->whereIn('agent_id', $salesmanIds)->whereBetween('visit_date', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->latest('visit_date')->latest('created_at')->latest('id')->get();
        $workingDays = collect(\Carbon\CarbonPeriod::create($fromDate, $toDate))->filter(fn ($date) => $date->dayOfWeek !== \Carbon\Carbon::FRIDAY)->count();
        $visitTypeCounts = $visits->groupBy('visit_type')->map->count();
        $feeSummary = $visits->flatMap->fees->groupBy('fee_type')->map(fn ($fees) => (float) $fees->sum('amount'));
        return view('warehouse-portal.visits.area-report', compact('warehouse', 'salesmen', 'salesmanId', 'fromDate', 'toDate', 'visits', 'workingDays', 'visitTypeCounts', 'feeSummary'));
    }

    public function dailyVisitShow($visit)
    {
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $salesmanIds = WarehouseSalesman::where('warehouse_id', $warehouse->id)
            ->whereNotNull('user_id')
            ->when($portalUser instanceof WarehouseSalesman, fn ($query) => $query->whereKey($portalUser->id))
            ->pluck('user_id');

        $visit = VisitInfo::query()
            ->with(['appCustomer', 'agent', 'fees'])
            ->whereIn('agent_id', $salesmanIds)
            ->findOrFail($visit);

        return view('warehouse-portal.visits.show', ['warehouse' => $warehouse, 'visit' => $visit, 'isSalesman' => Auth::guard('warehouse_salesman')->check()]);
    }

    public function dailyVisitDestroy($visit)
    {
        $salesman = $this->renewableEnergySalesman();
        $visit = VisitInfo::query()
            ->where('agent_id', $salesman->user_id)
            ->findOrFail($visit);

        $visit->fees()->delete();
        $visit->delete();

        return redirect()->route('warehouse.daily-visits')->with('message', 'Visit deleted successfully.');
    }

    public function dailyVisitEdit($visit)
    {
        $salesman = $this->renewableEnergySalesman();
        $visit = VisitInfo::with(['fees', 'appCustomer'])->where('agent_id', $salesman->user_id)->findOrFail($visit);
        $customers = AppCustomer::where('agent_id', $salesman->user_id)->orderBy('name')->get(['id', 'name', 'mobile', 'beneficiary_number', 'group_number']);
        return view('warehouse-portal.visits.edit', compact('visit', 'customers'));
    }

    public function dailyVisitUpdate(Request $request, $visit)
    {
        $salesman = $this->renewableEnergySalesman();
        $visit = VisitInfo::where('agent_id', $salesman->user_id)->findOrFail($visit);
        $data = $request->validate(['visit_date' => ['required', 'date'], 'customer_number' => ['nullable', 'string', 'max:100'], 'beneficiary_number' => ['nullable', 'string', 'max:100'], 'visit_type' => ['required', 'string', 'max:50'], 'description' => ['nullable', 'string', 'max:1000'], 'items' => ['nullable', 'array'], 'items.*.product' => ['nullable', 'string', 'max:255'], 'items.*.fee' => ['nullable', 'numeric', 'min:0']]);
        $items = collect($data['items'] ?? [])->map(fn ($item) => ['fee_type' => trim((string) ($item['product'] ?? '')), 'amount' => (float) ($item['fee'] ?? 0)])->filter(fn ($item) => $item['fee_type'] !== '')->values();
        DB::transaction(function () use ($data, $items, $visit) { $visit->update(['visit_date' => date('Y-m-d H:i:s', strtotime($data['visit_date'])), 'customer_number' => $data['customer_number'] ?? null, 'beneficiary_number' => $data['beneficiary_number'] ?? null, 'visit_type' => $data['visit_type'], 'description' => $data['description'] ?? null]); $visit->fees()->delete(); foreach ($items as $item) VisitFee::create(['visit_info_id' => $visit->id, 'fee_type' => $item['fee_type'], 'amount' => $item['amount']]); });
        return redirect()->route('warehouse.daily-visits')->with('message', 'Visit information updated successfully.');
    }

    public function manualVisitCreate(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $customers = AppCustomer::query()->where('agent_id', $salesman->user_id)->orderBy('name')->get(['id', 'name', 'mobile', 'beneficiary_number', 'group_number']);
        $selectedCustomerId = (string) $request->query('cm_id', '');
        return view('warehouse-portal.visits.create', compact('customers', 'selectedCustomerId'));
    }

    public function manualVisitStore(Request $request)
    {
        $salesman = $this->renewableEnergySalesman();
        $data = $request->validate([
            'app_customer_id' => ['required', 'integer', 'exists:app_customers,id'],
            'visit_date' => ['required', 'date'],
            'beneficiary_number' => ['nullable', 'string', 'max:100'],
            'customer_number' => ['nullable', 'string', 'max:100'],
            'visit_type' => ['required', 'string', 'max:50'],
            'memo_no' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
            'description' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.product' => ['nullable', 'string', 'max:255'],
            'items.*.fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        $customer = AppCustomer::where('id', $data['app_customer_id'])->where('agent_id', $salesman->user_id)->firstOrFail();
        $items = collect($data['items'] ?? [])->map(fn ($item) => ['fee_type' => trim((string) ($item['product'] ?? '')), 'amount' => (float) ($item['fee'] ?? 0)])->filter(fn ($item) => $item['fee_type'] !== '')->values();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $uploadDirectory = public_path('uploads/daily-visit');
            if (! is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
            $attachmentName = uniqid('visit_', true) . '.' . $request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move($uploadDirectory, $attachmentName);
            $attachmentPath = 'uploads/daily-visit/' . $attachmentName;
        }

        DB::transaction(function () use ($data, $customer, $salesman, $items, $attachmentPath) {
            $visit = VisitInfo::create([
                'customer_number' => $data['customer_number'] ?? $customer->group_number,
                'app_customer_id' => $customer->id,
                'description' => $data['description'] ?? null,
                'beneficiary_number' => $data['beneficiary_number'] ?? $customer->beneficiary_number,
                'memo_no' => !empty($data['memo_no']) ? [$data['memo_no']] : null,
                'attachment_path' => $attachmentPath,
                'visit_date' => date('Y-m-d H:i:s', strtotime($data['visit_date'])),
                'agent_id' => $salesman->user_id,
                'visit_type' => $data['visit_type'],
            ]);
            foreach ($items as $item) VisitFee::create(['visit_info_id' => $visit->id, 'fee_type' => $item['fee_type'], 'amount' => $item['amount']]);
        });

        return redirect()->route('warehouse.daily-visits')->with('message', 'Visit information added successfully.');
    }

    public function create(WarehouseInventoryService $inventory)
    {
        // Resolve/link the authenticated LSP application user before loading
        // customers. Newly-created LSP accounts may not have user_id filled
        // until their first portal request.
        $salesman = $this->renewableEnergySalesman();
        $warehouse = $this->currentWarehouse();

        $portalUser = $salesman;
        $products = Product::query()->select('products.*')->active()
            ->when($portalUser instanceof WarehouseSalesman, fn ($query) => $query->salesmanStock($portalUser->id))
            ->orderBy('product_name')->get()->map(function ($product) use ($portalUser, $warehouse, $inventory) {
                if ($portalUser instanceof WarehouseSalesman) {
                    $product->warehouse_purchase_qty = $product->salesman_assigned_qty;
                    $product->warehouse_transfer_qty = 0;
                    $product->warehouse_sale_qty = (float) ($product->salesman_sale_qty ?? 0) + (float) ($product->salesman_return_qty ?? 0);
                } else {
                    // A warehouse-direct sale cannot consume quantities reserved for salesmen.
                    $product->warehouse_purchase_qty = $inventory->warehouseReceived($warehouse->id, $product->id);
                    $product->warehouse_transfer_qty = 0;
                    $product->warehouse_sale_qty = $inventory->warehouseAssigned($warehouse->id, $product->id)
                        + $inventory->warehouseDirectSold($warehouse->id, $product->id);
                }
                return $product;
            });

        $customers = AppCustomer::query()
            ->where('agent_id', $salesman->user_id)
            ->latest('id')
            ->get(['id', 'name', 'mobile', 'email', 'beneficiary_number', 'group_number', 'village', 'unions']);

        return view('warehouse-portal.sales.create', compact('warehouse', 'products', 'customers'));
    }

    public function editSale(WarehouseSale $warehouse_sale, WarehouseInventoryService $inventory)
    {
        $warehouse = $this->currentWarehouse();
        $this->ensureSaleAccess($warehouse_sale, $warehouse);
        $sale = $warehouse_sale->load(['items.product']);
        return view('warehouse-portal.sales.edit', compact('warehouse', 'sale'));
    }

    public function storeSalesBeneficiary(Request $request)
    {
        $this->ensureSalesmanAccount();
        $salesman = Auth::guard('warehouse_salesman')->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'beneficiary_number' => ['required', 'string', 'max:100'],
            'village' => ['nullable', 'string', 'max:255'],
        ]);
        $customer = AppCustomer::create($data + ['agent_id' => $salesman->user_id, 'group_number' => 1, 'password' => Hash::make(Str::random(20)), 'date' => now()->toDateString(), 'created_by' => $salesman->user_id]);
        return response()->json(['message' => 'Customer added successfully.', 'customer' => $customer]);
    }

    public function beneficiaries(Request $request)
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);
        $salesmanUserIds = $salesmen->pluck('user_id')->filter()->values();
        $search = trim((string) $request->query('search', ''));
        $beneficiaryNumber = trim((string) $request->query('beneficiary_number', ''));
        $groupNumber = trim((string) $request->query('group_number', ''));
        $salesmanId = (int) $request->query('salesman_id', 0);
        $perPage = (string) $request->query('per_page', '10');
        if (!in_array($perPage, ['10', '25', '50', '100', 'all'], true)) $perPage = '10';
        $customerQuery = AppCustomer::query()->whereIn('agent_id', $salesmanUserIds)
            ->when($salesmanId > 0, fn ($query) => $query->where('agent_id', $salesmanId))
            ->when($beneficiaryNumber !== '', fn ($query) => $query->where('beneficiary_number', 'like', "%{$beneficiaryNumber}%"))
            ->when($groupNumber !== '', fn ($query) => $query->where('group_number', $groupNumber))
            ->when($search !== '', fn ($query) => $query->where(fn ($subQuery) => $subQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
                ->orWhere('beneficiary_number', 'like', "%{$search}%")
                ->orWhere('village', 'like', "%{$search}%")
                ->orWhere('unions', 'like', "%{$search}%")))
            ->latest();
        $totalCustomers = (clone $customerQuery)->count();
        $livestockTotals = (clone $customerQuery)->selectRaw('COALESCE(SUM(cow),0) as cow, COALESCE(SUM(bull),0) as bull, COALESCE(SUM(bakna),0) as bakna, COALESCE(SUM(goat),0) as goat, COALESCE(SUM(khasi),0) as khasi')->first();
        $customers = $perPage === 'all' ? $customerQuery->get() : $customerQuery->paginate((int) $perPage)->withQueryString();
        $editing = $request->filled('edit') ? AppCustomer::query()->whereIn('agent_id', $salesmanUserIds)->findOrFail((int) $request->query('edit')) : null;
        $beneficiaryNumbers = range(1, 40);
        $groupNumbers = range(1, 28);

        return view('warehouse-portal.beneficiaries.index', $this->beneficiaryViewData($warehouse) + compact('customers', 'search', 'beneficiaryNumber', 'groupNumber', 'salesmanId', 'salesmen', 'perPage', 'totalCustomers', 'livestockTotals', 'editing', 'beneficiaryNumbers', 'groupNumbers'));
    }

    public function createBeneficiary()
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);
        $editing = null;
        $beneficiaryNumbers = range(1, 40);
        $groupNumbers = range(1, 28);
        $createOnly = true;

        return view('warehouse-portal.beneficiaries.create', $this->beneficiaryViewData($warehouse) + compact('salesmen', 'editing', 'beneficiaryNumbers', 'groupNumbers', 'createOnly'));
    }

    public function editBeneficiary(AppCustomer $appCustomer)
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);
        abort_unless($salesmen->pluck('user_id')->contains((int) $appCustomer->agent_id), 403);
        $editing = $appCustomer;
        $beneficiaryNumbers = range(1, 40);
        $groupNumbers = range(1, 28);
        $createOnly = false;

        return view('warehouse-portal.beneficiaries.edit', $this->beneficiaryViewData($warehouse) + compact('salesmen', 'editing', 'beneficiaryNumbers', 'groupNumbers', 'createOnly'));
    }

    public function storeBeneficiary(Request $request)
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);
        $salesman = Auth::guard('warehouse_salesman')->user();
        if (!$salesman) {
            $request->validate(['salesman_id' => ['required', 'integer']]);
            $salesman = $salesmen->firstWhere('user_id', (int) $request->input('salesman_id'));
        }
        abort_unless($salesman, 422, 'Select a valid LSP for this Area Office.');

        $data = $this->beneficiaryData($request, true);
        $groupIsFull = AppCustomer::query()
            ->where('agent_id', $salesman->user_id)
            ->where('group_number', $data['group_number'])
            ->count() >= 40;

        if ($groupIsFull) {
            $message = 'This beneficiary group already has 40 members.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->withInput()->withErrors(['group_number' => $message]);
        }

        $data['password'] = Hash::make($data['password']);
        $data['date'] = now()->toDateString();
        AppCustomer::create($data + ['agent_id' => $salesman->user_id, 'created_by' => $salesman->user_id]);
        return redirect()->route('warehouse.beneficiaries.index')->with('message', 'Beneficiary added successfully.');
    }

    public function updateBeneficiary(Request $request, AppCustomer $appCustomer)
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesman = $this->beneficiarySalesmen($warehouse)->firstWhere('user_id', (int) $appCustomer->agent_id);
        abort_unless($salesman, 403);
        $data = $this->beneficiaryData($request, false);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $appCustomer->update($data + ['updated_by' => $salesman->user_id]);
        return redirect()->route('warehouse.beneficiaries.index')->with('message', 'Beneficiary updated successfully.');
    }

    public function destroyBeneficiary(AppCustomer $appCustomer)
    {
        $warehouse = $this->beneficiaryWarehouse();
        abort_unless($this->beneficiarySalesmen($warehouse)->pluck('user_id')->contains((int) $appCustomer->agent_id), 403);

        try {
            $appCustomer->delete();
        } catch (QueryException $exception) {
            // Keep historical visits and sales safe. The database intentionally
            // prevents deleting a beneficiary that is already used in a report.
            if ((int) ($exception->errorInfo[1] ?? 0) === 1451) {
                return redirect()->route('warehouse.beneficiaries.index')
                    ->with('error', 'This beneficiary cannot be deleted because sales or visit history is linked to it. You can edit the customer information instead.');
            }
            throw $exception;
        }

        return redirect()->route('warehouse.beneficiaries.index')->with('message', 'Beneficiary deleted successfully.');
    }

    public function importBeneficiariesForm()
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);

        return view('warehouse-portal.beneficiaries.import', $this->beneficiaryViewData($warehouse) + [
            'salesmen' => $salesmen,
            'report' => session('import_report'),
        ]);
    }

    public function importBeneficiaries(Request $request)
    {
        $warehouse = $this->beneficiaryWarehouse();
        $salesmen = $this->beneficiarySalesmen($warehouse);
        $salesman = Auth::guard('warehouse_salesman')->user();

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,xls,csv,txt'],
            'salesman_id' => [$salesman ? 'nullable' : 'required', 'integer'],
        ]);

        if (!$salesman) {
            $salesman = $salesmen->firstWhere('user_id', (int) $request->input('salesman_id'));
        }
        if (!$salesman || !$salesman->user_id) {
            return back()->withErrors(['salesman_id' => 'Select a valid LSP for this Area Office.']);
        }

        // Beneficiaries of LSPs in other Area Offices must never be moved here.
        $otherOfficeAgentIds = WarehouseSalesman::query()
            ->where('warehouse_id', '!=', $warehouse->id)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->all();

        set_time_limit(300);
        $import = new BeneficiaryImport((int) $salesman->user_id, $otherOfficeAgentIds);

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['file' => 'The file could not be imported. Please use the exported beneficiary Excel file or the template.']);
        }

        return redirect()->route('warehouse.beneficiaries.import')
            ->with('import_report', $import->report + ['lsp' => $salesman->name]);
    }

    public function beneficiaryImportTemplate()
    {
        $this->beneficiaryWarehouse();

        return Excel::download(
            new AppCustomerExport(AppCustomer::query()->whereRaw('1 = 0')),
            'beneficiary-import-template.xlsx'
        );
    }

    private function beneficiaryWarehouse()
    {
        return $this->currentWarehouse();
    }

    private function beneficiarySalesmen($warehouse)
    {
        $currentSalesman = Auth::guard('warehouse_salesman')->user();
        if ($currentSalesman) {
            return collect([$currentSalesman]);
        }
        return WarehouseSalesman::query()->where('warehouse_id', $warehouse->id)->whereNotNull('user_id')->orderBy('name')->get(['id', 'user_id', 'name']);
    }

    private function beneficiaryViewData($warehouse): array
    {
        $salesman = Auth::guard('warehouse_salesman')->user();
        return ['title' => 'Beneficiaries', 'warehouseName' => $salesman?->name ?? $warehouse->name, 'warehouseEmail' => $salesman?->email ?? $warehouse->email, 'isSalesman' => (bool) $salesman, 'warehouse' => $warehouse];
    }

    private function beneficiaryData(Request $request, bool $creating): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'beneficiary_number' => ['required', 'integer', 'between:1,40'],
            'group_number' => ['required', 'integer', 'between:1,28'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => [$creating ? 'required' : 'nullable', 'string', 'min:6', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'union' => ['nullable', 'string', 'max:255'],
            'cow' => ['nullable', 'integer', 'min:0'],
            'bull' => ['nullable', 'integer', 'min:0'],
            'bakna' => ['nullable', 'integer', 'min:0'],
            'goat' => ['nullable', 'integer', 'min:0'],
            'khasi' => ['nullable', 'integer', 'min:0'],
            'membership' => ['nullable', 'string', 'max:255'],
            'deworming' => ['nullable', 'string', 'max:255'],
            'bringing' => ['nullable', 'string', 'max:255'],
            'fattening' => ['nullable', 'string', 'max:255'],
            'treatment' => ['nullable', 'string', 'max:255'],
            'ai' => ['nullable', 'string', 'max:255'],
            'medicine' => ['nullable', 'string', 'max:255'],
        ]);

        $data['unions'] = $data['union'] ?? null;
        unset($data['union']);

        foreach (['cow', 'bull', 'bakna', 'goat', 'khasi'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        return $data;
    }

    public function store(Request $request, WarehouseInventoryService $inventory, WarehouseSaleAdminSyncService $adminSync)
    {
        $this->ensureSalesmanAccount();
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();

        $data = $request->validate([
            'sale_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'customer_village' => ['nullable', 'string', 'max:255'],
            'customer_union' => ['nullable', 'string', 'max:255'],
            'beneficiary_id' => ['nullable', 'integer', 'exists:app_customers,id'],
            'beneficiary_number' => ['nullable', 'integer', 'between:1,40'],
            'group_number' => ['nullable', 'integer', 'between:1,28'],
            'sale_beneficiary_number' => ['nullable', 'integer', 'between:1,40'],
            'sale_group_number' => ['nullable', 'integer', 'between:1,28'],
            'payment_method' => ['required', 'in:Cash,Credit,Mobile Banking'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ]);

        // Payment modal aliases; these values are used only to create/find
        // the app customer and are not stored on the warehouse sale table.
        $data['beneficiary_number'] = $data['beneficiary_number'] ?? $data['sale_beneficiary_number'] ?? null;
        $data['group_number'] = $data['group_number'] ?? $data['sale_group_number'] ?? null;

        if (!empty($data['beneficiary_id'])) {
            $customer = AppCustomer::query()
                ->where('agent_id', $portalUser->user_id)
                ->findOrFail((int) $data['beneficiary_id']);
            $data['beneficiary_number'] = $customer->beneficiary_number;
            $data['group_number'] = $customer->group_number;
        } elseif ($portalUser instanceof WarehouseSalesman && $portalUser->user_id) {
            $customer = AppCustomer::create([
                'agent_id' => $portalUser->user_id,
                'name' => $data['customer_name'],
                'mobile' => $data['customer_phone'],
                'beneficiary_number' => $data['beneficiary_number'],
                'group_number' => $data['group_number'],
                'password' => Hash::make(Str::random(20)),
                'date' => now()->toDateString(),
                'village' => $data['customer_village'] ?? ($data['customer_address'] ?? null),
                'unions' => $data['customer_union'] ?? null,
                'created_by' => $portalUser->user_id,
            ]);
            $data['beneficiary_id'] = $customer->id;
        }

        try {
            DB::transaction(function () use ($warehouse, $portalUser, $data, $inventory, $adminSync) {
                $sale = WarehouseSale::create([
                    'warehouse_id' => $warehouse->id,
                    'warehouse_salesman_id' => $portalUser instanceof WarehouseSalesman ? $portalUser->id : null,
                    'sale_date' => $data['sale_date'],
                    'invoice_no' => WarehouseSale::nextInvoiceNo(),
                    'customer_name' => $data['customer_name'] ?? null,
                    'customer_phone' => $data['customer_phone'],
                    'customer_address' => $data['customer_address'] ?? null,
                    'total_amount' => 0,
                    'payment_method' => $data['payment_method'],
                    'paid_amount' => 0,
                    'due_amount' => 0,
                    'created_by' => $portalUser?->id,
                ]);

                $subtotal = 0;

                foreach ($data['items'] as $item) {
                    $inventory->lockWarehouseProduct($warehouse->id, (int) $item['product_id']);
                    $available = $portalUser instanceof WarehouseSalesman
                        ? $inventory->salesmanAvailable($portalUser->id, (int) $item['product_id'])
                        : $inventory->warehouseUnassignedAvailable($warehouse->id, (int) $item['product_id']);

                    if ($available < $item['quantity']) {
                        throw new \RuntimeException('Not enough stock for one of the selected products.');
                    }

                    $lineTotal = round($item['quantity'] * $item['sale_price'], 2);
                    $subtotal += $lineTotal;

                    WarehouseSaleItem::create([
                        'warehouse_sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'sale_price' => $item['sale_price'],
                        'total' => $lineTotal,
                    ]);
                }

                $discount = min((float) ($data['discount'] ?? 0), $subtotal);
                $total = round($subtotal - $discount, 2);

                $paidAmount = $data['payment_method'] === 'Credit'
                    ? round((float) ($data['paid_amount'] ?? 0), 2)
                    : round($total, 2);

                if ($paidAmount > round($total, 2)) {
                    throw new \RuntimeException('Paid amount cannot be greater than the sale total.');
                }

                $sale->update([
                    'total_amount' => $total,
                    'paid_amount' => $paidAmount,
                    'due_amount' => round($total - $paidAmount, 2),
                ]);

                if ($paidAmount > 0) {
                    WarehouseSalePayment::create([
                        'warehouse_sale_id' => $sale->id,
                        'payment_date' => $data['sale_date'],
                        'amount' => $paidAmount,
                        'payment_method' => $data['payment_method'] === 'Credit' ? 'Cash' : $data['payment_method'],
                        'notes' => 'Initial payment',
                        'created_by' => $portalUser?->id,
                    ]);
                }

                if ($portalUser instanceof WarehouseSalesman) {
                    $adminSync->sync($sale->fresh(), $portalUser);
                }
            });
        } catch (\Throwable $throwable) {
            return back()->withInput()->withErrors(['items' => $throwable->getMessage()]);
        }

        return redirect()->route('warehouse.sales.index')->with('message', 'Sale saved successfully.');
    }

    public function show(WarehouseSale $warehouse_sale)
    {
        $warehouse = $this->currentWarehouse();

        $this->ensureSaleAccess($warehouse_sale, $warehouse);

        $warehouse_sale->load(['items.product', 'salesman', 'payments']);

        return view('warehouse-portal.sales.show', [
            'warehouse' => $warehouse,
            'sale' => $warehouse_sale,
        ]);
    }

    public function updateSale(Request $request, WarehouseSale $warehouse_sale, WarehouseInventoryService $inventory, WarehouseSaleAdminSyncService $adminSync)
    {
        $this->ensureSalesmanAccount();
        $warehouse = $this->currentWarehouse();
        $portalUser = $this->currentPortalUser();
        $this->ensureSaleAccess($warehouse_sale, $warehouse);
        if (empty($request->input('items'))) {
            $request->merge([
                'items' => $warehouse_sale->load('items')->items->map(fn ($item) => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'sale_price' => $item->sale_price,
                ])->values()->all(),
            ]);
        }
        $data = $request->validate([
            'sale_date' => ['required', 'date'], 'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'], 'customer_address' => ['nullable', 'string', 'max:500'],
            'customer_village' => ['nullable', 'string', 'max:255'], 'customer_union' => ['nullable', 'string', 'max:255'],
            'beneficiary_id' => ['nullable', 'integer', 'exists:app_customers,id'],
            'payment_method' => ['required', 'in:Cash,Credit,Mobile Banking'], 'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'], 'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'], 'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.sale_price' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($warehouse_sale, $warehouse, $portalUser, $data, $inventory, $adminSync) {
                $sale = WarehouseSale::query()->lockForUpdate()->findOrFail($warehouse_sale->id);
                $adminSync->deleteSaleSync($sale);
                $sale->payments()->delete();
                $sale->items()->delete();
                $subtotal = 0;
                foreach ($data['items'] as $item) {
                    $inventory->lockWarehouseProduct($warehouse->id, (int) $item['product_id']);
                    $available = $portalUser instanceof WarehouseSalesman ? $inventory->salesmanAvailable($portalUser->id, (int) $item['product_id']) : $inventory->warehouseUnassignedAvailable($warehouse->id, (int) $item['product_id']);
                    if ($available < $item['quantity']) throw new \RuntimeException('Not enough stock for one of the selected products.');
                    $lineTotal = round($item['quantity'] * $item['sale_price'], 2);
                    $subtotal += $lineTotal;
                    WarehouseSaleItem::create(['warehouse_sale_id' => $sale->id, 'product_id' => $item['product_id'], 'quantity' => $item['quantity'], 'sale_price' => $item['sale_price'], 'total' => $lineTotal]);
                }
                $total = round($subtotal - min((float) ($data['discount'] ?? 0), $subtotal), 2);
                $paid = $data['payment_method'] === 'Credit' ? round((float) ($data['paid_amount'] ?? 0), 2) : $total;
                if ($paid > $total) throw new \RuntimeException('Paid amount cannot be greater than the sale total.');
                $sale->update(['sale_date' => $data['sale_date'], 'customer_name' => $data['customer_name'], 'customer_phone' => $data['customer_phone'], 'customer_address' => $data['customer_address'] ?? null, 'payment_method' => $data['payment_method'], 'total_amount' => $total, 'paid_amount' => $paid, 'due_amount' => round($total - $paid, 2)]);
                if ($paid > 0) WarehouseSalePayment::create(['warehouse_sale_id' => $sale->id, 'payment_date' => $data['sale_date'], 'amount' => $paid, 'payment_method' => $data['payment_method'] === 'Credit' ? 'Cash' : $data['payment_method'], 'notes' => 'Initial payment', 'created_by' => $portalUser?->id]);
                if ($portalUser instanceof WarehouseSalesman) $adminSync->sync($sale->fresh(), $portalUser);
            });
        } catch (\Throwable $throwable) {
            return back()->withInput()->withErrors(['items' => $throwable->getMessage()]);
        }
        return redirect()->route('warehouse.sales.index')->with('message', 'Sale updated successfully.');
    }

    public function payDue(Request $request, WarehouseSale $warehouse_sale, WarehouseSaleAdminSyncService $adminSync)
    {
        $warehouse = $this->currentWarehouse();
        $this->ensureSaleAccess($warehouse_sale, $warehouse);

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'in:Cash,Mobile Banking'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($warehouse_sale, $data, $adminSync) {
                $sale = WarehouseSale::query()->lockForUpdate()->findOrFail($warehouse_sale->id);
                $due = round((float) $sale->total_amount - (float) $sale->paid_amount, 2);
                $amount = round((float) $data['amount'], 2);

                if ($amount > $due) {
                    throw new \RuntimeException('Payment cannot be greater than the current due amount.');
                }

                $payment = WarehouseSalePayment::create([
                    'warehouse_sale_id' => $sale->id,
                    'payment_date' => $data['payment_date'],
                    'amount' => $amount,
                    'payment_method' => $data['payment_method'],
                    'notes' => $data['notes'] ?? 'Due payment for invoice ' . $sale->invoice_no,
                    'created_by' => $this->currentPortalUser()?->id,
                ]);

                $newPaid = round((float) $sale->paid_amount + $amount, 2);
                $sale->update(['paid_amount' => $newPaid, 'due_amount' => round((float) $sale->total_amount - $newPaid, 2)]);

                if ($sale->salesman) {
                    $adminSync->syncPayment($sale->fresh(['items.product.category', 'salesman']), $payment);
                }

                // Keep each due collection visible in the LSP sales list as
                // its own payment sale. It intentionally has no line items,
                // so it never changes product stock or sold quantities.
                $paymentSale = WarehouseSale::create([
                    'warehouse_id' => $sale->warehouse_id,
                    'warehouse_salesman_id' => $sale->warehouse_salesman_id,
                    'sale_date' => $data['payment_date'],
                    'invoice_no' => WarehouseSale::nextInvoiceNo(),
                    'customer_name' => $sale->customer_name,
                    'customer_phone' => $sale->customer_phone,
                    'customer_address' => $sale->customer_address,
                    'total_amount' => $amount,
                    'payment_method' => $data['payment_method'],
                    'paid_amount' => $amount,
                    'due_amount' => 0,
                    'created_by' => $this->currentPortalUser()?->id,
                ]);

                WarehouseSalePayment::create([
                    'warehouse_sale_id' => $paymentSale->id,
                    'payment_date' => $data['payment_date'],
                    'amount' => $amount,
                    'payment_method' => $data['payment_method'],
                    'notes' => 'Due payment for invoice ' . $sale->invoice_no,
                    'created_by' => $this->currentPortalUser()?->id,
                ]);
            });
        } catch (\Throwable $throwable) {
            return back()->withErrors(['amount' => $throwable->getMessage()]);
        }

        return back()->with('message', 'Due payment recorded successfully.');
    }

    public function invoice(WarehouseSale $warehouse_sale)
    {
        $warehouse = $this->currentWarehouse();

        $this->ensureSaleAccess($warehouse_sale, $warehouse);

        $warehouse_sale->load(['items.product', 'salesman']);

        return view('warehouse-portal.sales.invoice', [
            'warehouse' => $warehouse,
            'sale' => $warehouse_sale,
            'business' => currentBranch(),
        ]);
    }

    public function adminInvoice(WarehouseSale $warehouse_sale)
    {
        abort_unless(Auth::check() && Auth::user()?->userPermission?->role?->role_name === 'Admin', 403);

        $warehouse_sale->load(['warehouse', 'items.product', 'salesman']);

        return view('report.lsp-invoice', [
            'warehouse' => $warehouse_sale->warehouse,
            'sale' => $warehouse_sale,
            'business' => currentBranch(),
        ]);
    }
}
