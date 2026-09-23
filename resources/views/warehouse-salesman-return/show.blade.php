@extends('layouts.dashboard')
@section('title', 'Return ' . $return->invoice_no)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box mt-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1">{{ $return->invoice_no }}</h4>
                    <div>Area Office: <strong>{{ $return->warehouse?->name ?: '-' }}</strong></div>
                    <div>LSP: <strong>{{ $return->salesman?->name ?: '-' }}</strong></div>
                    <div>Return Date Time: {{ \Carbon\Carbon::parse($return->return_date_time)->format('d-m-Y h:i A') }}</div>
                    <div>Notes: {{ $return->notes ?: '-' }}</div>
                </div>
                <div class="text-right">
                    <h4>Total: {{ number_format($return->total_quantity, 2) }}</h4>
                    <a href="{{ route('warehouse-salesman-returns.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="theme-primary text-white">
                        <tr>
                            <th>SL</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th class="text-right">Returned Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($return->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product?->product_name ?: '-' }}</td>
                                <td>{{ $item->product?->unit?->short_name ?: $item->product?->unit?->actual_name ?: '-' }}</td>
                                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No returned products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
