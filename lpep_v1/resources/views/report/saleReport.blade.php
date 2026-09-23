@extends('layouts.dashboard')
@section('title', ' | Sales Report')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card table-responsive mt-4">
                <div class="card-header">
                    <h4 style="font-size: 26px;" class="header-title"><b>{{ __('page.sreport')[9] }}</b></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" class="mb-4" method="GET">
                                <div class="row d-flex justify-content-end">
                                    @php
                                       $users = \App\Models\User::query()
                                            ->active()
                                            ->get();
                                    @endphp
                                    @if (permission('filterByUser'))
                                        <div class="col-md-3">
                                            <select name="user" id="user" class="form-control">
                                                <option value="">All User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ request('user') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-md-5">
                                        <div class="">
                                            <div class="
                                            input-daterange input-group" id="date-range">
                                            <input type="text" placeholder="Start Date"
                                                class="form-control datepicker startdate" name="start"
                                                value="{{ request('start') ?? '' }}" autocomplete="off" required>
                                            <span class="input-group-addon bg-primary b-0 text-white">to</span>
                                            <input type="text" placeholder="End Date"
                                                class="form-control datepicker enddate" name="end"
                                                value="{{ request('end') ?? '' }}" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <button class="btn btn-info btn-block" type="submit">Search</button>
                                </div>
                        </div>
                        </form>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-rep-plugin">
                            <div class="table-responsive" id="tablefixed">
                                {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'id' => 'data-table'], true) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
@section('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    {!! $dataTable->scripts() !!}

    <script>
        $(function() {
            $(".datepicker").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });
        $(".startdate").change(function() {
            $(".enddate").val($(this).val())
        })

        $(".business").on('change', function() {
            let business = $(this).val()
            let html = `<option value="">All user</option>`;
            $.get(`/user-business-wise/${business}`, function(response) {
                response.forEach(element => {
                    html += `<option value="${element.id}">${element.name}</option>`;
                });
                $("#user").html(html)
            })
        })
    </script>
@endsection
