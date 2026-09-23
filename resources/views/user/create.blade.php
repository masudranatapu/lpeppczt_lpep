@extends('layouts.dashboard')
@section('title', '| User Create')
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.usercreate')[0] }}</b></h4>
                    </div>
                </div>

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for=""> {{ __('page.usercreate')[1] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="User Name" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for=""> {{ __('Employee Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="employee_name" placeholder="Employee name" required>
                            @error('employee_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for=""> {{ __('page.phone') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile" placeholder="Mobile Number" required>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for=""> {{ __('page.usercreate')[2] }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="User Email" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for=""> {{ __('page.usercreate')[4] }} <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for=""> {{ __('page.usercreate')[5] }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" placeholder="User Password"
                                required>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for=""> {{ __('page.usercreate')[6] }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation"
                                placeholder="User Confirm Password" required>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-info">Save</button>
                    </div>

                </form>


            </div>
        </div>
    </div>

@endsection
