@extends('layouts.dashboard')

@section('content')
<link rel="stylesheet" href="/css/remove-number-arrows.css">
<style>
    /* Hover/background fixes for dropdown */
    #showSearchProducts .list-group-item-action {
        background-color: #fff;
        transition: background-color 0.15s ease-in-out;
        border-bottom: 1px solid #eee;
    }
    #showSearchProducts .list-group-item-action:hover,
    #showSearchProducts .list-group-item-action:focus {
        background-color: #e6f7ff !important;
        color: #000;
        opacity: 1 !important;
    }
    #showSearchProducts .list-group-item.disabled {
        background-color: #f9f9f9;
        color: #aaa;
    }
</style>

<form action="{{ route('stock-transfer.store') }}" method="post" id="transfer-form">
    @csrf

    <div class="row">
        <div class="col-md-12">

            {{-- Header card --}}
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="m-t-0 header-title mb-2">
                            <b>{{ __('page.stock_transfer.stock_transfer') }}</b>
                        </h4>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="agent_id">{{ __('page.common.agent') }}
                                <i class="fa fa-info-circle text-info hover-q no-print"></i>
                            </label>
                            <select required name="agent_id" id="agent_id" class="form-control">
                                <option value="">Select Agent</option>
                                @foreach (getCachedAgents() as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date">{{ __('page.stock_transfer.date') }}
                                <i class="fa fa-info-circle text-info hover-q no-print"></i>
                            </label>
                            <input required id="date" type="text" name="date"
                                   class="form-control datepicker text-center" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Search & grid --}}
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-8 offset-md-2">

                        {{-- Validation errors (optional but useful) --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="input-group position-relative">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>

                                <input class="form-control prevent" id="search_product"
                                       placeholder="{{ __('page.purchase')[7] ?? 'Search product by name or barcode' }}"
                                       autocomplete="off">

                                {{-- Autocomplete dropdown --}}
                                <div id="showSearchProducts"
                                     class="list-group"
                                     style="display:none; position:absolute; top:100%; left:0; right:0; z-index:999; max-height:320px; overflow:auto; box-shadow:0 3px 6px rgba(0,0,0,0.1);">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Transfer lines --}}
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered" id="purchase_entry_table">
                                <thead>
                                <tr class="theme-primary text-white">
                                    <th style="width: 36%;">{{ __('page.purchase')[8] ?? 'Product / Batch' }}</th>
                                    <th style="width: 27%;">Stock Quantity</th>
                                    <th style="width: 27%;">Transfer Quantity</th>
                                    <th style="width: 10%"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                </tr>
                                </thead>
                                <tbody id="mytab1">
                                {{-- rows appended here --}}
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary float-right" style="cursor: pointer">
                            Transfer Stock
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@section('script')
<script>
    const ROUTE_TRANSFER_AUTOCOMPLETE = "{{ route('autocomplete.transfer') }}";

    // prevent form submit on Enter inside search
    document.getElementById('search_product').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') e.preventDefault();
    });

    let debounceTimer = null;
    const $search = document.getElementById('search_product');
    const $dropdown = document.getElementById('showSearchProducts');

    $search.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (q.length < 1) { $dropdown.style.display = 'none'; $dropdown.innerHTML = ''; return; }

        debounceTimer = setTimeout(async () => {
            try {
                // Use GET (matches your route)
                const url = new URL(ROUTE_TRANSFER_AUTOCOMPLETE, window.location.origin);
                url.searchParams.set('q', q);

                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Network error');
                const items = await res.json(); // [{id, product_name, barcode, batches:[{id, batch_id, available_quantity}]}]
                renderDropdown(items);
            } catch (e) {
                console.error(e);
                $dropdown.style.display = 'none';
                $dropdown.innerHTML = '';
            }
        }, 250);
    });

    function renderDropdown(items) {
        if (!Array.isArray(items) || items.length === 0) {
            $dropdown.style.display = 'none';
            $dropdown.innerHTML = '';
            return;
        }
        const html = items.map(p => {
            const batches = (p.batches || []).map(b => `
                <a href="#" class="list-group-item list-group-item-action"
                   data-product-id="${p.id}"
                   data-product-name="${escapeHtml(p.product_name)}"
                   data-barcode="${escapeHtml(p.barcode ?? '')}"
                   data-purchase-product-id="${b.id}"
                   data-batch-code="${escapeHtml(b.batch_id ?? b.id)}"
                   data-available-qty="${Number(b.available_quantity)}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(p.product_name)}</strong>
                            <small class="text-muted">(${escapeHtml(p.barcode ?? '-')})</small>
                        </div>
                        <div><small>Batch: ${escapeHtml(b.batch_id ?? b.id)} • Avl: ${Number(b.available_quantity)}</small></div>
                    </div>
                </a>
            `).join('');
            return batches || `
                <div class="list-group-item disabled">
                    ${escapeHtml(p.product_name)} <small class="text-muted">(No stock)</small>
                </div>`;
        }).join('');

        $dropdown.innerHTML = html;
        $dropdown.style.display = 'block';
    }

    $dropdown.addEventListener('click', function (e) {
        const a = e.target.closest('a.list-group-item');
        if (!a) return;
        e.preventDefault();
        addRowFromSelection(a.dataset);
        $dropdown.style.display = 'none';
        $dropdown.innerHTML = '';
        $search.value = '';
        $search.focus();
    });

    function addRowFromSelection(d) {
        const tbody = document.getElementById('mytab1');
        const exists = tbody.querySelector(`tr[data-purchase-product-id="${d.purchaseProductId}"]`);
        if (exists) {
            const qtyInput = exists.querySelector('input[name="quantity[]"]');
            qtyInput && qtyInput.focus();
            return;
        }
        const tr = document.createElement('tr');
        tr.setAttribute('data-purchase-product-id', d.purchaseProductId);
        tr.innerHTML = `
            <td>
                <div>
                    <strong>${escapeHtml(d.productName)}</strong><br>
                    <small class="text-muted">Batch: ${escapeHtml(d.batchCode)}</small>
                </div>
                <input type="hidden" name="product_id[]" value="${d.productId}">
                <input type="hidden" name="purchase_product_id[]" value="${d.purchaseProductId}">
            </td>
            <td class="text-right align-middle">
                <span class="badge badge-info" data-available="${Number(d.availableQty)}">${Number(d.availableQty)}</span>
            </td>
            <td>
                <input type="number" class="form-control text-right"
                       name="quantity[]" placeholder="0"
                       oninput="validateQty(this)">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                    <i class="fa fa-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    }

    // Server expects quantity[]; cap to available and min 1
    window.validateQty = function (input) {
        const tr = input.closest('tr');
        const avl = Number(tr.querySelector('[data-available]').getAttribute('data-available'));
        let val = Number(input.value || 0);
        if (val > avl) val = avl;
        if (val < 1) val = 1;
        input.value = val;
    };

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.input-group')) {
            $dropdown.style.display = 'none';
        }
    });

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"'`=\/]/g, s =>
            ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[s])
        );
    }
</script>
@endsection
