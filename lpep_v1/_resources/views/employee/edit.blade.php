@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Update Employee</b></h4>
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <form method="POST" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Employee Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $employee->employee_name }}"
                                name="employee_name" placeholder="Employee Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Designation<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $employee->designation }}"
                                name="designation" placeholder="Designation" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Email<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->email }}" class="form-control" name="email"
                                placeholder="Email" id="email" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Mobile<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->mobile }}" class="form-control" name="mobile"
                                placeholder="Mobile" id="mobile" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">NID Number<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->nid_number }}" class="form-control"
                                name="nid_number" placeholder="NID Number" id="nid_number" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Picture<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="picture" placeholder="Mobile" id="picture">
                        </div>
                        <div class="col-md-6">
                            <label for="">Address<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->address }}" class="form-control" name="address"
                                placeholder="Address" id="address" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">Joining Date<span class="text-danger">*</span></label>
                            <input type="date" value="{{ $employee->joing_date }}" class="form-control"
                                name="joing_date" placeholder="Joing Date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Salary<span class="text-danger">*</span></label>
                            <input type="text" value="{{ $employee->salary }}" class="form-control" name="salary"
                                placeholder="Salary" id="salary" required>
                        </div>
                    </div>


                    <div class="text-right mt-4">
                        <button class="btn btn-success" type="submit" id="submit">Save</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection
