<x-warehouse-layout :title="'Invoice ' . $sale->invoice_no">
    @php
        $saleDate = $sale->sale_date instanceof \DateTimeInterface
            ? $sale->sale_date->format('d M Y')
            : \Carbon\Carbon::parse($sale->sale_date)->format('d M Y');
        $totalQuantity = $sale->items->sum(fn ($item) => (float) $item->quantity);
        // Keep the organisation identity/logo; use the selling LSP only for
        // the contact details printed on this invoice.
        $invoiceSalesman = $sale->salesman;
        $businessName = $business->invoice_name ?: $business->name;
        $businessPhone = $invoiceSalesman?->phone ?: $business->mobile;
        $businessEmail = $invoiceSalesman?->email ?: $business->email;
    @endphp

    <style>
        .sale-invoice-toolbar {
            display: flex;
            max-width: 210mm;
            margin: 0 auto 14px;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .sale-invoice-sheet {
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 210mm;
            min-height: 270mm;
            margin: 0 auto;
            padding: 14mm;
            background: #fff;
            color: #172033;
            box-shadow: 0 10px 35px rgba(15, 23, 42, .12);
            font-family: Arial, Helvetica, sans-serif;
        }
        .sale-invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 32px;
            padding-bottom: 20px;
            border-bottom: 3px solid #172033;
        }
        .sale-invoice-brand { max-width: 58%; }
        .sale-invoice-logo { display: block; max-width: 175px; max-height: 62px; margin-bottom: 10px; object-fit: contain; }
        .sale-invoice-business { margin: 0 0 6px; color: #111827; font-size: 23px; font-weight: 800; line-height: 1.2; }
        .sale-invoice-business-meta { color: #64748b; font-size: 11px; line-height: 1.7; }
        .sale-invoice-heading { min-width: 220px; text-align: right; }
        .sale-invoice-heading h1 { margin: 0 0 12px; color: #172033; font-size: 31px; font-weight: 800; letter-spacing: 4px; }
        .sale-invoice-number { margin-top: 5px; color: #64748b; font-size: 12px; }
        .sale-invoice-number strong { color: #172033; }
        .sale-invoice-info { display: grid; grid-template-columns: 1.1fr .9fr; gap: 16px; margin: 16px 0; }
        .sale-invoice-card { padding: 14px 16px; border: 1px solid #dbe2ea; border-radius: 8px; }
        .sale-invoice-label { margin-bottom: 7px; color: #64748b; font-size: 9px; font-weight: 800; letter-spacing: 1.2px; text-transform: uppercase; }
        .sale-invoice-value { color: #172033; font-size: 14px; font-weight: 800; }
        .sale-invoice-detail { margin-top: 5px; color: #526071; font-size: 11px; line-height: 1.55; }
        .sale-invoice-detail-row { display: flex; justify-content: space-between; gap: 15px; padding: 3px 0; }
        .sale-invoice-detail-row span:first-child { color: #64748b; }
        .sale-invoice-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .sale-invoice-table thead { display: table-header-group; }
        .sale-invoice-table th { padding: 7px 8px; background: #475569; color: #fff; font-size: 9px; font-weight: 800; letter-spacing: .8px; text-align: left; text-transform: uppercase; }
        .sale-invoice-table td { padding: 7px 8px; border-bottom: 1px solid #e5eaf0; color: #334155; vertical-align: top; }
        .sale-invoice-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .sale-invoice-table .center { text-align: center; }
        .sale-invoice-table .right { text-align: right; }
        .sale-invoice-product { color: #172033; font-weight: 700; }
        .sale-invoice-table tfoot { display: table-row-group; }
        .sale-invoice-table tfoot td { padding: 8px; border-top: 2px solid #475569; border-bottom: 0; background: #f8fafc; vertical-align: middle; }
        .sale-invoice-table .footer-metric { text-align: center; }
        .sale-invoice-table .footer-metric-label { display: block; margin-bottom: 3px; color: #64748b; font-size: 7px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; }
        .sale-invoice-table .footer-metric-value { display: block; color: #172033; font-size: 13px; font-weight: 800; }
        .sale-invoice-table .footer-grand-total { border-color: #475569; background: #475569; text-align: right; }
        .sale-invoice-table .footer-grand-total .footer-metric-label { color: #cbd5e1; }
        .sale-invoice-table .footer-grand-total .footer-metric-value { color: #fff; font-size: 15px; }
        .sale-invoice-payment-summary { width: 100%; margin: 14px 0 0; }
        .sale-invoice-payment-row { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 4px; border-bottom: 1px solid #dbe4ef; color: #475569; font-size: 12px; }
        .sale-invoice-payment-row strong { color: #172033; font-size: 13px; }
        .sale-invoice-payment-row.total { border-top: 2px solid #475569; }
        .sale-invoice-payment-row.total strong { font-size: 15px; }
        .sale-invoice-payment-row.paid strong { color: #15803d; }
        .sale-invoice-payment-row.due { border-bottom: 0; }
        .sale-invoice-payment-row.due strong { color: #c2410c; font-size: 15px; }
        .sale-invoice-signature-spacer { flex: 1 1 auto; min-height: 50px; }
        .sale-invoice-signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 38px; }
        .sale-invoice-signature { padding-top: 8px; border-top: 1px solid #94a3b8; color: #64748b; font-size: 9px; text-align: center; text-transform: uppercase; }
        .sale-invoice-footer { display: flex; justify-content: space-between; gap: 20px; margin-top: 28px; padding-top: 10px; border-top: 1px solid #e5eaf0; color: #94a3b8; font-size: 9px; }

        @page {
            size: A4 portrait;
            margin: 9mm 9mm 16mm;

            @bottom-left {
                content: {!! json_encode($businessName . ' · ' . $warehouse->name) !!};
                color: #64748b;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 8px;
            }

            @bottom-center {
                content: {!! json_encode('Invoice · ' . $sale->invoice_no) !!};
                color: #94a3b8;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 8px;
            }

            @bottom-right {
                content: counter(page) " of " counter(pages);
                color: #475569;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 8px;
                font-weight: 700;
            }
        }
        @media print {
            body { background: #fff !important; }
            body > div > aside,
            body > div > nav,
            body > div > [x-cloak],
            .sale-invoice-print-hide { display: none !important; }
            body > div { min-height: 0 !important; padding-left: 0 !important; background: #fff !important; }
            body > div > main { padding: 0 !important; }
            body > div > main > div { padding: 0 !important; }
            .sale-invoice-sheet {
                display: block;
                width: 100%;
                max-width: none;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .sale-invoice-header,
            .sale-invoice-info,
            .sale-invoice-signatures,
            .sale-invoice-card,
            .sale-invoice-table tr { break-inside: avoid; page-break-inside: avoid; }
            .sale-invoice-signature-spacer { display: block; min-height: 0; }
            .sale-invoice-footer { display: none !important; }
            .sale-invoice-table th,
            .sale-invoice-table tbody tr:nth-child(even) td,
            .sale-invoice-table .footer-grand-total {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 700px) {
            .sale-invoice-sheet { min-height: 0; padding: 22px 16px; }
            .sale-invoice-header { grid-template-columns: 1fr; display: grid; }
            .sale-invoice-brand { max-width: 100%; }
            .sale-invoice-heading { min-width: 0; text-align: left; }
            .sale-invoice-info { grid-template-columns: 1fr; }
            .sale-invoice-signatures { gap: 14px; }
        }
    </style>

    <div class="sale-invoice-toolbar sale-invoice-print-hide">
        <div>
            <h1 class="m-0 text-xl font-bold text-slate-900">Sale Invoice</h1>
            <p class="mb-0 mt-1 text-sm text-slate-500">Preview and print {{ $sale->invoice_no }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-warehouse.button href="{{ route('warehouse.sales.index') }}" variant="secondary" size="sm" icon="arrow-left">Back</x-warehouse.button>
            <button type="button" onclick="window.print()" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-transparent bg-red-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V7m-8 7h8m-9.25-6h10.5A1.75 1.75 0 0 1 17 9.75v3.5A1.75 1.75 0 0 1 15.25 15H14v1.25A1.75 1.75 0 0 1 12.25 18h-4.5A1.75 1.75 0 0 1 6 16.25V15H4.75A1.75 1.75 0 0 1 3 13.25v-3.5A1.75 1.75 0 0 1 4.75 8Z" />
                </svg>
                Print Invoice
            </button>
        </div>
    </div>

    <main class="sale-invoice-sheet">
        <header class="sale-invoice-header">
            <div class="sale-invoice-brand">
                @if ($business->invoice_logo)
                    <img src="{{ asset($business->invoice_logo) }}" class="sale-invoice-logo" alt="{{ $businessName }}">
                @endif
                <h2 class="sale-invoice-business">{{ $businessName }}</h2>
                <div class="sale-invoice-business-meta">
                    @if ($businessPhone)<div>Phone: {{ $businessPhone }}</div>@endif
                    @if ($businessEmail)<div>Email: {{ $businessEmail }}</div>@endif
                    @if ($business->website)<div>{{ $business->website }}</div>@endif
                </div>
            </div>
            <div class="sale-invoice-heading">
                <h1>INVOICE</h1>
                <div class="sale-invoice-number">Invoice No: <strong>{{ $sale->invoice_no }}</strong></div>
                <div class="sale-invoice-number">Invoice Date: <strong>{{ $saleDate }}</strong></div>
            </div>
        </header>

        <section class="sale-invoice-info">
            <div class="sale-invoice-card">
                <div class="sale-invoice-label">Bill To</div>
                <div class="sale-invoice-value">{{ $sale->customer_name ?: 'Walk-in Customer' }}</div>
                <div class="sale-invoice-detail">
                    @if ($sale->customer_phone)<div>Phone: {{ $sale->customer_phone }}</div>@endif
                    @if ($sale->customer_address)<div>{{ $sale->customer_address }}</div>@endif
                </div>
            </div>
            <div class="sale-invoice-card">
                <div class="sale-invoice-label">Sale Information</div>
                <div class="sale-invoice-detail">
                    <div class="sale-invoice-detail-row"><span>Area Office</span><strong>{{ $warehouse->name ?: '-' }}</strong></div>
                    <div class="sale-invoice-detail-row"><span>LSP</span><strong>{{ $sale->salesman?->name ?: 'Area Office' }}</strong></div>
                    <div class="sale-invoice-detail-row"><span>Payment</span><strong>{{ $sale->payment_method ?: '-' }}</strong></div>
                    <div class="sale-invoice-detail-row"><span>Paid / Due</span><strong>{{ number_format((float) $sale->paid_amount, 2) }} / {{ number_format((float) $sale->due_amount, 2) }}</strong></div>
                </div>
            </div>
        </section>

        <table class="sale-invoice-table">
            <thead>
                <tr>
                    <th class="center" style="width: 12%">SL</th>
                    <th style="width: 36%">Product Description</th>
                    <th class="right" style="width: 15%">Quantity</th>
                    <th class="right" style="width: 17%">Unit Price</th>
                    <th class="right" style="width: 20%">Amount (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sale->items as $item)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="sale-invoice-product">{{ $item->product?->product_name ?: '-' }}</div>
                        </td>
                        <td class="right">{{ number_format($item->quantity, 0) }}</td>
                        <td class="right">{{ number_format($item->sale_price, 2) }}</td>
                        <td class="right"><strong>{{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center">No sale items found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <section class="sale-invoice-payment-summary" aria-label="Payment summary">
            @php($invoiceDiscount = max(0, (float) $sale->items->sum('total') - (float) $sale->total_amount))
            @if ($invoiceDiscount > 0)
                <div class="sale-invoice-payment-row"><span>Initial Total</span><strong>{{ number_format((float) $sale->items->sum('total'), 2) }} BDT</strong></div>
                <div class="sale-invoice-payment-row"><span>Discount (-)</span><strong>{{ number_format($invoiceDiscount, 2) }} BDT</strong></div>
            @endif
            <div class="sale-invoice-payment-row total"><span>Total</span><strong>{{ number_format((float) $sale->total_amount, 2) }} BDT</strong></div>
            <div class="sale-invoice-payment-row paid"><span>Paid</span><strong>{{ number_format((float) $sale->paid_amount, 2) }} BDT</strong></div>
            <div class="sale-invoice-payment-row due"><span>Due</span><strong>{{ number_format((float) $sale->due_amount, 2) }} BDT</strong></div>
        </section>

        <div class="sale-invoice-signature-spacer" aria-hidden="true"></div>

        <section class="sale-invoice-signatures" id="sale-invoice-signatures">
            <div class="sale-invoice-signature">Prepared By</div>
            <div class="sale-invoice-signature">Customer Signature</div>
            <div class="sale-invoice-signature">Authorized Signature</div>
        </section>

        <footer class="sale-invoice-footer">
            <span>{{ $businessName }} &middot; {{ $warehouse->name }}</span>
            <span>System-generated invoice &middot; {{ $sale->invoice_no }}</span>
        </footer>
    </main>

    <x-slot name="script">
        <script>
            (function () {
                const sheet = document.querySelector('.sale-invoice-sheet');
                const spacer = document.querySelector('.sale-invoice-signature-spacer');
                const signatures = document.getElementById('sale-invoice-signatures');

                function alignSignaturesToPageBottom() {
                    if (!sheet || !spacer || !signatures) return;

                    spacer.style.height = '0px';

                    const pixelsPerMillimeter = 96 / 25.4;
                    const printablePageHeight = (297 - 9 - 16) * pixelsPerMillimeter;
                    const bottomClearance = 6 * pixelsPerMillimeter;
                    const sheetTop = sheet.getBoundingClientRect().top;
                    const signatureTop = signatures.getBoundingClientRect().top;
                    const usedHeight = Math.max(signatureTop - sheetTop, 0);
                    const usedOnFinalPage = usedHeight % printablePageHeight;
                    let spacerHeight = printablePageHeight - usedOnFinalPage - signatures.offsetHeight - bottomClearance;

                    if (spacerHeight < 0) spacerHeight += printablePageHeight;
                    spacer.style.height = `${Math.max(spacerHeight, 0)}px`;
                }

                window.addEventListener('beforeprint', alignSignaturesToPageBottom);
                window.addEventListener('afterprint', function () {
                    if (spacer) spacer.style.height = '';
                });
            })();

            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    window.print();
                }, 350);
            });
        </script>
    </x-slot>
</x-warehouse-layout>
