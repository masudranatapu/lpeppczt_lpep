<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VatGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VatGroupController extends Controller
{
    public function index()
    {
        return view('vat.index');
    }

    public function create()
    {
        return view('vat.create');
    }

    public function store(Request $request)
    {
        $vat = new VatGroup();

        $vat->vat_group_name = $request->vat_group_name;
        $vat->vat_percent = $request->vat_percent;
        if ($vat->save()) {
            return response()->json("New SD/VAT Group Added");
        }
    }

    public function edit($id)
    {
        return view('vat.edit', [
            'vat' => VatGroup::findOrFail($id)
        ]);
    }

    public function update(Request $request)
    {
        $vat = VatGroup::findOrFail($request->id);

        $vat->vat_group_name = $request->vat_group_name;
        $vat->vat_percent = $request->vat_percent;
        if ($vat->save()) {
            return response()->json("SD/VAT Group Updated");
        }
    }

    public function delete(Request $request)
    {
        Product::where('vat_group_id', $request->id)->update([
            'vat_group_id' => null
        ]);
        $vat = VatGroup::findOrFail($request->id)->delete();
        if ($vat) {
            return response()->json('SD/VAT Group Deleted Successfully');
        }
    }

    public function vatGroupAll()
    {
        return datatables()->of(VatGroup::query()->get())
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>";
                $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit">Edit</a>';
                $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
