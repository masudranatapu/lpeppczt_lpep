<?php

namespace App\Http\Controllers;

use App\Models\CowFeed;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CowFeedController extends Controller
{
    // You already have index() for DataTables; keeping it here for completeness
    public function index(Request $request)
    {
    if ($request->ajax()) {
        $query = CowFeed::query()
            ->when($request->filled('item_name'), function ($q) use ($request) {
                $q->where('item_name', 'like', '%' . $request->item_name . '%');
            })
            ->orderByDesc('id');

        return datatables()->of($query)
            ->addIndexColumn()
            ->editColumn('purchase_date', function ($row) {
                return $row->purchase_date
                    ? \Carbon\Carbon::parse($row->purchase_date)->format('d-m-Y')
                    : '';
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date
                    ? \Carbon\Carbon::parse($row->end_date)->format('d-m-Y')
                    : '';
            })
            ->addColumn('total_price', function ($row) {
                return $row->unit_price && $row->qty
                    ? number_format($row->unit_price * $row->qty, 2)
                    : '';
            })
            ->addColumn('action', function ($data) {
                $btn  = "<div class='btn btn-group'>";
                $btn .= "<button type='button' class='btn btn-sm btn-info' title='View' id='viewCowFeed' data-id='{$data->id}'><i class='fa fa-eye'></i></button>";
                $btn .= "<button type='button' class='btn btn-sm btn-primary' title='Edit' id='editCowFeed' data-id='{$data->id}'><i class='fa fa-edit'></i></button>";
                $btn .= "<button type='button' class='btn btn-sm btn-danger' title='Delete' id='deleteData' data-id='{$data->id}'><i class='fa fa-trash'></i></button>";
                $btn .= "</div>";
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('cow-feed.index');
    }


    /**
     * Modal: Create form partial
     */
    public function create()
    {
        $farms = Farm::select('id', 'name')->orderBy('name')->get();
        return view('cow-feed.create', compact('farms'));
    }

    /**
     * Store (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'      => ['required', Rule::in(['Feed','wheat bran','Kura','Cheata gouar','Grass'])],
            'volume'         => ['nullable','string','max:191'],
            'unit_price'     => ['nullable','numeric','between:0,999999.99'],
            'qty'            => ['nullable','integer','min:0'],
            'previus_stock'  => ['nullable','string','max:191'], // keep field name as in your migration
            'purchase_date'  => ['nullable','date'],
            'end_date'       => ['nullable','date','after_or_equal:purchase_date'],
            'farm_id'        => ['nullable','exists:farms,id'],
        ]);

        $cowFeed = CowFeed::create([
            'item_name'     => $validated['item_name'],
            'volume'        => $validated['volume'] ?? null,
            'unit_price'    => $validated['unit_price'] ?? null,
            'qty'           => $validated['qty'] ?? null,
            'previus_stock' => $validated['previus_stock'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'end_date'      => $validated['end_date'] ?? null,
            'farm_id'       => $validated['farm_id'] ?? null,
            'create_by'     => Auth::id(), // from logged-in user
        ]);

        return response()->json([
            'message' => 'Cow feed created successfully.',
            'id'      => $cowFeed->id,
        ]);
    }

    /**
     * Modal: Show (read-only partial)
     */
    public function show(CowFeed $cowFeed)
    {
        return view('cow-feed.view', compact('cowFeed'));
    }

    /**
     * Modal: Edit form partial
     */
    public function edit(CowFeed $cowFeed)
    {
        $farms = Farm::select('id', 'name')->orderBy('name')->get();
        return view('cow-feed.edit', compact('cowFeed','farms'));
    }

    /**
     * Update (AJAX)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_name'      => ['required', Rule::in(['Feed','wheat bran','Kura','Cheata gouar','Grass'])],
            'volume'         => ['nullable','string','max:191'],
            'unit_price'     => ['nullable','numeric','between:0,999999.99'],
            'qty'            => ['nullable','integer','min:0'],
            'previus_stock'  => ['nullable','string','max:191'],
            'purchase_date'  => ['nullable','date'],
            'end_date'       => ['nullable','date','after_or_equal:purchase_date'],
            'farm_id'        => ['nullable','exists:farms,id'],
        ]);
        $cowFeed = CowFeed::find($id);
        $cowFeed->update($validated);
        
        return redirect()->back();
        // return response()->json([
        //     'message' => 'Cow feed updated successfully.',
        // ]);
    }

    /**
     * Delete (AJAX)
     */
    public function destroy(CowFeed $cowFeed)
    {
        $cowFeed->delete();

        return response()->json([
            'message' => 'Cow feed deleted successfully.',
        ]);
    }
}
