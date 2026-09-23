@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <h4 class="m-t-0 header-title mb-2 text-center"><b>{{ __('Stock Report') }}</b></h4>

                {{-- ===== Filters (GET) ===== --}}
                <div class="no-print">
                    <form method="GET" action="{{ route('report.stock') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label d-block">Product name</label>
                            <input
                                type="text"
                                name="product_name"
                                value="{{ request('product_name') }}"
                                class="form-control"
                                placeholder="Search product name..."
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">From</label>
                            <input
                                type="date"
                                name="from"
                                value="{{ request('from') }}"
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">To</label>
                            <input
                                type="date"
                                name="to"
                                value="{{ request('to') }}"
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('report.stock') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </form>

                    <div class="d-flex gap-2 mb-3">
                        <button onclick="window.print()" class="btn btn-primary">🖨 Print</button>

                        {{-- Preserve filters on Export --}}
                        <a
                            href="{{ route('report.stock.export', request()->only(['product_name','from','to','created_by'])) }}"
                            class="btn btn-success"
                        >⬇ Export Excel</a>
                    </div>
                </div>

                {{-- ===== Table ===== --}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>{{ __('page.stock')[1] }}</th>
                            <th>{{ __('page.stock')[2] }}</th>
                            <th>{{ __('page.stock')[4] }}</th>
                            <th>{{ __('page.stock')[5] }}</th>
                            <th>{{ __('page.stock')[6] }}</th>
                            <th>{{ __('page.stock')[7] }}</th>
                            <th>{{ __('page.stock')[8] }}</th>
                            <th>{{ __('page.stock')[9] }}</th>
                            <th>{{ __('page.stock')[10] }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $stock_sale_price = 0;
                            $stock_purchase_price = 0;
                        @endphp

                        @forelse ($products as $product)
                            @php
                                $total_stock_in = round(
                                    ($product->purchase_qty ?? 0)
                                    + ($product->sale_return_qty ?? 0)
                                    + ($product->transferred_in_qty ?? 0),
                                    2
                                );

                                $total_stock_out = round(
                                    ($product->sale_qty ?? 0)
                                    + ($product->purchase_return_qty ?? 0)
                                    + ($product->transferred_out_qty ?? 0),
                                    2
                                );

                                $stock_qty = $total_stock_in - $total_stock_out;

                                $sale_price = $stock_qty * (float) ($product->selling_price ?? 0);
                                $purchase_price = $stock_qty * (float) ($product->purchase_price ?? 0);

                                $stock_sale_price += $sale_price;
                                $stock_purchase_price += $purchase_price;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ number_format((float) ($product->purchase_price ?? 0), 2) }}</td>
                                <td>{{ number_format((float) ($product->selling_price ?? 0), 2) }}</td>
                                <td>{{ number_format((float) $total_stock_in, 2) }}</td>
                                <td>{{ number_format((float) $total_stock_out, 2) }}</td>
                                <td>{{ number_format((float) $stock_qty, 2) }}</td>
                                <td>{{ number_format((float) $sale_price, 2) }}</td>
                                <td>{{ number_format((float) $purchase_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No products found for the selected filters.</td>
                            </tr>
                        @endforelse

                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                            <td colspan="7">{{ __('Total') }}</td>
                            <td>{{ number_format((float) $stock_sale_price, 2) }}</td>
                            <td>{{ number_format((float) $stock_purchase_price, 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('css')
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0 !important; padding: 0 !important; }
            .card-box { margin-bottom: 0 !important; padding-bottom: 0 !important; }
        }
    </style>
@endpush
