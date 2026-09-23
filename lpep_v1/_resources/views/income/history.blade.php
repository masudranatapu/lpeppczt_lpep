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
                        <div class="row">
                            <div class="col-md-4">
                                <label for="year">Year</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Select Year</option>
                                    @for ($i = date('Y'); $i >= 2024; $i--)
                                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                            {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
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
                        @if ($month && $year)
                            Monthly Income History OF {{ $month }}-{{ $year }}
                        @else
                            Life Time Income History
                        @endif
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

        const month = @json($month ?? null);
        const year  = @json($year ?? null);
        const baseTitle = (month && year) ? `Monthly_Income_History_${month}-${year}` : `Lifetime_Income_History`;

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
        });
    })();
    </script>
@endsection
