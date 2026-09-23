@extends('layouts.dashboard')
@php
    $isEditing = isset($purchase);
@endphp
@section('title', $isEditing ? 'Edit Admin Purchase' : 'Admin Purchase')

@section('content')
    <style>
        .warehouse-summary {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 14px 16px;
            background: #fff;
        }

        .warehouse-summary .label {
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .warehouse-summary .value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .warehouse-purchase-table th,
        .warehouse-purchase-table td {
            vertical-align: middle !important;
        }

        .warehouse-qty-control {
            display: flex;
            align-items: stretch;
            justify-content: center;
            min-width: 138px;
        }

        .warehouse-qty-control .qty-input {
            width: 68px;
            min-width: 0;
            border-radius: 0;
            border-left: 0;
            border-right: 0;
            padding-left: 6px;
            padding-right: 6px;
        }

        .warehouse-qty-control .qty-button {
            width: 36px;
            padding: 6px;
            border-radius: 0;
        }

        .warehouse-qty-control .qty-minus { border-radius: 4px 0 0 4px; }
        .warehouse-qty-control .qty-plus { border-radius: 0 4px 4px 0; }

        .select2-container {
            width: 100% !important;
        }

        .payment-panel {
            border: 1px solid #e3e8ef;
            border-radius: 10px;
            background: #f8fafc;
            padding: 20px;
        }

        .payment-panel-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .payment-balance {
            border-left: 3px solid #2aa9b9;
            background: #fff;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .purchase-total-banner {
            border: 2px solid #2aa9b9;
            border-radius: 10px;
            background: #eefbfc;
            padding: 16px 20px;
        }

        .purchase-total-banner .amount {
            color: #137986;
            font-size: 28px;
            font-weight: 800;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card-box mt-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                    <div>
                        <h4 class="mb-1">{{ $isEditing ? 'Edit Admin Purchase' : 'New Admin Purchase' }}</h4>
                        <div class="text-muted">Purchase into central admin stock. Stock can then be transferred to any Area Office.</div>
                    </div>
                </div>

                <form method="POST" action="{{ $isEditing ? route('warehouse-purchases.update', $purchase) : route('warehouse-purchases.store') }}" id="warehousePurchaseForm"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id ?? null) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}{{ $supplier->mobile ? ' (' . $supplier->mobile . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control"
                                value="{{ old('purchase_date', $purchase->purchase_date ?? date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4 form-group">
                            <label>Notes</label>
                            <input type="text" name="notes" class="form-control" value="{{ old('notes', $purchase->notes ?? null) }}" placeholder="Optional note">
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="attachment">Attachment</label>
                            <input type="file" name="attachment" id="attachment" class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">PDF, JPG or PNG; maximum 5 MB.</small>
                            @if ($isEditing && $purchase->attachment)
                                <div class="mt-1">
                                    <label class="mb-0"><input type="checkbox" name="remove_attachment" value="1"> Remove current attachment</label>
                                </div>
                            @endif
                            @error('attachment')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered table-hover text-center warehouse-purchase-table">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th style="width: 28%;">Product</th>
                                    <th style="width: 12%;">Qty</th>
                                    <th style="width: 16%;">Purchase Price</th>
                                    <th style="width: 16%;">Sale Price</th>
                                    <th style="width: 14%;">Line Total</th>
                                    <th style="width: 14%;">Profit</th>
                                    <th style="width: 4%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-items-body"></tbody>
                        </table>
                    </div>

                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-outline-secondary" id="add-item-row">
                            <i class="fa fa-plus"></i> Add Row
                        </button>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="warehouse-summary">
                                <span class="label">Purchase Total</span>
                                <div class="value" id="summary-purchase-total">0.00</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="warehouse-summary">
                                <span class="label">Expected Sale Total</span>
                                <div class="value" id="summary-sale-total">0.00</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="warehouse-summary">
                                <span class="label">Expected Profit</span>
                                <div class="value" id="summary-profit-total">0.00</div>
                            </div>
                        </div>
                    </div>

                    <div class="purchase-total-banner mt-4 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <div class="font-weight-bold">Total Product Purchase Amount</div>
                            <small class="text-muted">The total value of all products in this purchase.</small>
                        </div>
                        <div class="amount"><span id="purchase-grand-total">0.00</span> BDT</div>
                    </div>

                    <div class="payment-panel mt-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <div>
                                <div class="payment-panel-title">Payment Details</div>
                                <small class="text-muted">Choose how this purchase is paid and record any outstanding balance.</small>
                            </div>
                            <span class="badge badge-light px-3 py-2">Purchase payment</span>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-lg-4 col-md-6 form-group">
                                <label for="pay_by">Payment Method</label>
                                <select name="pay_by" id="pay_by" class="form-control" required>
                                    <option value="">Select payment method</option>
                                    @foreach (['Cash', 'Mobile Banking', 'Card', 'Bank Account'] as $paymentMethod)
                                        <option value="{{ $paymentMethod }}" {{ old('pay_by', $purchase->pay_by ?? null) === $paymentMethod ? 'selected' : '' }}>
                                            {{ $paymentMethod }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 form-group d-none" id="account-field">
                                <label for="account_id">Payment Account</label>
                                <select name="account_id" id="account_id" class="form-control">
                                    <option value="">Select account</option>
                                </select>
                                <small class="text-muted" id="account-empty-message"></small>
                            </div>

                            <div class="col-lg-4 col-md-6 form-group">
                                <label for="paid_amount">Paid Amount</label>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control"
                                    value="{{ old('paid_amount', $purchase->paid_amount ?? 0) }}" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="payment-balance">
                                    <small class="text-muted d-block">Total Payable</small>
                                    <strong id="payment-total">0.00</strong>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="payment-balance">
                                    <small class="text-muted d-block">Due Amount</small>
                                    <strong id="payment-due">0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" id="save-purchase-button" class="btn btn-primary mb-2">
                            {{ $isEditing ? 'Update Purchase' : 'Save Purchase' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="purchase-row-template">
        <tr>
            <td class="text-left">
                <select class="form-control product-select" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-sale-price="{{ number_format((float) $product->selling_price, 2, '.', '') }}">
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger d-none duplicate-warning mt-1 d-block"></small>
            </td>
            <td>
                <div class="warehouse-qty-control">
                    <button type="button" class="btn btn-danger qty-button qty-minus" title="Decrease quantity">
                        <i class="fa fa-minus"></i>
                    </button>
                    <input type="number" step="1" min="1" class="form-control qty-input text-center" value="1" required>
                    <button type="button" class="btn btn-success qty-button qty-plus" title="Increase quantity">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </td>
            <td>
                <input type="number" step="0.000001" min="0" class="form-control purchase-price-input text-center" value="0" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control sale-price-input text-center" value="0" required>
            </td>
            <td>
                <input type="text" class="form-control line-total-input text-center" value="0.00" readonly>
            </td>
            <td>
                <input type="text" class="form-control line-profit-input text-center" value="0.00" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@section('script')
    @php
        $warehousePurchaseProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'selling_price' => (float) $product->selling_price,
            ];
        })->values();

        $warehousePaymentAccounts = $accounts->map(function ($account) {
            if ($account->account_type === 'Mobile Banking') {
                $label = trim($account->mobile_bank_name . ' - ' . $account->mobile_number, ' -');
            } elseif ($account->account_type === 'Card') {
                $label = trim($account->card_type . ' - ' . $account->card_number, ' -');
            } else {
                $label = trim(optional($account->bank)->bank_name . ' - ' . $account->bank_account_number, ' -');
            }

            return ['id' => $account->id, 'type' => $account->account_type, 'label' => $label];
        })->values();

        $warehousePurchaseInitialItems = old('items');
        if ($warehousePurchaseInitialItems === null) {
            $warehousePurchaseInitialItems = isset($purchase)
                ? $purchase->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'purchase_price' => $item->purchase_price,
                        'sale_price' => $item->sale_price,
                    ];
                })->values()->all()
                : [];
        }
    @endphp

    <script>
        const PRODUCTS = @json($warehousePurchaseProducts);
        const PAYMENT_ACCOUNTS = @json($warehousePaymentAccounts);

        const PRODUCT_MAP = PRODUCTS.reduce(function (carry, product) {
            carry[String(product.id)] = product;
            return carry;
        }, {});

        const tbody = document.getElementById('purchase-items-body');
        const template = document.getElementById('purchase-row-template');
        const addRowButton = document.getElementById('add-item-row');
        const summaryPurchaseTotal = document.getElementById('summary-purchase-total');
        const summarySaleTotal = document.getElementById('summary-sale-total');
        const summaryProfitTotal = document.getElementById('summary-profit-total');
        const purchaseGrandTotal = document.getElementById('purchase-grand-total');
        const paymentTotal = document.getElementById('payment-total');
        const paymentDue = document.getElementById('payment-due');
        const paidAmount = document.getElementById('paid_amount');
        const payBy = document.getElementById('pay_by');
        const accountField = document.getElementById('account-field');
        const accountSelect = document.getElementById('account_id');
        const accountEmptyMessage = document.getElementById('account-empty-message');
        const oldAccountId = @json(old('account_id', $purchase->account_id ?? null));
        const INITIAL_ITEMS = @json($warehousePurchaseInitialItems);

        function money(value) {
            const number = Number(value || 0);
            return number.toFixed(2);
        }

        function parseMoney(value) {
            const number = parseFloat(value);
            return Number.isNaN(number) ? 0 : number;
        }

        function initSelect($select, placeholder = 'Select Product') {
            if (typeof $.fn.select2 !== 'undefined') {
                $select.select2({
                    width: '100%',
                    placeholder: placeholder,
                    allowClear: true,
                });
            }
        }

        function syncNames() {
            tbody.querySelectorAll('tr').forEach(function (row, index) {
                row.querySelector('.product-select').name = `items[${index}][product_id]`;
                row.querySelector('.qty-input').name = `items[${index}][quantity]`;
                row.querySelector('.purchase-price-input').name = `items[${index}][purchase_price]`;
                row.querySelector('.sale-price-input').name = `items[${index}][sale_price]`;
            });
        }

        function getSelectedProductIds(exceptRow = null) {
            return Array.from(tbody.querySelectorAll('.product-select'))
                .filter(function (select) {
                    return !exceptRow || !exceptRow.contains(select);
                })
                .map(function (select) {
                    return String(select.value || '');
                })
                .filter(function (value) {
                    return value !== '';
                });
        }

        function setDuplicateWarning(row, message) {
            const warning = row.querySelector('.duplicate-warning');
            if (!warning) {
                return;
            }

            warning.textContent = message || '';
            warning.classList.toggle('d-none', !message);
        }

        function updateSummary() {
            let purchaseTotal = 0;
            let saleTotal = 0;
            let profitTotal = 0;

            tbody.querySelectorAll('tr').forEach(function (row) {
                const qty = parseMoney(row.querySelector('.qty-input').value);
                const purchasePrice = parseMoney(row.querySelector('.purchase-price-input').value);
                const salePrice = parseMoney(row.querySelector('.sale-price-input').value);
                const linePurchase = qty * purchasePrice;
                const lineSale = qty * salePrice;
                const lineProfit = lineSale - linePurchase;

                row.querySelector('.line-total-input').value = money(linePurchase);
                row.querySelector('.line-profit-input').value = money(lineProfit);

                purchaseTotal += linePurchase;
                saleTotal += lineSale;
                profitTotal += lineProfit;
            });

            summaryPurchaseTotal.textContent = money(purchaseTotal);
            summarySaleTotal.textContent = money(saleTotal);
            summaryProfitTotal.textContent = money(profitTotal);
            purchaseGrandTotal.textContent = money(purchaseTotal);
            paymentTotal.textContent = money(purchaseTotal);
            const paid = parseMoney(paidAmount.value);
            paymentDue.textContent = money(Math.max(purchaseTotal - paid, 0));
        }

        function updatePaymentAccounts() {
            const method = payBy.value;
            const requiresAccount = method && method !== 'Cash';
            accountField.classList.toggle('d-none', !requiresAccount);
            accountSelect.required = Boolean(requiresAccount);
            accountSelect.innerHTML = '<option value="">Select account</option>';

            if (!requiresAccount) {
                accountEmptyMessage.textContent = '';
                return;
            }

            const matchingAccounts = PAYMENT_ACCOUNTS.filter(account => account.type === method);
            matchingAccounts.forEach(function (account) {
                const option = new Option(account.label, account.id, false, String(account.id) === String(oldAccountId));
                accountSelect.add(option);
            });
            accountEmptyMessage.textContent = matchingAccounts.length ? '' : `No ${method} account is configured.`;
        }

        function bindRowEvents(row) {
            const productSelect = row.querySelector('.product-select');
            const qtyInput = row.querySelector('.qty-input');
            const purchasePriceInput = row.querySelector('.purchase-price-input');
            const salePriceInput = row.querySelector('.sale-price-input');
            const removeButton = row.querySelector('.remove-row');
            const quantityMinus = row.querySelector('.qty-minus');
            const quantityPlus = row.querySelector('.qty-plus');

            initSelect($(productSelect));

            $(productSelect).on('change', function () {
                setDuplicateWarning(row, '');

                const selectedProductId = String(this.value || '');
                if (selectedProductId) {
                    const duplicateExists = getSelectedProductIds(row).includes(selectedProductId);
                    if (duplicateExists) {
                        this.value = '';
                        if (typeof $.fn.select2 !== 'undefined') {
                            $(this).val('').trigger('change.select2');
                        }
                        salePriceInput.value = money(0);
                        setDuplicateWarning(row, 'This item is already selected.');
                        updateSummary();
                        return;
                    }
                }

                const product = PRODUCT_MAP[String(this.value)];
                if (product) {
                    salePriceInput.value = money(product.selling_price);
                } else {
                    salePriceInput.value = money(0);
                }
                updateSummary();
            });

            qtyInput.addEventListener('input', updateSummary);
            qtyInput.addEventListener('change', function () {
                qtyInput.value = Math.max(1, Math.floor(parseMoney(qtyInput.value)) || 1);
                updateSummary();
            });
            quantityMinus.addEventListener('click', function () {
                qtyInput.value = Math.max(1, Math.floor(parseMoney(qtyInput.value)) - 1);
                updateSummary();
            });
            quantityPlus.addEventListener('click', function () {
                qtyInput.value = Math.max(1, Math.floor(parseMoney(qtyInput.value)) + 1);
                updateSummary();
            });
            purchasePriceInput.addEventListener('input', updateSummary);
            salePriceInput.addEventListener('input', updateSummary);

            removeButton.addEventListener('click', function () {
                if (tbody.querySelectorAll('tr').length > 1) {
                    row.remove();
                    syncNames();
                    updateSummary();
                    return;
                }

                row.querySelector('.product-select').value = '';
                if (typeof $.fn.select2 !== 'undefined') {
                    $(row.querySelector('.product-select')).val('').trigger('change');
                }
                setDuplicateWarning(row, '');
                qtyInput.value = 1;
                purchasePriceInput.value = 0;
                salePriceInput.value = 0;
                updateSummary();
            });
        }

        function addRow(item = null) {
            const row = template.content.firstElementChild.cloneNode(true);
            tbody.appendChild(row);
            bindRowEvents(row);
            if (item) {
                const productSelect = row.querySelector('.product-select');
                productSelect.value = item.product_id;
                if (typeof $.fn.select2 !== 'undefined') {
                    $(productSelect).val(String(item.product_id)).trigger('change.select2');
                }
                row.querySelector('.qty-input').value = item.quantity;
                row.querySelector('.purchase-price-input').value = item.purchase_price;
                row.querySelector('.sale-price-input').value = item.sale_price;
            }
            syncNames();
            updateSummary();
        }

        addRowButton.addEventListener('click', function () {
            addRow();
        });
        paidAmount.addEventListener('input', updateSummary);
        payBy.addEventListener('change', updatePaymentAccounts);

        initSelect($('#supplier_id'), 'Search and select supplier');
        updatePaymentAccounts();

        document.getElementById('warehousePurchaseForm')?.addEventListener('submit', function (event) {
            if (!this.checkValidity()) return;

            const button = document.getElementById('save-purchase-button');
            if (!button || button.disabled) {
                event.preventDefault();
                return;
            }

            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Saving...';
            button.setAttribute('aria-busy', 'true');
        });

        if (INITIAL_ITEMS.length) {
            INITIAL_ITEMS.forEach(addRow);
        } else {
            addRow();
        }
    </script>
@endsection
