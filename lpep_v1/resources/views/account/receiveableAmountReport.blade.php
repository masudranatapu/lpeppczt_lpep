@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <div class="row">
                    <div class="col-md-3">
                        <h4 class="m-t-0 header-title mb-3"><b>{{ __('page.account1report')[8] }}</b></h4>
                    </div>
                    <div class="col-md-9">
                        <form action="" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-12 m-auto row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="start">
                                            <span class="input-group-addon bg-primary b-0 text-white">to</span>
                                            <input type="date" class="form-control" name="end">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="contact_id" class="form-control" id="">
                                            <option value="">select customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">
                                                    {{ $customer->name }}({{ $customer->mobile }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info" type="submit">Search</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>




                <table id="datatable-buttons" class="table table-bordered table-hover mt-4" cellspacing="0">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>{{ __('page.account1report')[0] }}</th>
                            <th>{{ __('page.account1report')[1] }}</th>
                            <th>{{ __('page.account1report')[2] }}</th>
                            <th>{{ __('page.account1report')[3] }}</th>
                            <th>{{ __('page.account1report')[4] }}</th>
                            <th>{{ __('page.account1report')[5] }}</th>
                            <th>{{ __('page.account1report')[3] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $business = currentBranch();
                        @endphp
                        @foreach ($sales as $sale)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $sale->sale_date }}</td>
                                <td>{{ $business->name }}</td>
                                <td>{{ date('Y') . $sale->id }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->total_amount - $sale->paying_amount }}</td>
                                <td>
                                    <a href="{{ route('sale.invoice', $sale->id) }}"
                                        class="btn btn-sm btn-info">Invoice</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
