<?php

namespace App\Http\Controllers\App;

use App\Models\CattleGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CattleGroupController extends Controller
{
    public function index()
    {
        return view('cattle-groups.index');
    }

    public function create()
    {
        return view('cattle-groups.create');
    }

    public function store(Request $request): JsonResponse
    {
        CattleGroup::query()
            ->create([
                'name' => $request->name,
                'created_by' => $request->user_id,
            ]);

        return response()->json('Cattle Group saved successfully!');
    }

    public function edit($id)
    {
        $cattleGroup = CattleGroup::query()->findOrFail($id);

        return view('cattle-groups.edit', compact('cattleGroup'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        CattleGroup::query()->where('id', $id)
            ->update([
                'name' => $request->name,
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return response()->json('Cattle Group updated successfully!');
    }

    public function destroy($id): JsonResponse
    {
        CattleGroup::query()->where('id', $id)->delete();

        return response()->json('Cattle Group deleted successfully!');
    }

    public function allAsJson()
    {
        return datatables()->of(
            CattleGroup::query()
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
