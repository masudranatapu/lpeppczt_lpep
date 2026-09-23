<?php

namespace App\Http\Controllers\App;

use App\Models\User;
use App\Models\SeedCompany;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class SeedCompanyController extends Controller
{
    public function index()
    {
        return view('seed-companies.index');
    }

    public function create()
    {
        return view('seed-companies.create');
    }

    public function store(Request $request): JsonResponse
    {
        SeedCompany::query()
            ->create([
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Seed Company saved successfully!');
    }

    public function edit($id)
    {
        $seedCompany = SeedCompany::query()->findOrFail($id);

        return view('seed-companies.edit', compact('seedCompany'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        SeedCompany::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Seed Company updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        SeedCompany::query()->where('id', $id)->delete();

        return response()->json('Seed Company deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            SeedCompany::query()
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
