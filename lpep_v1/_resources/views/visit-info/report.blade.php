@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">Visit Report</h3>

                {{-- Filter --}}
                <form method="GET" action="{{ route('visit-info.report') }}" class="d-flex gap-2 align-items-center">
                    From
                    <input type="date" name="visit_date_from" value="{{ request('visit_date_from') }}" class="form-control"
                        required>
                    To
                    <input type="date" name="visit_date_to" value="{{ request('visit_date_to') }}" class="form-control"
                        required>
                    <select name="agent_id" class="form-control" required>
                        <option value="">-- Select Agent --</option>
                        @foreach($agents as $id => $name)
                            <option value="{{ $id }}" {{ (string) request('agent_id') === (string) $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->filled('visit_date_from') || request()->filled('visit_date_to') || request()->filled('agent_id'))
                        <a href="{{ route('visit-info.report') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                    <button type="button" onclick="window.print()" class="btn btn-outline-dark">
                        <i class="fas fa-print"></i> Print
                    </button>
                    @if($hasFilters && !$visits->isEmpty())
                        <button type="button" onclick="exportToExcel()" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button type="button" onclick="exportToPDF()" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    @endif
                </form>
            </div>

            <div class="card-body">
                @if(!$hasFilters)
                    <div class="alert alert-info mb-0">
                        Please select <strong>Visit Date</strong> and <strong>Agent</strong> to view the report.
                    </div>
                @else
                    @if($visits->isEmpty())
                        <div class="alert alert-warning mb-0">No data found for the selected filters.</div>
                    @else
                        <div id="reportContent">
                            {{-- ===== TOP SUMMARY (PDF + UI) ===== --}}
                            <div id="pdfTopSection" style="margin-bottom: 10px; font-size: 12px; text-align:center;">
                                <h4 style="margin:0;">Visit Report Summary</h4>

                               

                                <p>
                                    <strong>Date:</strong>
                                    {{ \Carbon\Carbon::parse(request('visit_date_from'))->format('d M Y') }}
                                    to
                                    {{ \Carbon\Carbon::parse(request('visit_date_to'))->format('d M Y') }}
                                </p>

                                <p>
                                    <strong>Total Beneficiaries:</strong> {{ $totalCustomers }} |
                                    <strong>Working Days:</strong> {{ $days }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <p class="mb-0">
                                    <strong>Area Name: </strong>
                                </p>
                                <p class="mb-0">
                                    <strong>Agent:</strong> {{ $agents[request('agent_id')] ?? 'N/A' }}
                                </p>
                            
                                <p class="mb-0">
                                    <strong>Group Number:</strong> {{ $groupNumbers->implode(', ') }}
                                </p>
                            
                                <p class="mb-0">
                                   
                                    @foreach(['C.F', 'Re.V', 'N.V', 'Reg.v', 'A.s'] as $type)
                                        {{ $type }} : {{ $visitTypeCounts[$type] ?? 0 }}@if(!$loop->last) &nbsp;&nbsp; @endif
                                    @endforeach
                                </p>
                            </div>
                            {{-- <h4 class="mb-2">
                                Report for:
                                <strong>
                                    {{ date('d-m-Y', strtotime(request('visit_date_from'))) }} to
                                    {{ date('d-m-Y', strtotime(request('visit_date_to'))) }}
                                    (Agent: {{ $agents[request('agent_id')] }})
                                </strong>
                            </h4> --}}

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle" id="visitTable">
                                    <thead>
                                        <tr>
                                            <th style="width:70px">SL</th>
                                            <th>Visit Nature</th>
                                            <th>Beneficiary Name</th>
                                            <th>Beneficiary Cell No.</th>
                                            <th>Group Number</th>
                                            <th>Beneficiary Number</th>
                                            <th>Date</th>
                                            <th style="width:220px">Memo/Invoice No(s)</th>
                                            <th style="width:140px" class="text-end">Total Amount</th>
                                            <th>Fee Types (itemized)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visits as $i => $visit)
                                            @php
                                                $total = $visit->fees->sum('amount');
                                                $memos = is_array($visit->memo_no) ? $visit->memo_no : [];
                                            @endphp
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $visit->visit_type ?? 'N/A' }}</td>
                                                <td>{{ $visit->appCustomer->name ?? 'N/A' }}</td>
                                                <td>{{ $visit->appCustomer->mobile ?? 'N/A' }}</td>
                                                <td>{{ $visit->customer_number ?? 'N/A' }}</td>
                                                <td>{{ $visit->beneficiary_number ?? 'N/A' }}</td>
                                                <td>{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d h:i A') : 'N/A' }}
                                                </td>
                                                <td>
                                                    @if(count($memos))
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($memos as $m)
                                                                <li>{{ $m }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($total) }} BDT</td>
                                                <td>
                                                    @if($visit->fees->count())
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($visit->fees as $fee)
                                                                <li>{{ $fee->fee_type }}: {{ number_format($fee->amount) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted">No fees</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Grouped sum by fee_type --}}
                            @if($feeSummary->count())
                                <div class="mt-4" id="summarySection">
                                    <h5 class="mb-2">Summary by Fee Type</h5>

                                    <table class="table table-sm table-bordered w-auto" id="summaryTable">
                                        <thead>
                                            <tr>
                                                <th>Fee Type</th>
                                                <th class="text-end">Daily Target</th>
                                                <th class="text-end">Total Target</th>
                                                <th class="text-end">Achieve Amount</th>
                                                <th class="text-end">Achievement %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $grandTotalAchieve = 0;
                                                $grandTotalTarget = 0;
                                            @endphp

                                            @foreach($subTargets as $type => $dailySubTarget)
                                                @php
                                                    $achieveAmount = $feeSummary[$type] ?? 0;
                                                    $targetAmount = $dailySubTarget * $days;
                                                    $achievementPercent = $targetAmount > 0
                                                        ? ($achieveAmount / $targetAmount) * 100
                                                        : 0;

                                                    $grandTotalAchieve += $achieveAmount;
                                                    $grandTotalTarget += $targetAmount;
                                                @endphp

                                                <tr>
                                                    <td>{{ $type }}</td>
                                                    <td class="text-end">{{ number_format($dailySubTarget) }} BDT</td>
                                                    <td class="text-end">{{ number_format($targetAmount) }} BDT</td>
                                                    <td class="text-end">{{ number_format($achieveAmount) }} BDT</td>
                                                    <td class="text-end">{{ number_format($achievementPercent, 2) }}%</td>
                                                </tr>
                                            @endforeach

                                            @php
                                                $grandAchievementPercent = $grandTotalTarget > 0
                                                    ? ($grandTotalAchieve / $grandTotalTarget) * 100
                                                    : 0;
                                            @endphp

                                            <tr>
                                                <th>Grand Total</th>
                                                <th class="text-end">{{ number_format($dailyTarget) }} BDT</th>
                                                <th class="text-end">{{ number_format($grandTotalTarget) }} BDT</th>
                                                <th class="text-end">{{ number_format($grandTotalAchieve) }} BDT</th>
                                                <th class="text-end">{{ number_format($grandAchievementPercent, 2) }}%</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
    {{-- Print stylesheet --}}
    <style>
        @media print {

            .main-header,
            .main-sidebar,
            .card-header .btn,
            .card-header form .btn,
            .card-header form select,
            .card-header form input {
                display: none !important;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .table {
                font-size: 12px;
            }
        }
    </style>

    {{-- CDN Libraries --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        function exportToExcel() {
            const wb = XLSX.utils.book_new();

            // Extract main table data
            const mainTable = document.getElementById('visitTable');
            const mainData = [];

            // Headers
            const headers = [];
            mainTable.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            mainData.push(headers);

            // Rows
            mainTable.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    // Handle lists in cells
                    const lists = td.querySelectorAll('ul li');
                    if (lists.length > 0) {
                        const items = Array.from(lists).map(li => li.textContent.trim()).join('; ');
                        row.push(items);
                    } else {
                        row.push(td.textContent.trim());
                    }
                });
                mainData.push(row);
            });

            const ws1 = XLSX.utils.aoa_to_sheet(mainData);
            XLSX.utils.book_append_sheet(wb, ws1, "Visit Report");

            // Extract summary table if exists
            const summaryTable = document.getElementById('summaryTable');
            if (summaryTable) {
                const summaryData = [];
                summaryTable.querySelectorAll('tr').forEach(tr => {
                    const row = [];
                    tr.querySelectorAll('th, td').forEach(cell => {
                        row.push(cell.textContent.trim());
                    });
                    summaryData.push(row);
                });

                const ws2 = XLSX.utils.aoa_to_sheet(summaryData);
                XLSX.utils.book_append_sheet(wb, ws2, "Summary");
            }

            const fileName = `Visit_Report_${new Date().toISOString().slice(0, 10)}.xlsx`;
            XLSX.writeFile(wb, fileName);
        }
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            let startY = 15;

            // ===== TITLE =====
            doc.setFontSize(16);
            doc.text('Visit Report', 148, startY, { align: 'center' });

            startY += 8;

            // ===== TOP SUMMARY (CENTER) =====
            const top = document.getElementById('pdfTopSection');

            if (top) {
                const lines = [];

                top.querySelectorAll('p').forEach(p => {
                    lines.push(p.innerText.trim());
                });

                doc.setFontSize(10);

                lines.forEach(line => {
                    doc.text(line, 148, startY, { align: 'center' });
                    startY += 5;
                });

                startY += 3;
            }

            // ===== MAIN TABLE =====
            const table = document.getElementById('visitTable');

            const headers = [];
            table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText));

            const rows = [];
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => row.push(td.innerText));
                rows.push(row);
            });

            doc.autoTable({
                head: [headers],
                body: rows,
                startY: startY,
                styles: { fontSize: 8 }
            });

            // ===== SUMMARY TABLE =====
            const summaryTable = document.getElementById('summaryTable');

            if (summaryTable) {
                const headers2 = [];
                summaryTable.querySelectorAll('thead th').forEach(th => headers2.push(th.innerText));

                const rows2 = [];
                summaryTable.querySelectorAll('tbody tr').forEach(tr => {
                    const row = [];
                    tr.querySelectorAll('td, th').forEach(td => row.push(td.innerText));
                    rows2.push(row);
                });

                const finalY = doc.lastAutoTable.finalY + 8;

                doc.text('Summary', 148, finalY, { align: 'center' });

                doc.autoTable({
                    head: [headers2],
                    body: rows2,
                    startY: finalY + 3,
                    styles: { fontSize: 9 }
                });
            }

            doc.save('Visit_Report.pdf');
        }
    </script>
@endsection