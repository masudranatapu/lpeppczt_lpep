@extends('layouts.pos_layout')
@push('css')
    <style>
        @media print {
            @page {
                size: A4 portrait;
                /* Keep it portrait */
                margin: 8mm;
            }

            /* Hide unnecessary elements */
            .no-print {
                display: none;
            }

            /* Rotating the entire content */
            .rotate-content {
                display: flex;
                flex-direction: column;
                /* Ensure content flows normally */
                writing-mode: vertical-rl;
                /* Keep it rotated */
                text-align: center;
                align-items: center;
                /* Center horizontally */
                justify-content: flex-start;
                /* Align content to the top */
                height: 100%;
                width: 100%;
                white-space: nowrap;
            }


            /* Table styles */
            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: auto;
                page-break-inside: avoid;
            }

            th,
            td {
                border: 1px solid black;
                padding: 6px;
                text-align: left;
                font-size: 10px;
                white-space: normal;
            }

            h2 {
                font-size: 16px;
                text-align: center;
                margin-bottom: 10px;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .img-logo {
                display: block !important;
            }


            /* .card-box {
            display: none;
        }
        .printable-content {
            display: block;
        } */

        }

        body {
            background: #fff !important;
            padding: 0 5px !important;
        }
    </style>
@endpush
@section('pos')
    <div class="row">
        <div class="col-12">

            <div class="">

                <div>
                    <a href="{{ route('income.index') }}"
                        class="btn btn-success btn-rounded waves-effect waves-light m-b-5 no-print">
                        <i class="fa fa-arrow-left m-r-5"></i>
                        <span>{{ __('Back') }}</span>
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-rounded no-print">
                        <i class="fa fa-print"></i> Print Report
                    </button>

                    <!-- Filter Form -->
                    <form action="{{ route('income.history') }}" method="GET" class="no-print">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="year">Year</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    @for ($i = date('Y'); $i >= 2024; $i--)
                                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="month">Month</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="">Select Month</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}"
                                            {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit"
                                    class="btn btn-success btn-rounded waves-effect waves-light form-control">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="rotate-content">
                    <img src="/{{ currentBranch()->logo }}" style="height: 80px; width: 80px;  rotate: 90deg;"
                        class="d-none img-logo">
                    <h4 class="text-center">
                        @if ($month && $year)
                            Monthly Income History OF {{ $month }}-{{ $year }}
                        @else
                            Life Time Income History
                        @endif
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Sectors') }}</th>
                                    <th>{{ __('Sales') }}</th>
                                    <th>{{ __('COGS') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotal = 0;
                                    $incomeTypeTotals = array_fill_keys($incomeTypes->toArray(), 0);
                                @endphp

                                @foreach ($incomeTypes as $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $type }}</td>
                                        <td>{{ $type }}</td>
                                        <td>{{ $type }}</td>
                                        <td>{{ $type }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>{{ __('Total') }}</strong></td>
                                    {{-- @foreach ($incomeTypes as $type)
                                        <td><strong>{{ number_format($incomeTypeTotals[$type]) }}</strong></td>
                                    @endforeach
                                    <td><strong>{{ number_format($grandTotal) }}</strong></td> --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
