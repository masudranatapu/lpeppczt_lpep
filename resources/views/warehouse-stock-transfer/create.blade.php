@extends('layouts.dashboard')
@section('title', 'New Area Office Stock Transfer')

@section('content')
<style>
    .warehouse-transfer-table th, .warehouse-transfer-table td { vertical-align: middle !important; }
    .warehouse-qty-control { display: flex; align-items: stretch; justify-content: center; min-width: 138px; }
    .warehouse-qty-control .qty-input { width: 68px; min-width: 0; border-radius: 0; border-left: 0; border-right: 0; padding-left: 6px; padding-right: 6px; }
    .warehouse-qty-control .qty-button { width: 36px; padding: 6px; border-radius: 0; }
    .warehouse-qty-control .qty-minus { border-radius: 4px 0 0 4px; }
    .warehouse-qty-control .qty-plus { border-radius: 0 4px 4px 0; }
    .select2-container { width: 100% !important; }
    .transfer-summary { border: 1px solid #e9ecef; border-radius: 6px; padding: 12px 16px; background: #f8fafc; }
    .transfer-summary small { display: block; color: #6c757d; text-transform: uppercase; }
    .transfer-summary strong { font-size: 20px; color: #1f2937; }
</style>

<div class="row"><div class="col-12"><div class="card-box mt-4">
    <div class="mb-3"><h4 class="mb-1">Transfer Admin Stock to Area Office</h4><p class="text-muted mb-0">Search products and use the plus/minus controls to set each transfer quantity.</p></div>
    <form method="POST" action="{{ route('warehouse-stock-transfers.store') }}" id="warehouseStockTransferForm">
        @csrf
        <div class="row">
            <div class="col-md-4 form-group"><label>Area Office</label><select name="warehouse_id" id="warehouse_id" class="form-control" required><option value="">Select Area Office</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" {{ old('warehouse_id', $selectedWarehouseId) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }} ({{ $warehouse->code }})</option>@endforeach</select></div>
            <div class="col-md-4 form-group"><label>Transfer Date</label><input type="date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" class="form-control" required></div>
            <div class="col-md-4 form-group"><label>Notes</label><input name="notes" value="{{ old('notes') }}" class="form-control"></div>
        </div>
        @include('warehouse-stock-transfer.items-form', ['buttonLabel' => 'Save Transfer'])
    </form>
</div></div></div>
@endsection

@section('script')
<script>
(function () {
    const body = document.getElementById('inventory-items');
    const template = document.getElementById('inventory-row');
    const totalQuantity = document.getElementById('transfer-total-quantity');

    function number(value) { const parsed = parseFloat(value); return Number.isNaN(parsed) ? 0 : parsed; }
    function initSelect(select) {
        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $(select).select2({ width: '100%', placeholder: 'Search and select product', allowClear: true });
        }
    }
    function syncNames() {
        body.querySelectorAll('tr').forEach(function (row, index) {
            row.querySelector('.product-select').name = `items[${index}][product_id]`;
            row.querySelector('.qty-input').name = `items[${index}][quantity]`;
        });
    }
    function updateTotal() {
        let total = 0;
        body.querySelectorAll('.qty-input').forEach(function (input) { total += number(input.value); });
        totalQuantity.textContent = total.toFixed(2);
    }
    function normalize(row) {
        const input = row.querySelector('.qty-input');
        const available = number(row.querySelector('.product-select').selectedOptions[0]?.dataset.available);
        let quantity = Math.max(1, Math.floor(number(input.value)) || 1);
        if (available > 0) quantity = Math.min(quantity, Math.floor(available));
        input.value = quantity;
        updateTotal();
    }
    function bindRow(row) {
        const product = row.querySelector('.product-select');
        const quantity = row.querySelector('.qty-input');
        initSelect(product);
        $(product).on('change', function () {
            const available = number(this.selectedOptions[0]?.dataset.available);
            row.querySelector('.available-quantity').textContent = available.toFixed(2);
            quantity.max = Math.floor(available);
            if (this.value && number(quantity.value) > available) quantity.value = Math.max(1, Math.floor(available));
            updateTotal();
        });
        row.querySelector('.qty-minus').addEventListener('click', function () {
            quantity.value = Math.max(1, Math.floor(number(quantity.value)) - 1);
            updateTotal();
        });
        row.querySelector('.qty-plus').addEventListener('click', function () {
            const available = number(product.selectedOptions[0]?.dataset.available);
            const next = Math.floor(number(quantity.value)) + 1;
            quantity.value = available > 0 ? Math.min(next, Math.floor(available)) : next;
            updateTotal();
        });
        quantity.addEventListener('input', updateTotal);
        quantity.addEventListener('change', function () { normalize(row); });
        row.querySelector('.remove-row').addEventListener('click', function () {
            if (body.querySelectorAll('tr').length > 1) {
                if (typeof $ !== 'undefined' && $(product).data('select2')) $(product).select2('destroy');
                row.remove(); syncNames(); updateTotal();
            }
        });
    }
    function addRow() {
        const row = template.content.firstElementChild.cloneNode(true);
        body.appendChild(row); bindRow(row); syncNames(); updateTotal();
    }
    document.getElementById('add-inventory-item').addEventListener('click', addRow);
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') $('#warehouse_id').select2({ width: '100%', placeholder: 'Search and select Area Office', allowClear: true });
    addRow();
})();
</script>
@endsection
