<?php

namespace App\Http\Controllers\App;

use App\Models\Farm;
use Devfaysal\BangladeshGeocode\Models\District;
use Devfaysal\BangladeshGeocode\Models\Division;
use Devfaysal\BangladeshGeocode\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppCustomer;
use App\Models\Union;

class FarmController extends Controller
{
    public function index()
    {
        return view('farms.index');
    }

    public function create()
    {
        $data = [
            'customers' => AppCustomer::query()
                ->select('id', 'name', 'mobile')
                ->get(),
            'divisions' => Division::query()->select('id', 'name', 'bn_name')->orderBy('name')->pluck('bn_name', 'id')
        ];

        return view('farms.create', $data);
    }

    public function store(Request $request): JsonResponse
    {
        $validation = $request->validate([
            "app_customer_id" => "required",
            "name" => "required",
            "division_id" => "required",
            "district_id" => "required",
            "upazila_id" => "required",
            "union_id" => "required",
        ]);

        $validation['created_by'] = $request->user_id;

        Farm::query()
            ->create($validation);
        return response()->json('Farm saved successfully!');
    }

    public function edit($id)
    {
        $farm = Farm::query()->with('customer')->findOrFail($id);

        $data = [
            'customers' => AppCustomer::query()
                ->select('id', 'name', 'mobile')
                ->get(),
            'farm' => $farm,
            'divisions' => Division::query()->select('id', 'name', 'bn_name')->orderBy('name')->pluck('bn_name', 'id'),
            'districts' => District::query()
                ->where('division_id', $farm->division_id ?? 1)
                ->select('id', 'name', 'bn_name')
                ->orderBy('name')
                ->pluck('bn_name', 'id'),
            'upazilas' => Upazila::query()
                ->where('district_id', $farm->district_id ?? 1)
                ->select('id', 'name', 'bn_name')
                ->orderBy('name')
                ->pluck('bn_name', 'id'),
            'unions' => Union::query()
                ->where('upazila_id', $farm->upazila_id ?? 1)
                ->select('id', 'name', 'bn_name')
                ->orderBy('name')
                ->pluck('bn_name', 'id'),
        ];

        return view('farms.edit', $data);
    }

    public function update(Request $request, $id)
    {
        Farm::query()->where('id', $id)
            ->update([
                'app_customer_id' => $request->app_customer_id,
                'name' => $request->name,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'upazila_id' => $request->upazila_id,
                'union_id' => $request->union_id,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Farm updated successfully!');
    }

    public function destroy($id)
    {
        Farm::query()->where('id', $id)->delete();

        return response()->json('Farm deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            Farm::query()
                ->when(isRole(ROLE_AGENT), fn ($query) => $query->whereRelation('customer', 'agent_id', auth()->id()))
                ->with('customer')
        )
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>" . $data->log();
                if (permission('uni3')) {
                    $btn .= '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="tableEdit">Edit</a>';
                }
                if (permission('uni4')) {
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }

    public function findByCustomerAsJson($id)
    {
        return response()->json(['data' => Farm::query()->where('app_customer_id', $id)->select('id', 'name')->get()]);
    }

    public function findDistrictsByDivisionAsJson($id)
    {
        return response()->json(['data' => District::query()->where('division_id', $id)->select('id', 'name', 'bn_name')->orderBy('name')->get()]);
    }

    public function findUpazilasByDistrictAsJson($id)
    {
        return response()->json(['data' => Upazila::query()->where('district_id', $id)->select('id', 'name', 'bn_name')->orderBy('name')->get()]);
    }
    public function findUnionsByUpazilaAsJson($id)
    {
        return response()->json(['data' => Union::query()->where('upazila_id', $id)->select('id', 'name', 'bn_name')->orderBy('name')->get()]);
    }
}
