@extends('layouts.dashboard')
@section('title', 'Admin Purchases')

@section('content')
    <style>
        .due-payment-modal .modal-content {
            border: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .due-payment-modal .modal-header {
            background: #172033;
            color: #fff;
            border: 0;
        }

        .due-payment-modal .modal-header .close {
            color: #fff;
            opacity: .8;
        }

        .due-balance-card {
            background: #f4f8fb;
            border-left: 4px solid #2aa9b9;
            border-radius: 6px;
            padding: 12px 15px;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive my-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Admin Purchases (Central Stock)</b></h4>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('warehouse-purchases.create') }}" class="btn btn-primary waves-effect waves-light m-b-5">
                            <i class="fa fa-plus-square m-r-5"></i>
                            <span>New Purchase</span>
                        </a>
                    </div>
                </div>

                <div>
                    <form method="GET" action="" id="searching">
                        <div class="row justify-content-end g-2 align-items-end">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="filter_type">Filter Type</label>
                                    <select name="filter_type" id="filter_type" class="form-control select2">
                                        <option value="all" @selected(request('filter_type', 'all') === 'all')>Life Time</option>
                                        <option value="month_year" @selected(request('filter_type') === 'month_year')>Month &amp; Year</option>
                                        <option value="year" @selected(request('filter_type') === 'year')>Year Only</option>
                                        <option value="custom" @selected(request('filter_type') === 'custom')>Custom Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="per_page">Per page</label>
                                    <select name="per_page" id="per_page" class="form-control">
                                        <option value="all" @selected(request('per_page', 'all') === 'all')>All</option>
                                        <option value="10" @selected(request('per_page') === '10')>10</option>
                                        <option value="20" @selected(request('per_page') === '20')>20</option>
                                        <option value="30" @selected(request('per_page') === '30')>30</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 year-filter-field" style="{{ in_array(request('filter_type'), ['month_year', 'year']) ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <select name="year" id="year" class="form-control" {{ request('filter_type') === 'month_year' || request('filter_type') === 'year' ? '' : 'disabled' }}>
                                        <option value="">Year</option>
                                        @foreach (range(now()->year, now()->year - 10) as $yearOption)
                                            <option value="{{ $yearOption }}" @selected(request('year') == $yearOption)>{{ $yearOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 month-filter-field" style="{{ request('filter_type') === 'month_year' ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control" {{ request('filter_type') === 'month_year' ? '' : 'disabled' }}>
                                        <option value="">Month</option>
                                        @foreach (range(1, 12) as $monthOption)
                                            <option value="{{ $monthOption }}" @selected(request('month') == $monthOption)>{{ date('F', mktime(0, 0, 0, $monthOption, 1)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 custom-filter-field" style="{{ request('filter_type') === 'custom' ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request('from_date') ?? '' }}" {{ request('filter_type') === 'custom' ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-2 custom-filter-field" style="{{ request('filter_type') === 'custom' ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        value="{{ request('to_date') ?? '' }}" {{ request('filter_type') === 'custom' ? '' : 'disabled' }}>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">&nbsp;</label>
                                    <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                        class="form-control" placeholder="Supplier name, product name or invoice">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button class="btn btn-success btn-no-border w-100" type="submit">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-rep-plugin">
                    <div class="table-responsive" id="tablefixed">
                        <table class="table table-bordered table-hover mt-0" style="margin-bottom: 70px">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th style="width: 5%">SL</th>
                                    <th style="width: 10%">Purchase Date</th>
                                    <th style="width: 18%">Product Name</th>
                                    <th style="width: 12%">Invoice No</th>
                                    <th style="width: 15%">Stock Location</th>
                                    <th style="width: 12%">Total Amount</th>
                                    <th style="width: 10%">Paid Amount</th>
                                    <th style="width: 10%">Due</th>
                                    <th style="width: 8%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchases->firstItem() + $loop->index }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->purchase_date }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">
                                            @foreach ($purchase->items as $item)
                                                <span class="d-inline-block mr-1 mb-1">{{ $item->product?->product_name }}@if (! $loop->last), @endif</span>
                                            @endforeach
                                        </td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->invoice_no }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ $purchase->warehouse?->name ?: 'Central Admin Stock' }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ number_format($purchase->total_amount, 2) }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ number_format($purchase->paid_amount, 2) }}</td>
                                        <td onclick="viewPurchase({{ $purchase->id }})">{{ number_format($purchase->due_amount, 2) }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <button type="button" class="btn btn-info dropdown-toggle waves-effect btn-sm"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    Action <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start"
                                                    style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a href="{{ route('warehouse-purchases.show', $purchase) }}" class="dropdown-item">View</a>
                                                    <a href="{{ route('warehouse-purchases.invoice', $purchase) }}" class="dropdown-item" target="_blank">Invoice</a>
                                                    @if (! $purchase->isLegacyWarehouseReceipt())
                                                        <a href="{{ route('warehouse-purchases.edit', $purchase) }}" class="dropdown-item">Edit</a>
                                                    @endif
                                                    @if ($purchase->attachment)
                                                        <a href="{{ asset($purchase->attachment) }}" class="dropdown-item" target="_blank" rel="noopener">Attachment</a>
                                                    @endif
                                                    @if ((float) $purchase->due_amount > 0)
                                                        <a href="javascript:void(0);" class="dropdown-item due-payment-button"
                                                            data-toggle="modal" data-target="#duePaymentModal"
                                                            data-purchase-id="{{ $purchase->id }}"
                                                            data-invoice="{{ $purchase->invoice_no }}"
                                                            data-due="{{ number_format($purchase->due_amount, 2, '.', '') }}"
                                                            data-action="{{ route('warehouse-purchases.payments.store', $purchase) }}">
                                                            Pay Due
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('warehouse-purchases.destroy', $purchase) }}" method="POST"
                                                        onsubmit="return confirm('Delete this admin purchase? This is blocked if any quantity has already been transferred.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No purchase found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><strong>Total</strong></td>
                                    <td><strong>{{ number_format($purchases->sum('total_amount'), 2) }}</strong></td>
                                    <td><strong>{{ number_format($purchases->sum('paid_amount'), 2) }}</strong></td>
                                    <td><strong>{{ number_format($purchases->sum('due_amount'), 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade due-payment-modal" id="duePaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" id="duePaymentForm">
                    @csrf
                    <input type="hidden" name="warehouse_purchase_id" id="due_purchase_id" value="{{ old('warehouse_purchase_id') }}">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title text-white mb-1">Record Due Payment</h5>
                            <small class="text-white-50" id="due_invoice_label"></small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="due-balance-card mb-3">
                            <small class="text-muted d-block">Outstanding Due</small>
                            <strong class="h5 mb-0" id="due_balance_label">0.00 BDT</strong>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="due_payment_date">Payment Date</label>
                                <input type="date" name="payment_date" id="due_payment_date" class="form-control"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="due_pay_by">Payment Method</label>
                                <select name="pay_by" id="due_pay_by" class="form-control" required>
                                    <option value="">Select method</option>
                                    @foreach (['Cash', 'Mobile Banking', 'Card', 'Bank Account'] as $method)
                                        <option value="{{ $method }}" {{ old('pay_by') === $method ? 'selected' : '' }}>{{ $method }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group d-none" id="due_account_field">
                            <label for="due_account_id">Payment Account</label>
                            <select name="account_id" id="due_account_id" class="form-control">
                                <option value="">Select account</option>
                            </select>
                            <small class="text-muted" id="due_account_message"></small>
                        </div>

                        <div class="form-group">
                            <label for="due_amount_input">Payment Amount</label>
                            <input type="number" name="amount" id="due_amount_input" class="form-control"
                                value="{{ old('amount') }}" min="0.01" step="0.01" required>
                            <small class="text-muted">You may pay the full due or a partial amount.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="due_notes">Notes</label>
                            <textarea name="notes" id="due_notes" class="form-control" rows="2" placeholder="Optional payment reference or note">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @php
        $duePaymentAccounts = $accounts->map(function ($account) {
            if ($account->account_type === 'Mobile Banking') {
                $label = trim($account->mobile_bank_name . ' - ' . $account->mobile_number, ' -');
            } elseif ($account->account_type === 'Card') {
                $label = trim($account->card_type . ' - ' . $account->card_number, ' -');
            } else {
                $label = trim(optional($account->bank)->bank_name . ' - ' . $account->bank_account_number, ' -');
            }

            return ['id' => $account->id, 'type' => $account->account_type, 'label' => $label];
        })->values();
    @endphp
    <script>
        const DUE_PAYMENT_ACCOUNTS = @json($duePaymentAccounts);
        const OLD_DUE_ACCOUNT_ID = @json(old('account_id'));
        const dueForm = document.getElementById('duePaymentForm');
        const duePayBy = document.getElementById('due_pay_by');
        const dueAccountField = document.getElementById('due_account_field');
        const dueAccountSelect = document.getElementById('due_account_id');
        const dueAccountMessage = document.getElementById('due_account_message');

        function updateDueAccounts() {
            const method = duePayBy.value;
            const needsAccount = method && method !== 'Cash';
            dueAccountField.classList.toggle('d-none', !needsAccount);
            dueAccountSelect.required = Boolean(needsAccount);
            dueAccountSelect.innerHTML = '<option value="">Select account</option>';

            if (!needsAccount) {
                dueAccountMessage.textContent = '';
                return;
            }

            const matches = DUE_PAYMENT_ACCOUNTS.filter(account => account.type === method);
            matches.forEach(function (account) {
                dueAccountSelect.add(new Option(account.label, account.id, false, String(account.id) === String(OLD_DUE_ACCOUNT_ID)));
            });
            dueAccountMessage.textContent = matches.length ? '' : `No ${method} account is configured.`;
        }

        function openDuePayment(button) {
            dueForm.action = button.dataset.action;
            document.getElementById('due_purchase_id').value = button.dataset.purchaseId;
            document.getElementById('due_invoice_label').textContent = `Invoice: ${button.dataset.invoice}`;
            document.getElementById('due_balance_label').textContent = `${button.dataset.due} BDT`;
            document.getElementById('due_amount_input').max = button.dataset.due;
            if (!document.getElementById('due_amount_input').value) {
                document.getElementById('due_amount_input').value = button.dataset.due;
            }
        }

        function viewPurchase(id) {
            window.location.href = `/warehouse-purchases/${id}`;
        }

        function warehousePurchaseSearch() {
            $('#searching').submit();
        }

        document.querySelectorAll('.due-payment-button').forEach(function (button) {
            button.addEventListener('click', function () { openDuePayment(button); });
        });
        duePayBy.addEventListener('change', updateDueAccounts);
        updateDueAccounts();

        @if (old('warehouse_purchase_id'))
            const oldPaymentButton = document.querySelector('[data-purchase-id="{{ old('warehouse_purchase_id') }}"]');
            if (oldPaymentButton) {
                openDuePayment(oldPaymentButton);
                $('#duePaymentModal').modal('show');
            }
        @endif

        function updateWarehousePurchaseFilters() {
            const filterType = document.getElementById('filter_type')?.value || 'all';
            const showYear = filterType === 'month_year' || filterType === 'year';
            const showMonth = filterType === 'month_year';
            const showCustom = filterType === 'custom';

            const setVisible = (elementList, visible) => {
                elementList.forEach((field) => {
                    field.style.display = visible ? '' : 'none';
                    field.hidden = !visible;
                });
            };

            setVisible(document.querySelectorAll('.year-filter-field'), showYear);
            setVisible(document.querySelectorAll('.month-filter-field'), showMonth);
            setVisible(document.querySelectorAll('.custom-filter-field'), showCustom);

            const yearInput = document.getElementById('year');
            const monthInput = document.getElementById('month');
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            if (yearInput) {
                yearInput.required = showYear;
                yearInput.disabled = !showYear;
            }
            if (monthInput) {
                monthInput.required = showMonth;
                monthInput.disabled = !showMonth;
            }
            if (fromDateInput) {
                fromDateInput.required = showCustom;
                fromDateInput.disabled = !showCustom;
            }
            if (toDateInput) {
                toDateInput.required = showCustom;
                toDateInput.disabled = !showCustom;
            }
        }

        const filterTypeInput = document.getElementById('filter_type');
        if (filterTypeInput) {
            filterTypeInput.addEventListener('change', updateWarehousePurchaseFilters);
            if (window.jQuery) {
                $(filterTypeInput).on('change', updateWarehousePurchaseFilters);
            }
        }
        updateWarehousePurchaseFilters();

        $(function() {
            $("input[type='date']").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });

        $(function() {
            $('#tablefixed').responsiveTable({
                addFocusBtn: false
            });
        });
    </script>
@endsection
