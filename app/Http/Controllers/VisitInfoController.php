<?php

namespace App\Http\Controllers;

use App\Exports\VisitInfoExport;
use App\Models\AgentSale;
use App\Models\Area;
use App\Models\User;
use App\Models\VisitFee;
use App\Models\VisitInfo;
use App\Models\AppCustomer;
use App\Models\WarehouseSalesman;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VisitInfoController extends Controller
{
    /**
     * Display a listing of visits with filters.
     */
    public function index(Request $request)
    {
        $query = VisitInfo::with(['appCustomer', 'agent', 'fees']);
    
        if (isRole(ROLE_AGENT)) {
            $agentId = Auth::id();
            $query->where('agent_id', $agentId);
        }
    
        // Filter by date
        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }
    
        // Filter by agent
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
    
        // Filter by name or beneficiary number
        if ($request->filled('namenumber')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('appCustomer', function ($subQ) use ($request) {
                    $subQ->where('name', 'like', '%' . $request->namenumber . '%');
                })->orWhereHas('appCustomer', function ($subQ) use ($request) {
                    $subQ->where('beneficiary_number', 'like', '%' . $request->namenumber . '%');
                });
            });
        }
    
        $visits = $query->orderByDesc('visit_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(100);
    
        $agents = User::whereHas('userPermission', function ($q) {
                $q->where('role_id', ROLE_AGENT);
            })
            ->where('status', 1)
            ->get();
    
        return view('visit-info.index', compact('visits', 'agents'));
    }

    /**
     * Show form for creating a new visit.
     */
    public function create(Request $request)
    {
        $cmId = $request->get('cm_id');

        $customers = AppCustomer::orderBy('name')->get();
        $agents = User::whereHas('userPermission', function ($q) {
            $q->where('role_id', ROLE_AGENT);
        })
            ->where('status', 1)
            ->get();


        return view('visit-info.create', compact('customers', 'cmId'));
    }

    /**
     * Store a newly created visit.
     */
    public function store(Request $request)
    {
        try {
            // 1) Validate (align with your form names + schema)
            $validated = $request->validate([
                'app_customer_id' => ['required', 'exists:app_customers,id'],
                'customer_number' => ['nullable', 'string', 'max:255'],
                'description' => 'nullable',
                'visit_type' => 'required',
                'beneficiary_number' => 'required',
                'memo_no' => ['nullable', 'array'],
                'memo_no.*' => ['nullable', 'string', 'max:255'],
                'attachment' => ['nullable', 'file', 'max:10000', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
                'items' => ['nullable', 'array'],
                'items.*.product' => ['nullable', 'string', 'max:255'],
                'items.*.fee' => ['nullable', 'integer', 'min:0'],
            ]);

            // Normalize memo numbers
            $memoNos = collect($validated['memo_no'] ?? [])
                ->filter(fn($v) => filled($v))
                ->values()
                ->all();

            // Normalize items
            $items = collect($validated['items'] ?? [])
                ->map(fn($row) => [
                    'fee_type' => (string) ($row['product'] ?? ''),
                    'amount' => (int) ($row['fee'] ?? 0),
                ])
                ->filter(fn($r) => $r['fee_type'] !== '')
                ->values()
                ->all();

            // 2) Persist (transaction)
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

            $visit = DB::transaction(function () use ($validated, $memoNos, $items, $attachmentPath) {
                // Create visit info
                $visit = VisitInfo::create([
                    'customer_number' => $validated['customer_number'] ?? null,
                    'app_customer_id' => $validated['app_customer_id'],
                    'description' => $validated['description'],
                    'beneficiary_number' => $validated['beneficiary_number'],
                    'memo_no' => !empty($memoNos) ? $memoNos : null,
                    'attachment_path' => $attachmentPath,
                    'visit_date' => Carbon::now()->format('Y-m-d H:i:s'),
                    'agent_id' => Auth::id(),
                    'visit_type' => $validated['visit_type']
                ]);

                // Create visit fees
                if (!empty($items)) {
                    foreach ($items as $row) {
                        VisitFee::create([
                            'visit_info_id' => $visit->id,
                            'fee_type' => $row['fee_type'],
                            'amount' => $row['amount'],
                        ]);
                    }
                }
                
                return $visit;
            });

            return redirect()
                ->route('visit-info.index')
                ->with('success', 'Visit information created successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create visit information: ' . $e->getMessage());
        }
    }
    /**
     * Show form for editing a visit.
     */
    public function edit(VisitInfo $visitInfo)
    {
        $visitInfo->load('fees');
        $customers = AppCustomer::orderBy('name')->get();

        // Provide an items array for the form (id + product + fee)
        $visitInfo->items = $visitInfo->fees->map(fn($f) => [
            'id' => $f->id,
            'product' => $f->fee_type,
            'fee' => $f->amount,
        ])->values()->all();

        return view('visit-info.edit', compact('visitInfo', 'customers'));
    }

    /**
     * Update the specified visit.
     */
    public function update(Request $request, VisitInfo $visitInfo)
    {
        // same rules as store + allow nullable items.*.id
        $validated = $request->validate([
            'app_customer_id' => ['required', 'exists:app_customers,id'],
            'customer_number' => ['nullable', 'string', 'max:255'],
            'description' => 'nullable',
            'visit_type' => 'required',
            'memo_no' => ['nullable', 'array'],
            'memo_no.*' => ['nullable', 'string', 'max:255'],
            'beneficiary_number' => 'required',
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:visit_fees,id'],
            'items.*.product' => ['nullable', 'string', 'max:255'],
            'items.*.fee' => ['nullable', 'integer', 'min:0'],
        ]);

        $memoNos = collect($validated['memo_no'] ?? [])
            ->filter(fn($v) => filled($v))
            ->values()
            ->all();

        $items = collect($validated['items'])
            ->map(fn($row) => [
                'id' => $row['id'] ?? null,
                'fee_type' => (string) ($row['product'] ?? ''),
                'amount' => (int) ($row['fee'] ?? 0),
            ])
            ->filter(fn($r) => $r['fee_type'] !== '')
            ->values();

        DB::transaction(function () use ($visitInfo, $validated, $memoNos, $items) {
            // 1) Update parent
            $visitInfo->update([
                'customer_number' => $validated['customer_number'] ?? null,
                'app_customer_id' => $validated['app_customer_id'],
                'description' => $validated['description'],
                'visit_type' => $validated['visit_type'],
                'beneficiary_number' => $validated['beneficiary_number'],
                'memo_no' => $memoNos,           // json
                'agent_id' => Auth::id(),        
            ]);

            // 2) Sync children (update existing, create new, delete removed)
            $existingIds = $visitInfo->fees()->pluck('id')->all();
            $keptIds = [];

            foreach ($items as $row) {
                if ($row['id'] && in_array($row['id'], $existingIds, true)) {
                    // update existing
                    VisitFee::where('id', $row['id'])->update([
                        'fee_type' => $row['fee_type'],
                        'amount' => $row['amount'],
                    ]);
                    $keptIds[] = $row['id'];
                } else {
                    // create new
                    $fee = $visitInfo->fees()->create([
                        'fee_type' => $row['fee_type'],
                        'amount' => $row['amount'],
                    ]);
                    $keptIds[] = $fee->id;
                }
            }

            $toDelete = array_diff($existingIds, $keptIds);
            if (!empty($toDelete)) {
                VisitFee::whereIn('id', $toDelete)->delete();
            }
           
        });

        return redirect()
            ->route('visit-info.index')
            ->with('success', 'Visit information updated successfully.');
    }


    /**
     * Display the specified visit.
     */
    public function show(VisitInfo $visitInfo)
    {
        $visitInfo->load(['appCustomer', 'agent', 'fees']);

        return view('visit-info.show', compact('visitInfo'));
    }

    /**
     * Remove the specified visit.
     */
    public function destroy(VisitInfo $visitInfo)
    {
        $visitInfo->delete();

        return redirect()
            ->route('visit-info.index')
            ->with('success', 'Visit information deleted successfully.');
    }


    public function report(Request $request)
    {
        $agentRecords = WarehouseSalesman::query()
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->where('status', 1))
            ->with('warehouse')
            ->orderBy('name')
            ->get(['id', 'user_id', 'name', 'warehouse_id']);
        $agents = $agentRecords->mapWithKeys(fn ($lsp) => [$lsp->user_id => trim($lsp->name . ' (' . ($lsp->warehouse?->name ?: 'N/A') . ')')]);

        $selectedAgentId = $request->input('agent_id', 'all');
        $selectedAgentAreaOffice = $selectedAgentId !== 'all'
            ? ($agentRecords->firstWhere('user_id', $selectedAgentId)?->warehouse?->name ?: 'N/A')
            : 'All Area Offices';
        $selectedAgentName = $selectedAgentId !== 'all'
            ? ($agentRecords->firstWhere('user_id', $selectedAgentId)?->name ?: 'N/A')
            : 'All LSPs';
        $hasFilters = $request->filled('visit_date_from') &&
                    $request->filled('visit_date_to');

        $visits = collect();
        $feeSummary = collect();
        $days = 0;

        if ($hasFilters) {
            $from = Carbon::parse($request->visit_date_from);
            $to = Carbon::parse($request->visit_date_to);

            // Get visits
            $visits = VisitInfo::with(['appCustomer', 'fees', 'agent'])
                ->whereBetween('visit_date', [
                    $from->copy()->startOfDay(),
                    $to->copy()->endOfDay()
                ])
                ->when($selectedAgentId !== 'all', function ($query) use ($selectedAgentId) {
                    $query->where('agent_id', $selectedAgentId);
                })
                ->orderByRaw('DATE(visit_date) DESC')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();

            // Fee summary
            if ($visits->isNotEmpty()) {
                $feeSummary = VisitFee::whereIn('visit_info_id', $visits->pluck('id'))
                    ->selectRaw('fee_type, SUM(amount) as total_amount')
                    ->groupBy('fee_type')
                    ->pluck('total_amount', 'fee_type');
            }

            // Count days (skip Friday)
            foreach (CarbonPeriod::create($from, $to) as $date) {
                if ($date->dayOfWeek !== Carbon::FRIDAY) {
                    $days++;
                }
            }
        }

        if ($selectedAgentId === 'all' && $visits->isNotEmpty()) {
            $reportAreaOffices = $visits
                ->map(fn ($visit) => $agentRecords->firstWhere('user_id', $visit->agent_id)?->warehouse?->name)
                ->filter()
                ->unique()
                ->values();

            if ($reportAreaOffices->count() === 1) {
                $selectedAgentAreaOffice = $reportAreaOffices->first();
            }
        }

        // Sub target setup
        $subTargets = [
            'Deworming' => 400,
            'Fattening' => 900,
            'Medicine' => 1200,
            'Treatment and others' => 300,
        ];


        $dailyTarget = array_sum($subTargets);
        $totalTargetAmount = $days * $dailyTarget;
        $totalAchieveAmount = $feeSummary->sum();
        $avgAchievement = $totalTargetAmount > 0
            ? ($totalAchieveAmount / $totalTargetAmount) * 100
            : 0;

        $groupNumbers = (clone $visits)->groupBy('customer_number')->keys()->values();
        $areaNames = WarehouseSalesman::with('warehouse')
            ->whereIn('user_id', $visits->pluck('agent_id')->unique())
            ->get()
            ->map(fn ($salesman) => $salesman->warehouse?->name)
            ->filter()
            ->unique()
            ->values();
        $visitTypeCounts = [
            'C.F'   => (clone $visits)->where('visit_type', 'C.F')->count(),
            'Re.V'  => (clone $visits)->where('visit_type', 'Re.V')->count(),
            'Reg.v' => (clone $visits)->where('visit_type', 'Reg.v')->count(),
            'N.V'   => (clone $visits)->where('visit_type', 'N.V')->count(),
            'A.s'   => (clone $visits)->where('visit_type', 'A.s')->count(),
        ];
       $totalCustomers = (clone $visits)->groupBy('app_customer_id')->count();
        return view('visit-info.report', compact(
            'agents',
            'visits',
            'feeSummary',
            'hasFilters',
            'groupNumbers',
            'areaNames',
            'days',
            'subTargets',
            'dailyTarget',
            'totalTargetAmount',
            'totalAchieveAmount',
            'avgAchievement',
            'visitTypeCounts',
            'totalCustomers',
            'selectedAgentId',
            'selectedAgentAreaOffice',
            'selectedAgentName'
        ));
    }

     public function byCustomer($customerId, Request $request)
    {
        // Pull invoices for the customer, remove null/empty, dedupe, sort
        $invoices = AgentSale::query()
            ->where('app_customer_id', $customerId)
            ->whereDate('sale_date', now()->format('Y-m-d')) 
            ->whereNotNull('invoice_no')
            ->pluck('invoice_no')
            ->map(fn($v) => trim((string)$v))
            ->filter()               // drop empties
            ->unique()
            ->values();

        $group_bfNumber = AppCustomer::select('group_number','beneficiary_number')->findOrFail($customerId);    

        return response()->json([
            'data' => $invoices,
            'group_number' => $group_bfNumber->group_number,
            'beneficiary_number' => $group_bfNumber->beneficiary_number,
        ]);
    }

      public function exportExcel(Request $request)
    {
        $file = 'visit-info-'.now()->format('Ymd-His').'.xlsx';
        return Excel::download(new VisitInfoExport($request->all()), $file);
    }
}
