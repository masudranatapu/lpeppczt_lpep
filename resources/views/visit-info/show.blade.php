@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title">Visit Details</h3>
            <div class="card-tools">
                <a href="{{ route('visit-info.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('visit-info.edit', $visitInfo) }}" class="btn btn-primary ms-2">
                    <i class="fas fa-edit"></i> Edit Visit
                </a>
                <form action="{{ route('visit-info.destroy', $visitInfo) }}" method="POST" class="d-inline ms-2"
                      onsubmit="return confirm('Are you sure you want to delete this visit?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Visit
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            @php
                // Derive totals & safe fallbacks
                $totalFee = optional($visitInfo->fees)->sum('amount') ?? 0;
                $agentName = optional($visitInfo->agent)->name ?? 'N/A';
                $agentMobile = optional($visitInfo->agent)->mobile
                    ?? optional($visitInfo->agent)->phone
                    ?? optional($visitInfo->agent)->mobile_number
                    ?? optional(optional($visitInfo->agent)->user)->phone
                    ?? 'N/A';
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%">Beneficiary Name</th>
                            <td>{{ $visitInfo->appCustomer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Beneficiary Cell Number</th>
                            <td>
                                @if($visitInfo->appCustomer->mobile !== 'N/A')
                                    <a href="tel:{{ preg_replace('/\s+/', '', $visitInfo->appCustomer->mobile) }}">{{ $visitInfo->appCustomer->mobile }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Beneficiary Group Number</th>
                            <td>{{ $visitInfo->customer_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Visit Date</th>
                            <td>{{ $visitInfo->visit_date ? $visitInfo->visit_date->format('d-m-Y') . ' ' . ($visitInfo->created_at?->format('h:i A') ?? '') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Agent Name</th>
                            <td>{{ $agentName }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>
                                {{ $visitInfo->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>Agent Mobile</th>
                            <td>
                                @if($agentMobile !== 'N/A')
                                    <a href="tel:{{ preg_replace('/\s+/', '', $agentMobile) }}">{{ $agentMobile }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>{{ number_format($totalFee) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    {{-- Memo Numbers --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Memo/Invoice Numbers</h4>
                        </div>
                        <div class="card-body">
                            @if(is_array($visitInfo->memo_no) && count($visitInfo->memo_no) > 0)
                                <ul class="list-group">
                                    @foreach($visitInfo->memo_no as $memo)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $memo }}</span>
                                            <span class="badge bg-light text-muted">Memo</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">No memo/invoice numbers recorded.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Fee Breakdown (from visit_fees) --}}
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Fee Breakdown</h4>
                            <span class="badge bg-primary">{{ number_format($totalFee) }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if($visitInfo->fees && $visitInfo->fees->count() > 0)
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 65%">Fee Type</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitInfo->fees as $fee)
                                            <tr>
                                                <td>{{ $fee->fee_type }}</td>
                                                <td class="text-end">{{ number_format($fee->amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">{{ number_format($totalFee) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <div class="p-3">
                                    <p class="text-muted mb-0">No fees recorded.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Optional: raw JSON debug (hide in prod) --}}
            {{-- <pre class="mt-3">{{ json_encode($visitInfo->toArray(), JSON_PRETTY_PRINT) }}</pre> --}}
        </div>
    </div>
</div>
@endsection
