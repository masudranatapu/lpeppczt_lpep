<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index()
    {
        return view('customer_group.index');
    }

    public function create()
    {
        return view('customer_group.create');
    }

    public function store(Request $request)
    {
        $customer = new CustomerGroup;
        $customer->name = $request->name;
        $customer->amount = $request->amount;
        $customer->created_by = auth()->id();

        if ($customer->save()) {
            return response()->json("Customer Group Added Successfully");
        }
    }

    public function edit($id)
    {
        return view('customer_group.edit', [
            'group' => CustomerGroup::findOrFail($id),
        ]);
    }

    public function groupUpdate(Request $request)
    {
        $customer = CustomerGroup::findOrFail($request->id);
        $customer->name = $request->name;
        $customer->amount = $request->amount;
        $customer->updated_by = auth()->id();

        if ($customer->save()) {
            return response()->json("Customer Group Updated Successfully");
        }
    }

    public function delete(Request $request)
    {
        CustomerGroup::findOrFail($request->id)->delete();

        return response()->json('Customer Group Deleted Successfully');
    }


    public function allCustomerGroup(Request $request)
    {
        return datatables()->of(CustomerGroup::query()->get())
            ->addColumn('action', function ($data) {
                $btn = "<div class='btn-group'>" . $data->log();
                $btn = $btn . '<a data-id="' . $data->id . '" class="btn btn-primary btn-sm" id="customerGroupEdit">Edit</a>';
                $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" id="deleteData" class="btn btn-danger btn-sm">Delete</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
    }
}
