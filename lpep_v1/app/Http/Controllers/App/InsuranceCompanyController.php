<?php

namespace App\Http\Controllers\App;

use App\Models\InsuranceCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InsuranceCompanyController extends Controller
{
    public function index()
    {
        return view('insurance-companies.index');
    }

    public function create()
    {
        return view('insurance-companies.create');
    }

    public function store(Request $request): JsonResponse
    {
        InsuranceCompany::query()
            ->create([
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Insurance Company saved successfully!');
    }

    public function edit($id)
    {
        $insuranceCompany = InsuranceCompany::query()->findOrFail($id);

        return view('insurance-companies.edit', compact('insuranceCompany'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        InsuranceCompany::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Insurance Company updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        InsuranceCompany::query()->where('id', $id)->delete();

        return response()->json('Insurance Company deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            InsuranceCompany::query()
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
