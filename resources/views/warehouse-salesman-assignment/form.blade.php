@php
    $assignedItems = old('items');
    if ($assignedItems === null && !empty($assignment?->items)) {
        $assignedItems = $assignment->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
        ])->values()->all();
    }
    $assignedItems = $assignedItems ?: [['product_id' => '', 'quantity' => '']];
@endphp

<div class="row">
    <div class="col-md-4 form-group">
        <label>Area Office</label>
        <input class="form-control" value="{{ $warehouse->name }} ({{ $warehouse->code }})" readonly>
        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
    </div>
    <div class="col-md-4 form-group">
        <label>LSP</label>
        <select name="warehouse_salesman_id" class="form-control" required>
            <option value="">Select salesman</option>
            @foreach($salesmen as $salesman)
                <option value="{{ $salesman->id }}" @selected((string) old('warehouse_salesman_id', $assignment->warehouse_salesman_id ?? '') === (string) $salesman->id)>{{ $salesman->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label>Assignment Date</label>
        <input type="date" name="assignment_date" value="{{ old('assignment_date', $assignment->assignment_date ?? date('Y-m-d')) }}" class="form-control" required>
    </div>
</div>

<div class="form-group">
    <label>Notes</label>
    <input name="notes" value="{{ old('notes', $assignment->notes ?? '') }}" class="form-control">
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Area Office Unassigned</th>
                <th>Assign Quantity</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="assignment-items"></tbody>
    </table>
</div>

<button type="button" class="btn btn-outline-secondary" id="add-assignment-item">Add Product</button>
<button class="btn btn-primary float-right" type="submit">{{ empty($assignment) ? 'Save Assignment' : 'Update Assignment' }}</button>

<template id="assignment-row">
    <tr>
        <td>
            <select class="form-control product" required>
                <option value="">Select product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-available="{{ $product->available_quantity }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="available text-right">0.00</td>
        <td><input type="number" class="form-control quantity" min="0.01" step="0.01" required></td>
        <td><button type="button" class="btn btn-danger btn-sm remove">x</button></td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var body = document.getElementById('assignment-items');
    var tpl = document.getElementById('assignment-row');
    var items = @json($assignedItems);

    function names() {
        body.querySelectorAll('tr').forEach(function (row, index) {
            row.querySelector('.product').name = 'items[' + index + '][product_id]';
            row.querySelector('.quantity').name = 'items[' + index + '][quantity]';
        });
    }

    function bindRow(row) {
        var product = row.querySelector('.product');
        var quantity = row.querySelector('.quantity');

        product.addEventListener('change', function () {
            var available = Number(product.options[product.selectedIndex].dataset.available || 0);
            row.querySelector('.available').textContent = available.toFixed(2);
            quantity.max = available;
        });

        row.querySelector('.remove').addEventListener('click', function () {
            if (body.children.length > 1) {
                row.remove();
                names();
            }
        });

        if (product.value) {
            product.dispatchEvent(new Event('change'));
        }
    }

    function add(productId, quantityValue) {
        var row = tpl.content.firstElementChild.cloneNode(true);
        body.appendChild(row);
        row.querySelector('.product').value = productId || '';
        row.querySelector('.quantity').value = quantityValue || '';
        bindRow(row);
        names();
    }

    document.getElementById('add-assignment-item').addEventListener('click', function () {
        add();
    });

    body.innerHTML = '';
    items.forEach(function (item) {
        add(item.product_id, item.quantity);
    });

    if (!body.children.length) {
        add();
    }
});
</script>
