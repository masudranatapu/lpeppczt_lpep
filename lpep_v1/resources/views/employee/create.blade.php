@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.employeecreate')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[1] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employee_name" placeholder="Employee Name"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[2] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="designation" placeholder="Designation" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[4] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="email" placeholder="Email" id="email" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[5] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile" placeholder="Mobile" id="mobile"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[6] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nid_number" placeholder="NID Number"
                                id="nid_number" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[7] }}<span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="picture" placeholder="Mobile" id="picture"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[8] }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address" placeholder="Address" id="address"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[9] }}<span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="joing_date" placeholder="Joing Date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">{{ __('page.employeecreate')[10] }}<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="salary" placeholder="Salary" id="salary"
                                required>
                        </div>
                    </div>


                    <div class="text-right mt-4">
                        <button class="btn btn-success" type="submit"
                            id="submit">{{ __('page.employeecreate')[11] }}</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

@endsection
