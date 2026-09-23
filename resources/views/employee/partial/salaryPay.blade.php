@extends('layouts.dashboard')

@section('content')
    
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive mt-4">
            <h4 class="m-t-0 header-title mb-4"><b>Salary Pay</b></h4>

            <form action="{{ route('salary.paid') }}" method="POST">
                @csrf
                <input type="hidden" class="form-control" name="employee_id" placeholder="Employee Name" value="{{ $employee->id }}" required>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="">Employee Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="employee_name" placeholder="Employee Name" value="{{ $employee->employee_name }}" readonly required>
                        @error('employee_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="salary" class="">Employee Monthly Salaray</label> 
                        <input type="text" id="salary" name="employee_monthly_salary" value="{{ $employee->salary }}" placeholder="Salaray" class="form-control" readonly>
                        @error('employee_monthly_salary')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
        
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="salary" class="col-form-label">Salaray Date</label> 
                        <input type="date" name="salary_date" id="salary" value="<?php echo date('Y-m-d');?>" placeholder="Salaray" class="form-control">
                        @error('salary_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="salary_month" class="col-form-label">Month</label> 
                        <select class="form-control select2" name="salary_month">
                            <option value="" selected>Select</option>
                            <option value="January">January</option>
                            <option value="February">February</option> 
                            <option value="March">March</option> 
                            <option value="April">April</option> 
                            <option value="May">May</option> 
                            <option value="June">June</option> 
                            <option value="July">July</option> 
                            <option value="August">August</option> 
                            <option value="September">September</option> 
                            <option value="October">October</option> 
                            <option value="November">November</option> 
                            <option value="December">December</option>
                        </select> 
                        @error('salary_month')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="salary" class="">Salaray Year</label> 
                        <input type="number" id="" name="salary_year" value="{{ date('Y') }}" placeholder="Salaray" class="form-control">
                        @error('salary_year')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="" >Payment Type</label> 
                        <select name="pay_by" id="pay_by" class="form-control" required="">
                            <option value="Cash">Cash</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                            <option value="Card">Card</option>
                            <option value="Bank Account">Bank Account</option>
                        </select>
                        @error('pay_by')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="salary" class="col-form-label">Salaray Date</label> 
                        <input type="text" name="amount" id="salary" value="0" placeholder="Salaray" class="form-control">
                        @error('amount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-2">
                        <div id="account_info" class="mt-4">

                        </div>
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
@section('script')
    <script>
        $("body").on('change',"#pay_by",function() {
            // purchase.payment.account
            let account_type = $(this).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('expense.payment.account') }}",{_token: _token,account_type: account_type},function(data) {
                $("#account_info").html(data)
                $(".select3").select2();
            })

        })
    </script>
@endsection
