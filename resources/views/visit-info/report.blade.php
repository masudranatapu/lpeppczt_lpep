@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">Visit Report</h3>

                {{-- Filter --}}
                <form method="GET" action="{{ route('visit-info.report') }}" class="d-flex gap-2 align-items-center report-actions">
                    From
                    <input type="date" name="visit_date_from" value="{{ request('visit_date_from') }}" class="form-control"
                        required>
                    To
                    <input type="date" name="visit_date_to" value="{{ request('visit_date_to') }}" class="form-control"
                        required>
                    <select name="agent_id" class="form-control" required>
                        <option value="all" {{ $selectedAgentId === 'all' ? 'selected' : '' }}>All LSPs</option>
                        @foreach($agents as $id => $name)
                            <option value="{{ $id }}" {{ (string) $selectedAgentId === (string) $id ? 'selected' : '' }}>
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
                        Please select <strong>Visit Date</strong> and <strong>LSP</strong> to view the report.
                    </div>
                @else
                    @if($visits->isEmpty())
                        <div class="alert alert-warning mb-0">No data found for the selected filters.</div>
                    @else
                        <div id="reportContent">
                            {{-- ===== TOP SUMMARY (PDF + UI) ===== --}}
                            <div id="pdfTopSection" style="margin-bottom: 10px; font-size: 14px; text-align:center;">
                                <h4 style="margin:0;">Visit Report</h4>

                               

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
                            <div id="reportDetailsSection" class="report-details-grid">
                                <div class="report-detail-item">
                                    <strong>Area Office:</strong> {{ $selectedAgentAreaOffice }}
                                </div>
                                <div class="report-detail-item">
                                    <strong>LSP:</strong> {{ $selectedAgentName }}
                                </div>
                                <div class="report-detail-item">
                                    @foreach(['C.F', 'Re.V', 'N.V','Reg.v', 'A.s'] as $type)
                                        <strong>{{ $type }}:</strong> {{ $visitTypeCounts[$type] ?? 0 }}@if(!$loop->last) &nbsp; @endif
                                    @endforeach
                                </div>
                            </div>
                            {{-- <h4 class="mb-2">
                                Report for:
                                <strong>
                                    {{ date('d-m-Y', strtotime(request('visit_date_from'))) }} to
                                    {{ date('d-m-Y', strtotime(request('visit_date_to'))) }}
                                    (LSP: {{ $agents[request('agent_id')] ?? 'N/A' }})
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
                                            <th>File</th>
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
                                                <td>{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') . ' ' . ($visit->created_at?->format('h:i A') ?? '') : 'N/A' }}
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
                                                <td>@if($visit->attachment_path)<a href="{{ asset(str_starts_with($visit->attachment_path, 'uploads/') ? $visit->attachment_path : 'storage/' . $visit->attachment_path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary" title="Open attachment"><i class="fa fa-file"></i></a>@else<span class="text-muted">N/A</span>@endif</td>
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
        .report-details-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 10px 0 14px;
            padding: 10px;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
            font-size: 13px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            .main-header,
            .main-sidebar,
            .topbar,
            .left.side-menu,
            .footer,
            .right-bar,
            .report-actions,
            .card-header .btn,
            .card-header form .btn,
            .card-header form select,
            .card-header form input {
                display: none !important;
            }

            html,
            body {
                width: 100% !important;
                min-width: 0 !important;
                overflow: visible !important;
            }

            .content-page,
            .content,
            .container-fluid,
            .row,
            .col-12 {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
                min-height: 0 !important;
            }

            body,
            .content-wrapper,
            .container-fluid,
            .card-box,
            .card {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
                border: none;
                box-shadow: none;
            }

            .card-header {
                display: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            #reportContent,
            .table-responsive {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                overflow: visible !important;
            }

            #pdfTopSection {
                margin-bottom: 6px !important;
                break-inside: avoid;
            }

            #pdfTopSection h4 { font-size: 15px; font-weight: 700; }
            #pdfTopSection p { margin: 2px 0 !important; }

            .report-details-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 6px;
                padding: 7px;
                margin: 5px 0 7px;
                font-size: 8px;
                break-inside: avoid;
            }

            .table {
                width: 100% !important;
                font-size: 7px;
                color: #000 !important;
            }

            .table th,
            .table td {
                padding: 2px !important;
                line-height: 1.1 !important;
                vertical-align: top !important;
                white-space: normal !important;
                overflow-wrap: anywhere;
            }

            #visitTable {
                table-layout: fixed !important;
                margin-bottom: 8px !important;
            }

            #visitTable thead { display: table-header-group; }
            #visitTable th:nth-child(1) { width: 3% !important; }
            #visitTable th:nth-child(2) { width: 6% !important; }
            #visitTable th:nth-child(3) { width: 12% !important; }
            #visitTable th:nth-child(4) { width: 10% !important; }
            #visitTable th:nth-child(5) { width: 8% !important; }
            #visitTable th:nth-child(6) { width: 9% !important; }
            #visitTable th:nth-child(7) { width: 12% !important; }
            #visitTable th:nth-child(8) { width: 14% !important; }
            #visitTable th:nth-child(9) { width: 9% !important; }
            #visitTable th:nth-child(10) { width: 17% !important; }
            #visitTable ul { margin: 0 !important; padding-left: 12px !important; }

            .table tr {
                break-inside: avoid;
            }

            #summaryTable {
                width: 100% !important;
            }

            #summarySection {
                margin-top: 5px !important;
                break-inside: avoid;
            }

            #summarySection h5 { margin: 0 0 3px !important; font-size: 11px; }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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
            const mainData = [['Visit Report']];

            document.querySelectorAll('#pdfTopSection p').forEach(p => {
                mainData.push([p.innerText.replace(/\s+/g, ' ').trim()]);
            });

            document.querySelectorAll('#reportDetailsSection .report-detail-item').forEach(item => {
                mainData.push([item.innerText.replace(/\s+/g, ' ').trim()]);
            });

            mainData.push([]);

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
            const doc = new jsPDF('p', 'mm', 'a4');

            let startY = 15;

            // ===== TITLE =====
            doc.setFontSize(16);
            doc.text('Visit Report', 105, startY, { align: 'center' });

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
                    doc.text(line, 105, startY, { align: 'center' });
                    startY += 5;
                });

                startY += 3;
            }

            // ===== REPORT DETAILS =====
            const detailItems = Array.from(
                document.querySelectorAll('#reportDetailsSection .report-detail-item')
            ).map(item => item.innerText.replace(/\s+/g, ' ').trim());

            if (detailItems.length) {
                doc.autoTable({
                    body: [detailItems],
                    startY: startY,
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2,
                        valign: 'middle',
                        lineColor: [210, 210, 210],
                        lineWidth: 0.2
                    },
                    bodyStyles: {
                        fillColor: [248, 249, 250],
                        textColor: [30, 30, 30]
                    }
                });

                startY = doc.lastAutoTable.finalY + 4;
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

            const rowCount = rows.length;
            const detailFontSize = rowCount > 18 ? 5 : (rowCount > 10 ? 6 : 7);

            doc.autoTable({
                head: [headers],
                body: rows,
                startY: startY,
                margin: { left: 5, right: 5 },
                styles: { fontSize: detailFontSize, cellPadding: 1.2, overflow: 'linebreak' },
                headStyles: { fontSize: detailFontSize, cellPadding: 1.2 },
                showHead: 'everyPage'
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

                doc.text('Summary', 105, finalY, { align: 'center' });

                doc.autoTable({
                    head: [headers2],
                    body: rows2,
                    startY: finalY + 3,
                    margin: { left: 5, right: 5 },
                    styles: { fontSize: 7, cellPadding: 1.2 },
                    showHead: 'everyPage'
                });
            }

            doc.save('Visit_Report.pdf');
        }
    </script>
@endsection
