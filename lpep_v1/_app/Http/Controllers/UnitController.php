<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('unit.index', [
            'units' => Unit::query()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('unit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'actual_name' => 'required'
        ]);

        $unit = new Unit;
        $unit->actual_name = $request->actual_name;
        $unit->short_name = $request->short_name;
        $unit->is_decimal = $request->is_decimal == 'true';
        $unit->created_by = Auth::id();

        if ($unit->save()) {
            return response()->json('New Unit Added Successfully');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('unit.edit', [
            'unit' => Unit::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unitUpdate(Request $request)
    {
        $request->validate([
            'actual_name' => 'required'
        ]);

        $unit = Unit::findOrFail($request->id);
        $unit->actual_name = $request->actual_name;
        $unit->short_name = $request->short_name;
        $unit->is_decimal = $request->is_decimal == 'true';

        if ($unit->save()) {
            return response()->json('Unit Updated Successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unitDeleted(Request $request)
    {
        $unit = Unit::findOrFail($request->id)->delete();
        if ($unit) {
            return response()->json("Unit Deleted Successfully");
        }
    }

    public function allUnit()
    {
        return datatables()->of(Unit::query()->get())
            ->addColumn('decimal', function ($data) {
                return $data->is_decimal ? '<span style="color: #444;">Yes</span>' : '<span style="color: #444;">No</span>';
            })
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>";
                if (permission('uni3')) {
                    $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="unitEdit">Edit</a>';
                }
                if (permission('uni4')) {
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'decimal'])
            ->addIndexColumn()
            ->make(true);
    }
}
