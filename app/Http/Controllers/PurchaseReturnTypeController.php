<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnType;
use Exception;
use Illuminate\Http\Request;

class PurchaseReturnTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $damageTypes = PurchaseReturnType::query()
                ->with("createdBy", "updatedBy")
                ->get();
            return datatables()
                ->of($damageTypes)
                ->addColumn('action', function ($data) {
                    $btn = "<div class='btn-group'>";
                    $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit"><i class="fa fa-edit"></i></a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created', function ($data) {
                    return $data->createdBy->name ?? '';
                })
                ->addColumn('updated', function ($data) {
                    return $data->updatedBy->name ?? '';
                })
                ->rawColumns(['action', "created", "updated"])
                ->addIndexColumn()
                ->make(true);
        }
        return view("purchase.damage.index");
    }

    public function create()
    {
        return view("purchase.damage.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        PurchaseReturnType::query()->create([
            "name" => $request->name,
            "created_by" => auth()->id()
        ]);
        return back()->with("message", "New damage type add");
    }

    public function edit($id)
    {
        return view("purchase.damage.edit", [
            'type' => PurchaseReturnType::query()->find($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => "required"
        ]);

        PurchaseReturnType::query()
            ->find($id)
            ->update([
                "name" => $request->name,
                'updated_by' => auth()->id()
            ]);
        return back()->with("message", "Damage type updated");
    }

    public function destroy($id)
    {
        try {
            PurchaseReturnType::query()
                ->find($id)
                ->delete();
            return back()->with("message", "Damage type delete");
        } catch (Exception $e) {
            return back()->with([
                'type' => "error",
                "message" => "Damage type conflict with purchase damage"
            ]);
        }
    }
}
