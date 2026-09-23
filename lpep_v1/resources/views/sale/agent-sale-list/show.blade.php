@extends('layouts.dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ __('View Income Details') }}</h4>
            </div>
            <div class="card-box">

                <div class="row">
                    <div class="col-md-2">
                        <a href="{{ route('agentsalelist.index') }}" class="btn btn-success waves-effect waves-light m-b-5">
                            <i class="fa fa-arrow-left m-r-5"></i>
                            <span>{{ __('Back') }}</span>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <h4 class="text-center text-black">{{ $user->name }} -
                                <small>({{ $user->employee_name }})</small>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <form action="{{ route('agentsalelist.show', $user->id) }}" method="get">
                                <div class="d-flex">
                                    <select name="year"  class="form-control" style="width: 200px;">
                                        <option value="" selected disabled>Select Year</option>
                                        @for ($year = 2020; $year <= now()->year; $year++)
                                            <option value="{{ $year }}"
                                                {{ old('year', $year) == $reqYear ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    <select name="month"  class="form-control" style="width: 200px;">
                                        <option value="" selected disabled>Select Month</option>
                                        @for ($month = 1; $month <= 12; $month++)
                                            <option value="{{ $month }}"
                                                {{ old('month', $month) == $reqMonth ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                                        @endfor
                                    </select>
                                    <button class="btn btn-sm btn-success btn-rounded" type="submit"
                                        style="width: 60px;height: 38px;margin-left: 10px; cursor:pointer;">Filter</button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('#') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->agentSales  as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d') }}
                                        <small>{{ \Carbon\Carbon::parse($sale->sale_date)->format('M') }}</small>
                                    </td>
                                    <td>{{ $sale->sale_date }}
                                        <small>({{ \Carbon\Carbon::parse($sale->sale_date)->format('l') }})</small>
                                    </td>
                                    <td>{{ $sale->total_amount_sum }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center font-weight-bold">{{ __('No Record Found...!') }}</td>
                                </tr>
                            @endforelse

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <strong>{{ __('Total') }} =</strong>
                                </td>
                                <td>
                                    <strong>
                                        {{ $user->agentSales->sum('total_amount_sum') ?? 0 }}
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                </div>

            </div>

        </div>
    </div>
@endsection

@push('css')
    <style>
        .table th {
            font-size: 12px !important;
        }
    </style>
@endpush
