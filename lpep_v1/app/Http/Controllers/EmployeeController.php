<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employee.index', [
            'employees' => Employee::query()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('employee.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $employee = new Employee();
        $employee->employee_name = $request->employee_name;
        $employee->designation = $request->designation;
        $employee->email = $request->email;
        $employee->mobile = $request->mobile;
        $employee->nid_number = $request->nid_number;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->joing_date = $request->joing_date;
        if ($request->hasFile('picture')) {
            $path = $request->picture->store('uploads/employee', ['disk' => 'public_uploads']);
            $employee->picture = json_encode($path);
        }
        if ($employee->save()) {
            return redirect()->route('employee.index')->with('message', "New Employee Added Successfully");
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
        return view('employee.edit', [
            'employee' => Employee::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->employee_name = $request->employee_name;
        $employee->designation = $request->designation;
        $employee->email = $request->email;
        $employee->mobile = $request->mobile;
        $employee->nid_number = $request->nid_number;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->joing_date = $request->joing_date;
        if ($request->hasFile('picture')) {
            unlink(json_decode($employee->picture));
            $path = $request->picture->store('uploads/employee', ['disk' => 'public_uploads']);
            $employee->picture = json_encode($path);
        }
        if ($employee->save()) {
            return redirect()->route('employee.index')->with('message', "New Employee Added Successfully");
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
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return redirect()->route('employee.index')->with('message', "Employee Delete Successfully");
        } catch (\Throwable $th) {
            return redirect()->route('employee.index');
        }
    }

    public function allEmployee()
    {
        return datatables()->of(Employee::with('business')->get())
            ->addColumn('img', function ($data) {
                $img = "<img src='/uploads/employee/{$data->picture}' height='40pc'>";
                return $img;
            })
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
