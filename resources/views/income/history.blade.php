@extends('layouts.pos_layout')
@push('css')
    <style>
@media print {
    @page {
        size: A4 portrait; /* Keep it portrait */
        margin: 8mm;
    }

    /* Hide unnecessary elements */
    .no-print {
        display: none;
    }

    /* Rotating the entire content */
    .rotate-content {
        display: flex;
        flex-direction: column; /* Ensure content flows normally */
        writing-mode: vertical-rl; /* Keep it rotated */
        text-align: center;
        align-items: center; /* Center horizontally */
        justify-content: flex-start; /* Align content to the top */
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

    th, td {
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

    body{
        background: #fff;
        padding: 0;
    }
    .img-logo{
        display: block !important;
    }


    /* .card-box {
        display: none;
    }
    .printable-content {
        display: block;
    } */

}

body{
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
                    <a href="{{ route('income.index') }}" class="btn btn-success btn-rounded waves-effect waves-light m-b-5 no-print">
                        <i class="fa fa-arrow-left m-r-5"></i>
                        <span>{{ __('Back') }}</span>
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-rounded no-print">
                        <i class="fa fa-print"></i> Print Report
                    </button>
                     <button type="button" id="btnExportExcel" class="btn btn-info btn-rounded no-print">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </button>
                    <button type="button" id="btnExportPdf" class="btn btn-danger btn-rounded no-print">
                        <i class="fa fa-file-pdf-o"></i> Export to PDF
                    </button>
                    <!-- Filter Form -->
                    <form action="{{ route('income.history') }}" method="GET" class="no-print">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-2 col-md-6">
                                <label for="filter_type">Filter Type</label>
                                <select name="filter_type" id="filter_type" class="form-control">
                                    <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>Life Time</option>
                                    <option value="month_year" {{ $filterType === 'month_year' ? 'selected' : '' }}>Month &amp; Year</option>
                                    <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Year Only</option>
                                    <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom Date</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label for="area_office">Area Office</label>
                                <select name="area_office" id="area_office" class="form-control">
                                    <option value="">All Area Offices</option>
                                    @foreach ($areaOfficeOptions as $office)
                                        <option value="{{ $office }}" {{ $areaOffice === $office ? 'selected' : '' }}>
                                            {{ $office }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 year-filter-field">
                                <label for="year">Year</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    @foreach ($yearOptions as $yearOption)
                                        <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 month-filter-field">
                                <label for="month">Month</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="">Select Month</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}"
                                            {{ $month == $m ? 'selected' : '' }}>
                                            {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 custom-filter-field">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control"
                                    value="{{ $fromDate }}">
                            </div>
                            <div class="col-lg-2 col-md-6 custom-filter-field">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control"
                                    value="{{ $toDate }}">
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-rounded waves-effect waves-light form-control">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="rotate-content">
                    <img src="/{{ currentBranch()->logo }}" style="height: 80px; width: 80px;  rotate: 90deg;" class="d-none img-logo">
                    <h4 class="text-center">
                        Income History - {{ $filterLabel }}
                    </h4>

                    <div class="table-responsive">
                        <table id="incomeTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Employee Name') }}</th>
                                    <th>{{ __('W/D') }}</th>
                                    <th>{{ __('Target') }}</th>
                                    <th>{{ __('A/A%') }}</th>
                                    @foreach ($incomeTypes as $type)
                                        <th>{{ $type }}</th>
                                    @endforeach
                                    <th><strong>{{ __('Total') }}</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotal = 0;
                                    $incomeTypeTotals = array_fill_keys($incomeTypes->toArray(), 0);
                                @endphp
                                @foreach ($employeeEarnings as $employee => $earnings)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $employee }}</td>
                                        <td>{{ $employeeWorkingDays[$employee] ?? 0 }}</td>
                                        <td>{{ number_format($employeeTargets[$employee]) }}</td>
                                        <td>{{ number_format($employeeAA[$employee],2) }}%</td>
                                        @php $rowTotal = 0; @endphp

                                        @foreach ($incomeTypes as $type)
                                            @php
                                                $amount = $earnings[$type] ?? 0;
                                                $rowTotal += $amount;
                                                $incomeTypeTotals[$type] += $amount;
                                            @endphp
                                        <td>{{ $amount }}</td>
                                        @endforeach

                                        <td><strong>{{ $rowTotal }}</strong></td>
                                        @php $grandTotal += $rowTotal; @endphp
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>{{ __('Total') }}</strong></td>
                                    <td><strong>{{ $totalWorkingDays }}</strong></td>
                                    <td><strong>{{ number_format($totalTarget) }}</strong></td>
                                    <td><strong>{{ number_format($averageAA, 2) }}%</strong></td>
                                    @foreach ($incomeTypes as $type)
                                        <td><strong>{{ number_format($incomeTypeTotals[$type]) }}</strong></td>
                                    @endforeach
                                    <td><strong>{{ number_format($grandTotal) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Keep order: jsPDF first, then autotable -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    <!-- SheetJS -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
    (function() {
        const $ = (sel) => document.querySelector(sel);

        const filterLabel = @json($filterLabel);
        const baseTitle = `Income_History_${filterLabel}`.replaceAll(' ', '_').replaceAll('/', '-');

        function updateFilterFields() {
            const filterType = $('#filter_type').value;
            const showYear = filterType === 'month_year' || filterType === 'year';
            const showMonth = filterType === 'month_year';
            const showCustom = filterType === 'custom';

            document.querySelectorAll('.year-filter-field').forEach((field) => {
                field.style.display = showYear ? '' : 'none';
            });
            document.querySelectorAll('.month-filter-field').forEach((field) => {
                field.style.display = showMonth ? '' : 'none';
            });
            document.querySelectorAll('.custom-filter-field').forEach((field) => {
                field.style.display = showCustom ? '' : 'none';
            });

            $('#year').required = showYear;
            $('#month').required = showMonth;
            $('#from_date').required = showCustom;
            $('#to_date').required = showCustom;

            $('#year').disabled = !showYear;
            $('#month').disabled = !showMonth;
            $('#from_date').disabled = !showCustom;
            $('#to_date').disabled = !showCustom;
        }

        function libsReady() {
            const ok = (window.XLSX && window.jspdf && window.jspdf.jsPDF && window.jspdf.jsPDF instanceof Function && window.jspdf);
            if (!ok) console.error('Export libs not ready. window.XLSX:', !!window.XLSX, ' window.jspdf:', !!window.jspdf);
            return ok;
        }

        function exportExcel() {
            if (!libsReady()) return alert('Excel/PDF library not loaded. Check CDN or CSP.');
            const table = document.getElementById('incomeTable');
            if (!table) return console.error('#incomeTable not found');
            try {
                const wb = XLSX.utils.table_to_book(table, { sheet: 'Income' });
                XLSX.writeFile(wb, `${baseTitle}.xlsx`);
            } catch (e) {
                console.error('Excel export error:', e);
                alert('Failed to export Excel. See console for details.');
            }
        }

        function exportPdf() {
            if (!libsReady()) return alert('Excel/PDF library not loaded. Check CDN or CSP.');
            const table = document.getElementById('incomeTable');
            if (!table) return console.error('#incomeTable not found');
            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation:'landscape', unit:'pt', format:'a4' });
                doc.setFontSize(12);
                doc.text(baseTitle.replaceAll('_',' '), 40, 32);
                doc.autoTable({
                    html: '#incomeTable',
                    startY: 50,
                    styles: { fontSize: 8, cellPadding: 3, overflow: 'linebreak' },
                    headStyles: { fillColor: [240,240,240] },
                    didDrawPage: function () {
                        const pageCount = doc.internal.getNumberOfPages();
                        const pageSize = doc.internal.pageSize;
                        const current = doc.internal.getCurrentPageInfo().pageNumber;
                        doc.setFontSize(8);
                        doc.text(`Page ${current} of ${pageCount}`, pageSize.getWidth() - 80, pageSize.getHeight() - 10);
                    }
                });
                doc.save(`${baseTitle}.pdf`);
            } catch (e) {
                console.error('PDF export error:', e);
                alert('Failed to export PDF. See console for details.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnX = $('#btnExportExcel');
            const btnP = $('#btnExportPdf');
            if (btnX) btnX.addEventListener('click', exportExcel);
            if (btnP) btnP.addEventListener('click', exportPdf);
            $('#filter_type').addEventListener('change', updateFilterFields);
            updateFilterFields();
        });
    })();
    </script>
@endsection
