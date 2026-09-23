@extends('layouts.dashboard')
@section('title', 'Admin Stock Summary')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Admin Stock Summary</h4>
                <div class="text-muted">Central stock purchases, availability, and Area Office transfers.</div>
            </div>
            <div class="mt-2 mt-md-0 admin-stock-page-actions">
                <a href="{{ route('warehouse-stock-transfers.index') }}" class="btn btn-outline-dark mr-1 shadow-sm">
                    <i class="fa fa-history mr-1"></i> Transfer History
                </a>
                <a href="{{ route('warehouse-stock-transfers.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa fa-plus mr-1"></i> New Transfer
                </a>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card-box table-responsive admin-stock-report-card">
            <form method="GET" action="{{ route('admin-stock-summary.index') }}" class="admin-stock-search-form mb-3">
                <div class="admin-stock-show-field form-group mb-0">
                    <label for="admin-stock-show" class="mb-1">Show</label>
                    <select id="admin-stock-show" class="form-control" disabled><option>All</option></select>
                </div>
                <div class="admin-stock-product-field form-group mb-0">
                    <label for="admin-stock-product-name" class="mb-1">Product Name</label>
                    <input type="text" id="admin-stock-product-name" name="product_name" value="{{ $productName }}" class="form-control" placeholder="Search by product name">
                </div>
                <div class="admin-stock-status-field form-group mb-0">
                    <label for="admin-stock-status" class="mb-1">Stock Status</label>
                    <select id="admin-stock-status" name="stock_status" class="form-control">
                        <option value="all" {{ $stockStatus === 'all' ? 'selected' : '' }}>All</option>
                        <option value="in" {{ $stockStatus === 'in' ? 'selected' : '' }}>Stock In</option>
                        <option value="low" {{ $stockStatus === 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ $stockStatus === 'out' ? 'selected' : '' }}>Stock Out</option>
                    </select>
                </div>
                <x-loading-submit type="submit" class="btn btn-primary" loading-text="Searching...">Search</x-loading-submit>
                <a href="{{ route('admin-stock-summary.index') }}" class="btn btn-outline-secondary">Reset</a>
            </form>

            <div class="table-responsive" id="tablefixed">
            <table class="table table-hover mt-0 mb-0 admin-stock-report-table">
                <thead>
                    <tr>
                        <th style="width: 7%">SL</th>
                        <th style="width: 33%">Product</th>
                        <th style="width: 15%" class="text-right">Purchased Qty</th>
                        <th style="width: 15%" class="text-right">Transferred Qty</th>
                        <th style="width: 15%" class="text-right">Available Qty</th>
                        <th style="width: 15%">Status</th>
                    </tr>
                    @if($adminStock->isNotEmpty())
                        <tr class="admin-stock-total-row">
                            <th colspan="2" class="text-left">Total</th>
                            <th class="text-right">{{ number_format($totals->purchased, 2) }}</th>
                            <th class="text-right">{{ number_format($totals->transferred, 2) }}</th>
                            <th class="text-right">{{ number_format($totals->available, 2) }}</th>
                            <th></th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($adminStock as $product)
                        @php($available = (float) $product->available_quantity)
                        @php($hasStock = $available > 0)
                        @php($isLowStock = $available > 0 && $available <= 10)
                        <tr class="{{ $hasStock ? 'admin-stock-row-in' : 'admin-stock-row-out' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img class="admin-stock-product-image mr-2" src="{{ checkImage($product->image) }}" alt="{{ $product->product_name }}">
                                    <strong>{{ $product->product_name }}</strong>
                                </div>
                            </td>
                            <td class="text-right"><span class="admin-qty-pill admin-qty-in">{{ number_format($product->purchased_quantity, 2) }}</span></td>
                            <td class="text-right"><span class="admin-qty-pill admin-qty-out">{{ number_format($product->transferred_quantity, 2) }}</span></td>
                            <td class="text-right"><span class="admin-stock-pill {{ $hasStock ? 'admin-stock-pill-in' : 'admin-stock-pill-out' }}">{{ number_format($product->available_quantity, 2) }}</span></td>
                            <td><span class="badge {{ !$hasStock ? 'badge-danger' : ($isLowStock ? 'badge-warning' : 'badge-success') }}">{{ !$hasStock ? 'Stock Out' : ($isLowStock ? 'Low Stock' : 'Stock In') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No admin stock has been purchased yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<style>
    .admin-stock-report-card { border-top: 3px solid #157c34; background: linear-gradient(180deg, #fff 0%, #fbfffd 100%); }
    .admin-stock-search-form { display: flex; align-items: end; gap: 14px; padding: 14px 16px; border: 1px solid #e6eef3; border-radius: 10px; background: #f8fafc; }
    .admin-stock-show-field { width: 110px; }
    .admin-stock-product-field { width: min(360px, 100%); }
    .admin-stock-status-field { width: 180px; }
    .admin-stock-search-form label { color: #475569; font-size: 12px; font-weight: 700; }
    @media (max-width: 575px) { .admin-stock-search-form { flex-wrap: wrap; } .admin-stock-show-field, .admin-stock-product-field, .admin-stock-status-field { width: 100%; } }
    .admin-stock-report-card .btn { border-radius: 6px; font-weight: 600; box-shadow: 0 2px 5px rgba(15,23,42,.08); }
    .admin-stock-legend { display: flex; flex-wrap: wrap; gap: 14px; color: #64748b; font-size: 12px; font-weight: 700; }
    .legend-dot { display: inline-block; width: 9px; height: 9px; margin-right: 5px; border-radius: 50%; }
    .legend-in { background: #16a34a; }
    .legend-out { background: #dc2626; }
    .admin-stock-report-table th, .admin-stock-report-table td { vertical-align: middle; border: 1px solid #d8e2ea !important; }
    .admin-stock-report-table thead tr:first-child th { position: sticky; top: 0; z-index: 2; padding: 12px; border-color: #0f766e !important; background: #0f766e; color: #fff; font-size: 12px; letter-spacing: .02em; text-transform: uppercase; }
    .admin-stock-total-row th { padding: 10px 12px !important; background: #eaf8f6 !important; color: #0f766e !important; border-color: #d8e2ea !important; font-weight: 800; text-transform: none !important; }
    .admin-stock-report-table tbody tr { transition: background-color .15s ease, transform .15s ease; }
    .admin-stock-report-table tbody tr:hover { background: #f6fbff; }
    .admin-stock-row-in { background: rgba(22,163,74,.04); }
    .admin-stock-row-out { background: rgba(220,38,38,.05); }
    .admin-stock-product-image { width: 42px; height: 42px; flex: 0 0 42px; object-fit: cover; border: 1px solid #e5edf3; border-radius: 10px; background: #fff; }
    .admin-qty-pill, .admin-stock-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 72px; padding: 6px 10px; border-radius: 999px; font-weight: 700; line-height: 1; }
    .admin-qty-in, .admin-stock-pill-in { background: #e9f8ef; color: #12824a; }
    .admin-qty-out { background: #fff6dd; color: #9a6700; }
    .admin-stock-pill-out { background: #fff0f0; color: #c61b1b; }
    @media (max-width: 767.98px) { .admin-stock-report-header { align-items: flex-start; flex-direction: column; } }
</style>
@endpush
