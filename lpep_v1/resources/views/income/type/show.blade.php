@extends('layouts.dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mt-4">
                <h4>{{ __('Income Types') }}</h4>
            </div>
            <div class="card-box">

                <div class="row">
                    <div class="col-12 col-md-2 mb-3">
                        <a href="{{ route('incomeType.index') }}"
                            class="btn btn-success waves-effect waves-light btn-rounded w-100">
                            <i class="fa fa-arrow-left mr-2"></i>
                            <span>{{ __('Back') }}</span>
                        </a>
                    </div>

                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-group">
                            <h4 class="text-center text-black">{{ $type->income_type }}</h4>
                        </div>
                    </div>

                    <div class="col-12 col-md-7">
                        <div class="d-flex justify-content-between flex-wrap">
                            <form action="{{ route('incomeType.show', $type->id) }}" method="get" class="w-100"
                                id="filterForm">
                                <div class="d-flex flex-wrap w-100">
                                    <select id="year" name="year" class="form-control mb-2 mb-md-0 mr-2"
                                        style="max-width: 200px;">
                                        <option value="" selected disabled>Select Year</option>
                                        @for ($year = 2020; $year <= now()->year; $year++)
                                            <option value="{{ $year }}"
                                                {{ old('year', $year) == $reqYear ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    <select id="month" name="month" class="form-control mb-2 mb-md-0 mr-2"
                                        style="max-width: 200px;">
                                        <option value="" selected disabled>Select Month</option>
                                        @for ($month = 1; $month <= 12; $month++)
                                            <option value="{{ $month }}"
                                                {{ old('month', $month) == $reqMonth ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                    <button class="btn btn-sm btn-success btn-rounded mb-2 mb-md-0" type="submit"
                                        style="width: 60px;height: 38px;cursor:pointer;">Filter</button>

                                    <a id="printButton" href="{{ route('incomeType.print', $type->id) }}" target="_blank"
                                        class="btn btn-sm btn-rounded mb-2 mb-md-0" title="Print File"
                                        style="width: 60px;height: 38px;cursor:pointer;">
                                        <i class="fa fa-print" style="font-size: 25px; color:black;"></i>
                                    </a>
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
                            @forelse ($incomes  as $income)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($income->income_date)->format('d') }}
                                        <small>{{ \Carbon\Carbon::parse($income->income_date)->format('M') }}</small>
                                    </td>
                                    <td>{{ $income->income_date }}
                                        <small>({{ \Carbon\Carbon::parse($income->income_date)->format('l') }})</small>
                                    </td>
                                    <td id="sub_total">
                                        {{ incomeTypeInlineTotal($type->income_type, $income->details) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center font-weight-bold">{{ __('No Record Found...!') }}
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <strong>{{ __('Total') }} =</strong>
                                </td>
                                <td>
                                    <strong id="total">

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
@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('table tbody tr').forEach(row => {
                    let subTotalText = row.querySelector('#sub_total')?.innerText.trim();
                    let subTotal = parseFloat(subTotalText);
                    if (!isNaN(subTotal)) {
                        total += subTotal;
                    }
                });
                document.getElementById('total').innerText = total.toFixed(2);
            }
            calculateTotal();
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearSelect = document.getElementById('year');
            const monthSelect = document.getElementById('month');
            const printButton = document.getElementById('printButton');

            function updatePrintLink() {
                const year = yearSelect.value;
                const month = monthSelect.value;
                let printUrl = "{{ route('incomeType.print', $type->id) }}";

                if (year && month) {
                    printUrl += `?year=${year}&month=${month}`;
                } else if (year) {
                    printUrl += `?year=${year}`;
                } else if (month) {
                    printUrl += `?month=${month}`;
                }
                printButton.href = printUrl;
            }

            yearSelect.addEventListener('change', updatePrintLink);
            monthSelect.addEventListener('change', updatePrintLink);
        });
    </script>
@endpush
