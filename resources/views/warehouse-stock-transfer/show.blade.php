@extends('layouts.dashboard')
@section('title', 'Stock Transfer '.$transfer->invoice_no)
@section('content')
<div class="row"><div class="col-12"><div class="card-box mt-4">
    <div class="d-flex justify-content-between"><div><h4>{{ $transfer->invoice_no }}</h4><div>Area Office: <strong>{{ $transfer->warehouse?->name }}</strong></div><div>Date: {{ $transfer->transfer_date }}</div></div><h4>Total: {{ number_format($transfer->total_quantity, 2) }}</h4></div>
    <hr><div class="table-responsive"><table class="table table-bordered"><thead class="theme-primary text-white"><tr><th>SL</th><th>Product</th><th>Unit</th><th class="text-right">Quantity</th></tr></thead><tbody>@foreach($transfer->items as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item->product?->product_name }}</td><td>{{ $item->product?->unit?->unit_name ?? '-' }}</td><td class="text-right">{{ number_format($item->quantity, 2) }}</td></tr>@endforeach</tbody></table></div>
    @if($transfer->notes)<p><strong>Notes:</strong> {{ $transfer->notes }}</p>@endif
</div></div></div>
@endsection
