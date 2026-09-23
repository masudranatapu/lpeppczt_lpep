@extends('layouts.dashboard')

@section('content')
<link rel="stylesheet" href="/css/remove-number-arrows.css">

<form action="{{ route('stock-transfer.agent.store') }}" method="post">
    @csrf

    <input type="hidden" name="from_agent_id" value="{{ $fromAgentId }}">

    <div class="row">
        <div class="col-md-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #157c34;">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="m-t-0 header-title mb-2">
                            <b>{{ __('page.stock_transfer.stock_transfer') }} (Agent → Agent)</b>
                        </h4>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>From Agent</label>
                            <input type="text" class="form-control" value="{{ optional(auth()->user())->name ?? ('Agent #'.$fromAgentId) }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to_agent_id">{{ __('page.common.agent') }} (Receiver)
                                <i class="fa fa-info-circle text-info hover-q no-print"></i>
                            </label>
                            <select required name="to_agent_id" id="to_agent_id" class="form-control">
                                <option value="">Select Agent</option>
                                @foreach ($agents as $agent)
                                    @if ($agent->id != $fromAgentId)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date">{{ __('page.stock_transfer.date') }}
                                <i class="fa fa-info-circle text-info hover-q no-print "></i>
                            </label>
                            <input required id="date" type="text" name="date"
                                   class="form-control datepicker text-center" value="{{ $today }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>

                                <input class="form-control prevent" id="search_product"
                                       placeholder="Search product/batch from your stock…" autofocus name="search_product"
                                       type="text" autocomplete="off">

                                <div id="showSearchProducts" style="display:none; position:absolute; z-index:1000; max-height:300px; overflow:auto; width:100%; background:#fff; border:1px solid #eee; border-top:none;"></div>
                            </div>
                            <small class="text-muted">Type product name or batch no. Only batches you currently have will appear.</small>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-8 offset-md-2">
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered" id="purchase_entry_table">
                                <thead>
                                <tr class="theme-primary text-white">
                                    <th style="width: 34%;">Product &amp; Batch</th>
                                    <th style="width: 18%;">Available</th>
                                    <th style="width: 18%;">Transfer Qty</th>
                                    <th style="width: 10%"><i class="fa fa-trash" aria-hidden="true"></i></th>
                                </tr>
                                </thead>
                                <tbody id="mytab1"></tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary float-right" style="cursor:pointer">
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
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const ROUTE_TRANSFER_AUTOCOMPLETE = "{{ route('autocomplete.transfer.agent') }}"; // agent-scoped
    const FROM_AGENT_ID = "{{ $fromAgentId }}";

    // Minimal inline JS (works without touching your global /js/stock-transfer.js)
    (function () {
        const $search = document.getElementById('search_product');
        const $list = document.getElementById('showSearchProducts');
        const $tbody = document.getElementById('mytab1');
        const added = new Set(); // prevent duplicate batch rows

        function templateRow(item) {
            const key = item.purchase_product_id;
            return `
                <tr data-key="${key}">
                    <td>
                        <input type="hidden" name="product_id[]" value="${item.product_id}">
                        <input type="hidden" name="purchase_product_id[]" value="${item.purchase_product_id}">
                        <div><strong>${item.product_name}</strong></div>
                        <div class="text-muted">
                            ${item.invoice_no ? ('Invoice: ' + item.invoice_no) : ('Batch: PP#' + item.purchase_product_id)}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">${item.available_qty}</span>
                    </td>
                    <td>
                        <input type="number" min="1" max="${item.available_qty}" step="1" required
                               name="quantity[]" class="form-control text-center" placeholder="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }
    
        function renderDropdown(items) {
            if (!items.length) { $list.style.display = 'none'; $list.innerHTML=''; return; }
            $list.innerHTML = items.map(i =>
                `<div class="p-2 hover" data-json='${JSON.stringify(i)}'>
                    <div><strong>${i.product_name}</strong></div>
                    <small>
                        ${i.invoice_no ? ('Invoice: ' + i.invoice_no) : ('Batch: PP#' + i.purchase_product_id)}
                        • Available: ${i.available_qty}
                    </small>
                 </div>`
            ).join('');
            $list.style.display = 'block';
            Array.from($list.querySelectorAll('.hover')).forEach(el => {
                el.style.cursor = 'pointer';
                el.addEventListener('mouseenter', () => el.style.background = '#f9f9f9');
                el.addEventListener('mouseleave', () => el.style.background = '#fff');
                el.addEventListener('click', () => {
                    const data = JSON.parse(el.getAttribute('data-json'));
                    const key = data.purchase_product_id;
                    if (added.has(key)) {
                        $list.style.display = 'none';
                        $search.value = '';
                        return;
                    }
                    added.add(key);
                    $tbody.insertAdjacentHTML('beforeend', templateRow(data));
                    $list.style.display = 'none';
                    $search.value = '';
                });
            });
        }

        let t = null;
        $search.addEventListener('input', function () {
            clearTimeout(t);
            const q = this.value.trim();
            if (q.length < 1) { $list.style.display = 'none'; return; }
            t = setTimeout(() => {
                const url = new URL(ROUTE_TRANSFER_AUTOCOMPLETE, window.location.origin);
                url.searchParams.set('q', q);
                url.searchParams.set('from_agent_id', FROM_AGENT_ID);
                fetch(url, {headers: {'X-Requested-With':'XMLHttpRequest'}})
                    .then(r => r.json())
                    .then(renderDropdown)
                    .catch(() => { $list.style.display = 'none'; });
            }, 200);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#showSearchProducts') && e.target !== $search) {
                $list.style.display = 'none';
            }
        });

        $tbody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-remove');
            if (!btn) return;
            const tr = btn.closest('tr');
            const key = tr.getAttribute('data-key');
            added.delete(parseInt(key, 10));
            tr.remove();
        });
    })();
</script>
@endsection
