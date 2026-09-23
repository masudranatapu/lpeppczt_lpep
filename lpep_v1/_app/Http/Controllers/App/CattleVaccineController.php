<?php

namespace App\Http\Controllers\App;

use App\Models\CattleDisease;
use App\Models\CattleVaccine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CattleVaccineController extends Controller
{
    public function index()
    {
        return view('cattle-vaccines.index');
    }

    public function create()
    {
        $diseases = CattleDisease::query()->select('id', 'name')->pluck('name', 'id');

        return view('cattle-vaccines.create', compact('diseases'));
    }

    public function store(Request $request): JsonResponse
    {
        CattleVaccine::query()
            ->create([
                'disease_id' => $request->disease_id,
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Disease saved successfully!');
    }

    public function edit($id)
    {
        $diseases = CattleDisease::query()->select('id', 'name')->pluck('name', 'id');
        $cattleVaccine = CattleVaccine::query()->findOrFail($id);

        return view('cattle-vaccines.edit', compact('diseases', 'cattleVaccine'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        CattleVaccine::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'disease_id' => $request->disease_id,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Disease updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        CattleVaccine::query()->where('id', $id)->delete();

        return response()->json('Disease deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            CattleVaccine::query()
                ->with('disease', 'createdBy', 'updatedBy')
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
