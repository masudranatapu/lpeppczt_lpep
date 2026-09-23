@extends('layouts.dashboard')

@section('content')
<style>
    .purchase-report-actions .dt-buttons { display: flex; gap: 6px; flex-wrap: wrap; }
    .purchase-report-table th, .purchase-report-table td { vertical-align: middle !important; white-space: nowrap; }
    @media print {
        .d-print-none, .dt-buttons, .dataTables_filter, .dataTables_info, .dataTables_paginate { display: none !important; }
        .content-page, .content, .container-fluid { margin: 0 !important; padding: 0 !important; }
        .card-box { box-shadow: none !important; border: 0 !important; }
        @page { size: A4 landscape; margin: 10mm; }
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card-box mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div>
                    <h4 class="mb-1">Purchase {{ $purchase->invoice_no }}</h4>
                    <div>Stock Location: {{ $purchase->warehouse?->name ?: 'Central Admin Stock' }}</div>
                    <div>Date: {{ $purchase->purchase_date }}</div>
                    @if ($purchase->attachment)
                        <div class="mt-2 d-print-none">
                            <a href="{{ asset($purchase->attachment) }}" target="_blank" rel="noopener"
                                class="btn btn-outline-info btn-sm">
                                <i class="fa fa-paperclip"></i> View Supplier Invoice
                            </a>
                        </div>
                    @endif
                </div>
                <div><strong>Total Product Purchase: {{ number_format($purchase->total_amount, 2) }} BDT</strong></div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 d-print-none flex-wrap">
                <h5 class="mb-2">Purchase Product Details</h5>
                <div id="purchase-report-actions" class="purchase-report-actions mb-2"></div>
            </div>

            @php
                $purchaseTotal = $purchase->items->sum('total');
                $saleTotal = $purchase->items->sum(function ($item) {
                    return (float) $item->sale_price * (float) $item->quantity;
                });
                $profitTotal = $saleTotal - $purchaseTotal;
            @endphp

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="text-muted">Purchase Total</div>
                            <div class="h4 mb-0">{{ number_format($purchaseTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="text-muted">Expected Sale Total</div>
                            <div class="h4 mb-0">{{ number_format($saleTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body py-3">
                            <div class="text-muted">Expected Profit</div>
                            <div class="h4 mb-0">{{ number_format($profitTotal, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border mb-3">
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="text-muted small">Payment Method</div>
                            <strong>{{ $purchase->pay_by ?: '-' }}</strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-muted small">Payment Account</div>
                            <strong>
                                @if ($purchase->pay_by === 'Mobile Banking')
                                    {{ $purchase->account?->mobile_bank_name }} {{ $purchase->account?->mobile_number }}
                                @elseif ($purchase->pay_by === 'Card')
                                    {{ $purchase->account?->card_number }}
                                @elseif ($purchase->pay_by === 'Bank Account')
                                    {{ $purchase->account?->bank?->bank_name }} {{ $purchase->account?->bank_account_number }}
                                @else
                                    -
                                @endif
                            </strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-muted small">Paid Amount</div>
                            <strong>{{ number_format($purchase->paid_amount, 2) }}</strong>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-muted small">Due Amount</div>
                            <strong>{{ number_format($purchase->due_amount, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if ($purchase->payments->isNotEmpty())
                <div class="mt-4 mb-3">
                    <h5>Payment History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Payment Type</th>
                                    <th>Account</th>
                                    <th class="text-right">Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->payments->sortBy('payment_date') as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->payment_date }}</td>
                                        <td>{{ $payment->pay_by }}</td>
                                        <td>
                                            @if ($payment->pay_by === 'Mobile Banking')
                                                {{ $payment->account?->mobile_bank_name }} {{ $payment->account?->mobile_number }}
                                            @elseif ($payment->pay_by === 'Card')
                                                {{ $payment->account?->card_number }}
                                            @elseif ($payment->pay_by === 'Bank Account')
                                                {{ $payment->account?->bank?->bank_name }} {{ $payment->account?->bank_account_number }}
                                            @else
                                                Cash
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
            <table id="warehouse-purchase-report-table" class="table table-bordered purchase-report-table">
                <thead>
                    <tr>
                        <th>Purchase Date</th>
                        <th>SL</th>
                        <th>Supplier</th>
                        <th>Invoice No</th>
                        <th>Product</th>
                        <th>Unit Size</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>Sale Price</th>
                        <th>Total</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $purchase->purchase_date }}</td>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->supplier?->name ?: '-' }}</td>
                            <td>{{ $purchase->invoice_no }}</td>
                            <td>{{ $item->product?->product_name }}</td>
                            <td>{{ $item->product?->unit?->short_name ?: $item->product?->unit?->actual_name ?: '-' }}</td>
                            <td>{{ number_format($item->quantity, 0) }}</td>
                            <td>{{ number_format($item->purchase_price, 2) }}</td>
                            <td>{{ number_format($item->sale_price, 2) }}</td>
                            <td>{{ number_format($item->total, 2) }}</td>
                            <td>{{ number_format(((float) $item->sale_price - (float) $item->purchase_price) * (float) $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="9" class="text-right">Grand Total</th>
                        <th>{{ number_format($purchaseTotal, 2) }}</th>
                        <th>{{ number_format($profitTotal, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        const reportTitle = @json('Area Office Purchase - ' . $purchase->invoice_no);
        const table = $('#warehouse-purchase-report-table').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            dom: 'Brt',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: reportTitle,
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: reportTitle,
                    orientation: 'landscape',
                    pageSize: 'A4',
                    footer: true
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-primary btn-sm',
                    title: reportTitle,
                    footer: true
                }
            ]
        });

        table.buttons().container().appendTo('#purchase-report-actions');
    });
</script>
@endsection
