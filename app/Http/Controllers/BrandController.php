<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('brand.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Brand();

        $category->brand_name = $request->brand_name;
        $category->created_by = Auth::id();
        $category->status = 1;
        if ($category->save()) {
            return response()->json('New Brand Added Successfully');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('brand.edit', [
            'brand' => Brand::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function brandUpdate(Request $request)
    {
        $category = Brand::findOrFail($request->id);

        $category->brand_name = $request->brand_name;
        if ($category->save()) {
            return response()->json('Brand Updated Successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function brandStatus(Request $request)
    {
        $category = Brand::findOrFail($request->id);
        if ($category->status == 1) {
            $category->status = 0;
            $category->save();
            return response()->json('Category Deactived Successfully');
        } else {
            $category->status = 1;
            $category->save();
            return response()->json('Category Active Successfully');
        }
    }

    public function allBrand()
    {
        return datatables()->of(Brand::query()->get())
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>";
                if (permission('bra3')) {
                    $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="brandEdit">Edit</a>';
                }

                if (permission('bra4')) {
                    if ($data->status == 1) {
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Deactive</a>';
                    } else {
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-warning btn-sm">Active</a>';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
