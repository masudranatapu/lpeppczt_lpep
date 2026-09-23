<x-warehouse-layout :title="'Print Assignment ' . $assignment->invoice_no">
    @php
        $assignmentDate = \Carbon\Carbon::parse($assignment->assignment_date)->format('d M Y');
        $businessName = $business->invoice_name ?: $business->name;
        $formatQuantity = function ($quantity) {
            return rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.');
        };
    @endphp

    <style>
        .assignment-print-toolbar { display:flex; max-width:210mm; margin:0 auto 14px; align-items:center; justify-content:space-between; gap:16px; }
        .assignment-print-sheet { box-sizing:border-box; width:100%; max-width:210mm; min-height:270mm; margin:0 auto; padding:14mm; background:#fff; color:#172033; box-shadow:0 10px 35px rgba(15,23,42,.12); font-family:Arial,Helvetica,sans-serif; }
        .assignment-print-header { display:flex; justify-content:space-between; gap:32px; padding-bottom:20px; border-bottom:3px solid #172033; }
        .assignment-print-logo { display:block; max-width:175px; max-height:62px; margin-bottom:10px; object-fit:contain; }
        .assignment-print-business { margin:0 0 6px; color:#111827; font-size:23px; font-weight:800; }
        .assignment-print-meta { color:#64748b; font-size:11px; line-height:1.7; }
        .assignment-print-heading { min-width:250px; text-align:right; }
        .assignment-print-heading h1 { margin:0 0 12px; font-size:25px; font-weight:800; letter-spacing:2px; }
        .assignment-print-number { margin-top:5px; color:#64748b; font-size:12px; }
        .assignment-print-info { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin:16px 0; }
        .assignment-print-card { padding:14px 16px; border:1px solid #dbe2ea; border-radius:8px; }
        .assignment-print-label { margin-bottom:7px; color:#64748b; font-size:9px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
        .assignment-print-value { color:#172033; font-size:14px; font-weight:800; }
        .assignment-print-detail { margin-top:5px; color:#526071; font-size:11px; line-height:1.55; }
        .assignment-print-table { width:100%; border-collapse:collapse; font-size:12px; }
        .assignment-print-table thead { display:table-header-group; }
        .assignment-print-table th { padding:8px; background:#475569; color:#fff; font-size:9px; font-weight:800; letter-spacing:.8px; text-align:left; text-transform:uppercase; }
        .assignment-print-table td { padding:8px; border-bottom:1px solid #e5eaf0; color:#334155; }
        .assignment-print-table tbody tr:nth-child(even) td { background:#f8fafc; }
        .assignment-print-table .center { text-align:center; }
        .assignment-print-table .right { text-align:right; }
        .assignment-print-table tfoot td { padding:10px 8px; border-top:2px solid #475569; background:#f8fafc; font-weight:800; }
        .assignment-print-signatures { display:grid; grid-template-columns:repeat(3,1fr); gap:38px; margin-top:90px; }
        .assignment-print-signature { padding-top:8px; border-top:1px solid #94a3b8; color:#64748b; font-size:9px; text-align:center; text-transform:uppercase; }
        @page { size:A4 portrait; margin:9mm; }
        @media print {
            body { background:#fff !important; }
            body > div > aside, body > div > nav, body > div > [x-cloak], .assignment-print-hide { display:none !important; }
            body > div { min-height:0 !important; padding-left:0 !important; background:#fff !important; }
            body > div > main, body > div > main > div { padding:0 !important; }
            .assignment-print-sheet { width:100%; max-width:none; min-height:0; margin:0; padding:0; box-shadow:none; }
            .assignment-print-header, .assignment-print-info, .assignment-print-card, .assignment-print-table tr { break-inside:avoid; page-break-inside:avoid; }
            .assignment-print-table th, .assignment-print-table tbody tr:nth-child(even) td { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }
        @media(max-width:700px) { .assignment-print-sheet{min-height:0;padding:22px 16px}.assignment-print-header{display:grid}.assignment-print-heading{text-align:left}.assignment-print-info{grid-template-columns:1fr}.assignment-print-signatures{gap:14px} }
    </style>

    <div class="assignment-print-toolbar assignment-print-hide">
        <div><h1 class="m-0 text-xl font-bold text-slate-900">Stock Assignment</h1><p class="mb-0 mt-1 text-sm text-slate-500">Preview and print {{ $assignment->invoice_no }}</p></div>
        <div class="flex items-center gap-2">
            <x-warehouse.button href="{{ route('warehouse.salesman-assignments.index') }}" variant="secondary" size="sm" icon="arrow-left">Back</x-warehouse.button>
            <button type="button" onclick="window.print()" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-transparent bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V7m-8 7h8m-9.25-6h10.5A1.75 1.75 0 0 1 17 9.75v3.5A1.75 1.75 0 0 1 15.25 15H14v1.25A1.75 1.75 0 0 1 12.25 18h-4.5A1.75 1.75 0 0 1 6 16.25V15H4.75A1.75 1.75 0 0 1 3 13.25v-3.5A1.75 1.75 0 0 1 4.75 8Z" /></svg>Print Assignment</button>
        </div>
    </div>

    <main class="assignment-print-sheet">
        <header class="assignment-print-header">
            <div>
                @if($business->invoice_logo)<img src="{{ asset($business->invoice_logo) }}" class="assignment-print-logo" alt="{{ $businessName }}">@endif
                <h2 class="assignment-print-business">{{ $businessName }}</h2>
                <div class="assignment-print-meta">@if($business->mobile)<div>Phone: {{ $business->mobile }}</div>@endif @if($business->email)<div>Email: {{ $business->email }}</div>@endif</div>
            </div>
            <div class="assignment-print-heading"><h1>STOCK ASSIGNMENT</h1><div class="assignment-print-number">Invoice No: <strong>{{ $assignment->invoice_no }}</strong></div><div class="assignment-print-number">Date: <strong>{{ $assignmentDate }}</strong></div></div>
        </header>

        <section class="assignment-print-info">
            <div class="assignment-print-card"><div class="assignment-print-label">Assigned To</div><div class="assignment-print-value">{{ $assignment->salesman?->name ?: '-' }}</div><div class="assignment-print-detail">{{ $assignment->salesman?->email ?: 'No email provided' }}</div></div>
            <div class="assignment-print-card"><div class="assignment-print-label">Area Office</div><div class="assignment-print-value">{{ $assignment->warehouse?->name ?: '-' }}</div><div class="assignment-print-detail">Code: {{ $assignment->warehouse?->code ?: '-' }}@if($assignment->notes)<br>Notes: {{ $assignment->notes }}@endif</div></div>
        </section>

        <table class="assignment-print-table">
            <thead><tr><th class="center" style="width:12%">SL</th><th style="width:55%">Product</th><th style="width:15%">Unit</th><th class="right" style="width:18%">Qty</th></tr></thead>
            <tbody>@forelse($assignment->items as $item)<tr><td class="center">{{ $loop->iteration }}</td><td><strong>{{ $item->product?->product_name ?: '-' }}</strong></td><td>{{ $item->product?->unit?->short_name ?: $item->product?->unit?->actual_name ?: '-' }}</td><td class="right"><strong>{{ $formatQuantity($item->quantity) }}</strong></td></tr>@empty<tr><td colspan="4" class="center">No assigned products found.</td></tr>@endforelse</tbody>
            <tfoot><tr><td colspan="2">{{ $assignment->items->count() }} {{ \Illuminate\Support\Str::plural('Product', $assignment->items->count()) }}</td><td class="right">Total Qty</td><td class="right">{{ $formatQuantity($assignment->total_quantity) }}</td></tr></tfoot>
        </table>

        <section class="assignment-print-signatures"><div class="assignment-print-signature">Prepared By</div><div class="assignment-print-signature">LSP Signature</div><div class="assignment-print-signature">Authorized Signature</div></section>
    </main>
</x-warehouse-layout>
