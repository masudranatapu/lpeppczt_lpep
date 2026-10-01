<?php

namespace App\Http\Controllers\App;

use App\Exports\AppCustomerExport;
use App\Models\User;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppCustomer;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;

class AppCustomerController extends Controller
{
    private const PER_PAGE_OPTIONS = ['10', '25', '50', '100', 'all'];

    private function filteredQuery(Request $request)
    {
        return AppCustomer::query()
            ->when(isRole(ROLE_AGENT), fn ($q) => $q->where('agent_id', auth()->id()))
            ->when($request->filled('agent_id'), fn ($q) => $q->where('agent_id', $request->agent_id))
            ->when($request->filled('staf'), fn ($q) => $q->where('agent_id', $request->staf))
            ->when($request->filled('group_name'), fn ($q) => $q->where('beneficiary_number', $request->group_name))
            ->when($request->filled('group_number'), fn ($q) => $q->where('group_number', $request->group_number))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);
                $q->where(fn ($sub) => $sub
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('village', 'like', "%{$search}%")
                    ->orWhere('unions', 'like', "%{$search}%"));
            });
    }

    private function livestockTotals($query)
    {
        return (clone $query)->selectRaw("
                COALESCE(SUM(cow),0)   as cow,
                COALESCE(SUM(bull),0)  as bull,
                COALESCE(SUM(bakna),0) as bakna,
                COALESCE(SUM(goat),0)  as goat,
                COALESCE(SUM(khasi),0) as khasi
            ")->first();
    }

    public function index(Request $request)
    {
        $isAdmin = Auth::user()?->userPermission?->role?->role_name === 'Admin';
        $baseQuery = $this->filteredQuery($request);
        $totals = $this->livestockTotals($baseQuery);
        $query = (clone $baseQuery)
            ->with(['createdBy:id,name', 'updatedBy:id,name', 'importedFrom:id,agent_id', 'importedFrom.agent:id,name,employee_name'])
            ->orderByDesc('id');

        // Still served as DataTables JSON for the app-bioenergy page, which reuses this endpoint.
        if ($request->ajax()) {
            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($isAdmin) {
                    $btn = "<div class='btn btn-group'>" . $data->log();
                    if ($isAdmin) {
                        $btn .= "<button type='button' class='btn btn-sm btn-success' title='QR Code' id='viewQr' data-id='{$data->id}'><i class='fa fa-qrcode'></i></button>";
                    }
                    $btn .= "<button type='button' class='btn btn-sm btn-primary' title='Edit' id='editAppCustomer' data-id='{$data->id}'><i class='fa fa-edit'></i></button>";
                    $btn .= "<button type='button' class='btn btn-sm btn-danger' title='Delete' id='deleteData' data-id='{$data->id}'><i class='fa fa-trash'></i></button>";
                    $btn .= "</div>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->with('totals', $totals)
                ->make(true);
        }

        $perPage = (string) $request->query('per_page', '25');
        if (!in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = '25';
        }
        $customers = $perPage === 'all'
            ? $query->get()
            : $query->paginate((int) $perPage)->withQueryString();

        $agents = User::whereHas('userPermission', function ($q) {
                $q->where('role_id', ROLE_AGENT);
            })
            ->where('status', 1)
            ->get();
        $staffs = User::whereHas('userPermission', function ($q) {
                $q->where('role_id', 2);
            })
            ->where('status', 1)
            ->get();
        $perPageOptions = self::PER_PAGE_OPTIONS;

        return view('app-customer.index', compact('agents', 'staffs', 'customers', 'totals', 'perPage', 'perPageOptions', 'isAdmin'));
    }

    public function export(Request $request)
    {
        // Bulk selection arrives as one comma separated field, so selecting
        // more rows than PHP's max_input_vars (1000 on the server) still works.
        $ids = collect(explode(',', (string) $request->input('ids')))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        // With ids only those rows are exported; the agent restriction still applies.
        $query = $this->filteredQuery($request)
            ->when($request->filled('ids'), fn ($q) => $q->whereIn('id', $ids));

        return Excel::download(
            new AppCustomerExport($query),
            'beneficiaries-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function create(Request $request)
    {
        return view('app-customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'agent_id' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'beneficiary_number' =>'required',
            "group_number" => "required",
            'email' => 'nullable',
            'password' => 'required',
            'cow' => 'nullable',
            'bull' => 'nullable',
            'bakna' => 'nullable',
            'goat' => 'nullable',
            'khasi' => 'nullable',
            'membership' => 'nullable',
            'deworming' => 'nullable',
            'bringing' => 'nullable',
            'fattening' => 'nullable',
            'treatment' => 'nullable',
            'ai' => 'nullable',
            'medicine' => 'nullable',
            // 'feed' => 'nullable',
            'village' => 'nullable',
            'union' => 'nullable',

        ]);

        $check40 = AppCustomer::where('agent_id',$request->agent_id)
                    ->where('group_number', $request->group_number)->count();
        if($check40 >= 40){
                return response()->json('Your 40 membars group target full');
        }

        AppCustomer::query()->create([
            'agent_id' => $request->agent_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'beneficiary_number' => $request->beneficiary_number,
            'group_number' => $request->group_number,
            'password' => Hash::make($request->password),
            'cow' => $request->cow ?? 0,
            'bull' => $request->bull ?? 0,
            'bakna' => $request->bakna ?? 0,
            'goat' => $request->goat ?? 0,
            'khasi' => $request->khasi ?? 0,
            'membership' => $request->membership ?? null,
            'deworming' => $request->deworming ?? null,
            'bringing' => $request->bringing ?? null,
            'fattening' => $request->fattening ?? null,
            'treatment' => $request->treatment ?? null,
            'ai' => $request->ai ?? null,
            'medicine' => $request->medicine ?? null,
            // 'feed' => $request->feed ?? null,
            'date' => date('Y-m-d'),
            'created_by' => auth()->id(),
            'village' => $request->village ?? null,
            'unions' => $request->union ?? null,
        ]);

        return response()->json('Customer Adeed Success');
    }

    public function edit($id)
    {
        return view('app-customer.edit', [
            'customer' => AppCustomer::query()->find($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            'agent_id' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            "group_number" => "required",
            'email' => 'nullable',
            'password' => 'nullable',
            'beneficiary_number' =>'required',
            'cow' => 'nullable',
            'bull' => 'nullable',
            'bakna' => 'nullable',
            'goat' => 'nullable',
            'khasi' => 'nullable',
            'membership' => 'nullable',
            'deworming' => 'nullable',
            'bringing' => 'nullable',
            'fattening' => 'nullable',
            'treatment' => 'nullable',
            'ai' => 'nullable',
            'medicine' => 'nullable',
            // 'feed' => 'nullable',
            'village' => 'nullable',
            'unions' => 'nullable',
        ]);

        if ($request->password)
            $validation['password'] = Hash::make($request->password);
        $validation['updated_by'] =  auth()->id();
        AppCustomer::query()
            ->find($id)
            ->update($validation);
        return response()->json('Success');
    }

    public function destroy($id)
    {
        try {
            $customer = AppCustomer::with('farms')->find($id);

            if (!$customer) {
                return response()->json("Customer not found", 404);
            }

            // First delete related farms
            if ($customer->farms->count() > 0) {
                $customer->farms()->delete();
            }

            // Then delete the customer
            $customer->delete();

            return response()->json("Successfully deleted");
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json("Something went wrong", 500);
        }
    }

    // public function select2Ajax(Request $request)
    // {
    //     $customers = AppCustomer::query()
    //         ->select('id', 'name as text', 'mobile')
    //         ->where(function ($query) use ($request) {
    //             $query->where('name', 'like', '%' . $request->q . '%')
    //                 ->orWhere('mobile', 'like', '%' . $request->q . '%');
    //         })
    //         ->when(isRole(ROLE_AGENT), fn ($query) => $query->where('agent_id', auth()->id()))
    //         ->skip(($request->page - 1) * 5)
    //         ->orderByDesc("id")
    //         ->take(5)
    //         ->get()
    //         ->map(function ($item) {
    //             $item->text = $item->text . " ( " . $item->mobile . " )";
    //             return $item;
    //         });


    //     $customers_count = AppCustomer::query()
    //         ->select('id', 'name as text', 'mobile')
    //         ->where(function ($query) use ($request) {
    //             $query->where('name', 'like', '%' . $request->q . '%')
    //                 ->where('mobile', 'like', '%' . $request->q . '%');
    //         })
    //         ->skip(($request->page - 1) * 5)
    //         ->orderByDesc("id")
    //         ->take(5)
    //         ->get()
    //         ->count();

    //     return response()->json([
    //         'items' => $customers,
    //         'pagination' => ['more' => $customers_count > 0]
    //     ]);
    // }


    public function select2Ajax(Request $request)
    {
        $customers = AppCustomer::query()
        ->select('id', 'name', 'mobile')
        ->when(isRole(ROLE_AGENT), fn($query) => $query->where('agent_id', auth()->id()))
        ->orderByDesc("id")
        ->get();

        return view('contact.partial.jsonCustomer', [
            'customers' => $customers,
        ]);
    }

    public function show($id)
    {
        $customer = AppCustomer::with('farms')->find($id);
        return view('app-customer.show', compact('customer'));
    }
}
