<?php

namespace App\Http\Controllers\App;

use App\Models\InsuranceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InsuranceTypeController extends Controller
{
    public function index()
    {
        return view('insurance-types.index');
    }

    public function create()
    {
        return view('insurance-types.create');
    }

    public function store(Request $request): JsonResponse
    {
        InsuranceType::query()
            ->create([
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Insurance Company saved successfully!');
    }

    public function edit($id)
    {
        $insuranceType = InsuranceType::query()->findOrFail($id);

        return view('insurance-types.edit', compact('insuranceType'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        InsuranceType::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Insurance Company updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        InsuranceType::query()->where('id', $id)->delete();

        return response()->json('Insurance Company deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            InsuranceType::query()
                ->with('createdBy', 'updatedBy')
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
}
