<div class="table-responsive mt-2">
    <table class="table table-bordered table-hover text-center warehouse-transfer-table">
        <thead class="theme-primary text-white"><tr><th style="width:42%">Product</th><th style="width:18%">Admin Available</th><th style="width:28%">Qty</th><th style="width:8%">Action</th></tr></thead>
        <tbody id="inventory-items"></tbody>
    </table>
</div>
<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
    <button type="button" class="btn btn-outline-secondary mb-2" id="add-inventory-item"><i class="fa fa-plus"></i> Add Row</button>
    <div class="transfer-summary mb-2"><small>Total Transfer Quantity</small><strong id="transfer-total-quantity">0.00</strong></div>
</div>
<div class="text-right mt-3"><button class="btn btn-primary" type="submit">{{ $buttonLabel }}</button></div>

<template id="inventory-row">
    <tr>
        <td class="text-left"><select class="form-control product-select" required><option value="">Select Product</option>@foreach($products as $product)<option value="{{ $product->id }}" data-available="{{ $product->available_quantity }}">{{ $product->product_name }}</option>@endforeach</select></td>
        <td><strong class="available-quantity">0.00</strong></td>
        <td><div class="warehouse-qty-control"><button type="button" class="btn btn-danger qty-button qty-minus" title="Decrease quantity"><i class="fa fa-minus"></i></button><input type="number" step="1" min="1" value="1" class="form-control qty-input text-center" required><button type="button" class="btn btn-success qty-button qty-plus" title="Increase quantity"><i class="fa fa-plus"></i></button></div></td>
        <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
    </tr>
</template>
