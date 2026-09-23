<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Category;

        $category->category_name = $request->category_name;
        $category->created_by = Auth::id();
        $category->status = 1;
        if ($category->save()) {
            return response()->json('New Category Added Successfully');
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
        return view('category.edit', [
            'category' => Category::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function categoryUpdate(Request $request)
    {
        $category = Category::findOrFail($request->id);

        $category->category_name = $request->category_name;
        if ($category->save()) {
            return response()->json('Category Updated Successfully');
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

    public function categoryStatus(Request $request)
    {
        $category = Category::findOrFail($request->id);
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

    public function allCategory()
    {
        return datatables()->of(Category::query()->get())
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>";
                if (permission('cat3')) {
                    $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="categoryEdit">Edit</a>';
                }

                if (permission('cat4')) {
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
