<?php

namespace App\Http\Controllers\App;

use App\Models\CattleDisease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CattleDiseaseController extends Controller
{
    public function index()
    {
        return view('cattle-diseases.index');
    }

    public function create()
    {
        return view('cattle-diseases.create');
    }

    public function store(Request $request): JsonResponse
    {
        CattleDisease::query()
            ->create([
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Disease saved successfully!');
    }

    public function edit($id)
    {
        $cattleDisease = CattleDisease::query()->findOrFail($id);

        return view('cattle-diseases.edit', compact('cattleDisease'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        CattleDisease::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Disease updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        CattleDisease::query()->where('id', $id)->delete();

        return response()->json('Disease deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            CattleDisease::query()
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
