@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>{{ __('page.employee')[0] }}</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        @if (permission('em2'))
                            <a href="{{ route('employee.create') }}" class="btn btn-primary waves-effect waves-light m-b-5"
                                id="addNew"> <i class="fa fa-plus-square m-r-5"></i>
                                <span>{{ __('page.employee')[1] }}</span> </a>
                        @endif
                    </div>
                </div>

                <table id="data-table" class="table table-striped table-bordered mt-4" cellspacing="0" width="100%">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.employee')[2] }}</th>
                            <th>{{ __('page.employee')[3] }}</th>
                            <th>{{ __('page.employee')[4] }}</th>
                            <th>{{ __('page.employee')[5] }}</th>
                            <th>{{ __('page.employee')[6] }}</th>
                            <th>{{ __('page.employee')[7] }}</th>
                            <th>{{ __('page.employee')[8] }}</th>
                            <th>{{ __('page.employee')[9] }}</th>
                            <th>{{ __('page.employee')[10] }}</th>
                            <th>{{ __('page.employee')[11] }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td>
                                    <img src="{{ json_decode($employee->picture) }}" height="40px" alt="">
                                </td>
                                <td>{{ currentBranch()->name }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td>
                                    {{ $employee->mobile }}
                                </td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->salary }}</td>
                                <td>{{ $employee->joing_date }}</td>
                                <td>

                                    <div class="btn-group btn-sm">
                                        <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                            data-toggle="dropdown" aria-expanded="false"> Action <span
                                                class="caret"></span> </button>
                                        <div class="dropdown-menu" x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            @if (permission('em3'))
                                                <a href="{{ route('employee.edit', $employee->id) }}"
                                                    class="dropdown-item">Edit</a>
                                            @endif
                                            @if (permission('em4'))
                                                <a id="delete" href="{{ route('employee.delete', $employee->id) }}"
                                                    class="dropdown-item">Delete</a>
                                            @endif
                                            @if (permission('sal1'))
                                                <a href="{{ route('employee.salary.pay', $employee->id) }}"
                                                    class="dropdown-item">Pay Salary</a>
                                                {{-- <a onclick="slaryPay({{ $employee->id }})" class="dropdown-item">Pay Salary</a> --}}
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Business Modal Start -->
    <div class="modal fade bd-example-modal-xl" id="unitModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 100%">
            <div class="modal-content" id="modalcontent">

            </div>
        </div>
    </div>
    <!-- Business Modal End -->

@endsection
@section('script')
    <script>
        function slaryPay(employee_id) {
            $.get(`/pay/${employee_id}/salary`, function(data) {
                $('#modalcontent').html(data)
                $("#unitModal").modal('show')
            });
        }
    </script>
@endsection
