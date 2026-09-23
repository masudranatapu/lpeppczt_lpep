<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetCategoryController extends Controller
{
    public function index()
    {
        return view('asset.assetCategory', [
            'items' => AssetCategory::query()->orderBy('id', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {
        AssetCategory::insert([
            'asset_category' => $request->asset_category,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('asset.category')->with('message', "New Asset Category Added");
    }

    public function edit($id)
    {
        return view('asset.assetCategory', [
            'items' => AssetCategory::query()->orderBy('id', 'desc')->get(),
            'type' => AssetCategory::findOrFail($id),
        ]);
    }

    public function update(Request $request)
    {
        AssetCategory::find($request->id)->update([
            'asset_category' => $request->asset_category,
        ]);

        return redirect()->route('asset.category')->with('message', "Asset Category Updated");
    }

    public function destroy($id)
    {
        AssetCategory::findOrFail($id)->delete();
        return redirect()->route('asset.category')->with('message', "Asset Category Deleted");
    }
}
