<?php

namespace App\Http\Controllers\App;

use App\Models\Calf;
use App\Models\CalfBirthProblem;
use App\Models\Cattle;
use App\Models\Contact;
use App\Models\Farm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NabilAnam\SimpleUpload\SimpleUpload;
use App\Http\Controllers\Controller;
use App\Models\AppCustomer;

class CalfController extends Controller
{
    public function index()
    {
        return view('calves.index');
    }

    public function create()
    {
        $data = [
            'birthProblems' => CalfBirthProblem::query()->select('id', 'name')->pluck('name', 'id'),
            'cattles' => Cattle::query()->get(),
            'farms' => Farm::query()->get(),
        ];

        return view('calves.create', $data);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $calf = Calf::create([
                'farm_id' => $request->farm_id,
                'cattle_id' => $request->cattle_id,

                'tag' => $request->tag,
                'name' => $request->name,
                'birth_date' => $request->birth_date,
                'weight' => $request->weight,
                'gender' => $request->gender,
                'image' => (new SimpleUpload)
                    ->fileBase64($request->image)
                    ->dirName('calves')
                    ->save(),

                'created_by_type' => 'staff',
                'created_by' => $request->user_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            foreach (json_decode($request->calf_birth_problem_ids ?? '[]') as $calf_birth_problem_id) {
                $calf->birthProblems()->create([
                    'calf_birth_problem_id' => $calf_birth_problem_id,
                ]);
            }

            DB::commit();
            return response()->json('Calf saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }

    public function edit($id)
    {
        $data = [
            'customers' => Contact::query()
                ->where('type', 'customer')
                ->where('name', '<>', 'Guest')
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'birthProblems' => CalfBirthProblem::query()
                ->select('id', 'name')
                ->pluck('name', 'id'),
            'calf' => Calf::query()->findOrFail($id),
        ];
        $data['farm'] = Farm::query()
            ->find($data['calf']->farm_id);
        $data['cattle'] = Cattle::query()
            ->where('id', $data['calf']->cattle_id)
            ->select('id', 'name')
            ->first();

        return view('calves.edit', $data);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $calf = Calf::query()->find($id);

        try {
            DB::beginTransaction();

            $calf->update([
                'farm_id' => $request->farm_id,
                'cattle_id' => $request->cattle_id,

                'tag' => $request->tag,
                'name' => $request->name,
                'birth_date' => $request->birth_date,
                'weight' => $request->weight,
                'gender' => $request->gender,
                'image' => (new SimpleUpload)
                    ->file($request->image)
                    ->dirName('calves')
                    ->deleteIfExists($calf->image)
                    ->save(),
                'updated_by_type' => 'staff',
                'updated_by' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $calf->birthProblems()->delete();
            foreach (($request->calf_birth_problem_ids ?? []) as $calf_birth_problem_id) {
                $calf->birthProblems()->create([
                    'calf_birth_problem_id' => $calf_birth_problem_id,
                ]);
            }

            DB::commit();

            return response()->json('Calf updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            Calf::query()->find($id)->delete();
            DB::commit();

            return response()->json('Calf deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json('Calf couldn\'t be deleted!');
        }
    }

    public function allAsJson()
    {
        return datatables()->of(
            Calf::query()
                ->with('farm', 'cattle', 'createdBy', 'updatedBy', 'createdByCustomer', 'updatedByCustomer')
                ->select('calves.*')
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
