@extends('layouts.dashboard')

@section('title', 'Invoice ' . $purchase->invoice_no)

@section('content')
    <style>
        .invoice-toolbar { max-width: 210mm; margin: 24px auto 12px; }
        .invoice-sheet {
            box-sizing: border-box;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px;
            padding: 16mm 15mm;
            background: #fff;
            color: #172033;
            box-shadow: 0 8px 30px rgba(15, 23, 42, .12);
            font-family: Arial, Helvetica, sans-serif;
        }
        .invoice-head { display: flex; justify-content: space-between; gap: 30px; padding-bottom: 18px; border-bottom: 2px solid #172033; }
        .invoice-logo { max-width: 180px; max-height: 66px; object-fit: contain; margin-bottom: 10px; }
        .business-name { margin: 0 0 5px; font-size: 23px; font-weight: 800; color: #111827; }
        .business-meta { color: #64748b; font-size: 12px; line-height: 1.7; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0 0 10px; font-size: 30px; letter-spacing: 3px; color: #172033; }
        .invoice-number { color: #64748b; font-size: 13px; }
        .invoice-number strong { color: #172033; }
        .invoice-info { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 22px 0; }
        .info-card { border: 1px solid #dbe2ea; border-radius: 8px; padding: 14px 16px; }
        .info-label { color: #64748b; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 7px; }
        .info-value { font-size: 14px; font-weight: 700; color: #172033; }
        .invoice-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .invoice-table thead th { padding: 11px 9px; background: #172033; color: #fff; text-align: left; font-size: 10px; letter-spacing: .5px; text-transform: uppercase; }
        .invoice-table thead th.text-right, .invoice-table td.text-right { text-align: right; }
        .invoice-table thead th.text-center, .invoice-table td.text-center { text-align: center; }
        .invoice-table tbody td { padding: 11px 9px; border-bottom: 1px solid #e5eaf0; }
        .invoice-bottom { display: grid; grid-template-columns: 1fr 270px; gap: 28px; margin-top: 22px; }
        .payment-box { padding: 14px; border: 1px solid #dbe2ea; border-radius: 8px; font-size: 12px; line-height: 1.8; }
        .payment-row { display: flex; justify-content: space-between; gap: 12px; }
        .totals { border-top: 2px solid #172033; }
        .total-row { display: flex; justify-content: space-between; padding: 9px 3px; border-bottom: 1px solid #e5eaf0; font-size: 13px; }
        .total-row.grand { padding-top: 12px; border-bottom: 0; font-size: 17px; font-weight: 800; }
        .notes { margin-top: 22px; padding: 12px 14px; background: #f7f9fc; border-radius: 7px; font-size: 11px; color: #526071; }
        .invoice-footer { margin-top: 60px; display: flex; justify-content: space-between; color: #64748b; font-size: 10px; }
        .signature { width: 170px; padding-top: 8px; border-top: 1px solid #64748b; text-align: center; }
        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff !important; }
            .d-print-none, .left-side-menu, .topbar, .content-page > .footer { display: none !important; }
            .content-page, .content, .container-fluid { margin: 0 !important; padding: 0 !important; }
            .invoice-sheet {
                box-sizing: border-box;
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 12mm 15mm;
                box-shadow: none;
                page-break-after: avoid;
            }
            .invoice-table thead th {
                background: #172033 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .invoice-table tr, .info-card, .payment-box, .invoice-bottom, .invoice-footer { page-break-inside: avoid; }
            .invoice-footer { display: flex !important; }
        }
    </style>

    <div class="invoice-toolbar d-print-none text-right">
        <a href="{{ route('warehouse-purchases.index') }}" class="btn btn-light">Back</a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print mr-1"></i> Print Invoice
        </button>
    </div>

    <main class="invoice-sheet">
        <header class="invoice-head">
            <div>
                @if ($business->invoice_logo)
                    <img src="{{ asset($business->invoice_logo) }}" class="invoice-logo" alt="{{ $business->invoice_name ?? $business->name }}">
                @endif
                <h2 class="business-name">{{ $business->invoice_name ?? $business->name }}</h2>
                <div class="business-meta">
                    @if ($business->mobile)<div>Phone: {{ $business->mobile }}</div>@endif
                    @if ($business->email)<div>Email: {{ $business->email }}</div>@endif
                    @if ($business->website)<div>{{ $business->website }}</div>@endif
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">Invoice No: <strong>{{ $purchase->invoice_no }}</strong></div>
                <div class="invoice-number">Purchase Date: <strong>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</strong></div>
            </div>
        </header>

        <section class="invoice-info">
            <div class="info-card">
                <div class="info-label">Stock Destination</div>
                <div class="info-value">{{ $purchase->warehouse?->name ?: 'Central Admin Stock' }}</div>
                @if ($purchase->warehouse?->code)<div class="business-meta">Area Office code: {{ $purchase->warehouse->code }}</div>@endif
            </div>
            <div class="info-card">
                <div class="info-label">Purchase Information</div>
                <div class="info-value">Status: Completed</div>
                <div class="business-meta">Prepared by: {{ $purchase->createdBy?->name ?: 'Administrator' }}</div>
            </div>
        </section>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 7%">SL</th>
                    <th style="width: 39%">Product</th>
                    <th class="text-right" style="width: 14%">Quantity</th>
                    <th class="text-right" style="width: 18%">Unit Price</th>
                    <th class="text-right" style="width: 22%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->product?->product_name ?: '-' }}</strong></td>
                        <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="text-right"><strong>{{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="invoice-bottom">
            <div>
                <div class="payment-box">
                    <div class="info-label">Payment Details</div>
                    <div class="payment-row"><span>Payment type</span><strong>{{ $purchase->pay_by ?: '-' }}</strong></div>
                    @if ($purchase->pay_by !== 'Cash' && $purchase->account)
                        <div class="payment-row">
                            <span>Account</span>
                            <strong>
                                @if ($purchase->pay_by === 'Mobile Banking')
                                    {{ $purchase->account->mobile_bank_name }} - {{ $purchase->account->mobile_number }}
                                @elseif ($purchase->pay_by === 'Card')
                                    {{ $purchase->account->card_number }}
                                @else
                                    {{ $purchase->account?->bank?->bank_name }} - {{ $purchase->account->bank_account_number }}
                                @endif
                            </strong>
                        </div>
                    @endif
                </div>
                @if ($purchase->notes)
                    <div class="notes"><strong>Notes:</strong> {{ $purchase->notes }}</div>
                @endif
            </div>
            <div class="totals">
                <div class="total-row"><span>Purchase Total</span><strong>{{ number_format($purchase->total_amount, 2) }}</strong></div>
                <div class="total-row"><span>Paid Amount</span><strong>{{ number_format($purchase->paid_amount, 2) }}</strong></div>
                <div class="total-row grand"><span>Due Amount</span><span>{{ number_format($purchase->due_amount, 2) }} BDT</span></div>
            </div>
        </section>

        <footer class="invoice-footer">
            <div>Thank you. This is a system-generated admin purchase invoice.</div>
            <div class="signature">Authorized Signature</div>
        </footer>
    </main>
@endsection

@section('script')
    <script>
        window.addEventListener('load', function () {
            window.setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
@endsection
