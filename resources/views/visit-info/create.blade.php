@extends('layouts.dashboard')

@section('content')
<div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
        <div class="card-header">
            <h3 class="card-title">{{ isset($visitInfo) ? 'Edit Visit Information' : 'Create New Visit' }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($visitInfo) ? route('visit-info.update', $visitInfo) : route('visit-info.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($visitInfo))
                    @method('PUT')
                @endif

                @php
                    // Normalize old/initial arrays
                    $memoValues = old('memo_no', isset($visitInfo->memo_no) ? (array)$visitInfo->memo_no : ['']);

                    // Build initial repeater items
                    $initialItems = old('items');
                    if (!$initialItems) {
                        if (isset($visitInfo) && isset($visitInfo->items) && is_array($visitInfo->items)) {
                            $initialItems = $visitInfo->items;
                        }
                    }
                    if (!$initialItems || !is_array($initialItems) || count($initialItems) === 0) {
                        $initialItems = [['product' => '', 'fee' => '']];
                    }

                    // Label map for products
                    $productMap = [
                        'Memberhip Fee' => 'Membership Fee',
                        'Deworming' => __('frontend.Deworming'),
                        'Anestrus' =>'Anestrus',
                        'Fattening' => __('frontend.Fattening'),
                        'Treatment and others' => __('frontend.Treatment'),
                        'Artificial Insemination'        => __('frontend.AI'),
                        'Medicine'  => __('frontend.Medicine'),
                        'Nill' => 'Nill',
                    ];
                @endphp

                <div class="row">
                    {{-- Customer (Select2) --}}
                    <div class="col-md-6"> 
                        <div class="form-group"> 
                            <label for="app_customer_id">Beneficiary Name <span class="text-danger">*</span></label> 
                            <select name="app_customer_id" id="app_customer_id" class="form-control js-select2 @error('app_customer_id') is-invalid @enderror" data-placeholder="Select Customer" required {{ isset($cmId) && $cmId != '' ? 'disabled' : '' }}> 
                                <option value=""></option> 
                                @foreach($customers as $customer) 
                                    <option value="{{ $customer->id }}" {{ (old('app_customer_id', $cmId ?? '') == $customer->id) ? 'selected' : '' }}> {{ $customer->name }} ({{ $customer->mobile }}) </option> 
                                    @endforeach 
                                </select> 
                                @if (isset($cmId) && $cmId != '') 
                                <input type="hidden" name="app_customer_id" value="{{ $cmId }}"> 
                                @endif 
                                @error('app_customer_id') <span class="invalid-feedback">{{ $message }}</span> 
                                @enderror 
                            </div> 
                        </div>

                    {{-- Customer Number --}}
                    
                    <div class="col-md-6 col-sm-12 mb-3">
                        <label for="customer_number">{{ __('sidebar.app.beneficiary') }}  Group Number <span class="text-danger">*</span></label>
                        <select name="customer_number" id="customer_number" class="form-control" required>
                            <option value="">Select Number</option>
                            @for($i = 1; $i <= 28; $i++)
                                @if ($i == 7 || $i == 14 || $i == 15 || $i == 22)
                                    @continue
                                @endif
                                <option value="{{ $i }}" {{ old('customer_number', $visitInfo->customer_number ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Visit Date --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="beneficiary_number">Beneficiary Number <span class="text-danger">*</span></label>
                            <select name="beneficiary_number" id="beneficiary_number" class="form-control" required>
                            <option value="">Select Beneficiary Number</option>
                            @for($i=1 ; $i<= 40 ; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-3">
                        <label for="visit_type"> Visit Type <span class="text-danger">*</span></label>
                        <select name="visit_type" id="visit_type" class="form-control" required>
                            <option value="">Select Visit Type</option>
                            <option value="C.F">C.F</option>
                            <option value="Re.V">Re.V</option>
                            <option value="Reg.v">Reg.v</option>
                             <option value="N.V">N.V</option>
                            <option value="A.s">A.s</option>
                        </select>
                    </div>
                    
                     <div class="col-md-6">
                        <div class="form-group">
                            <label for="visit_date">Description</label>
                           <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6"><div class="form-group"><label for="attachment">Document / File (Max 10MB)</label><input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="form-control"><small class="form-text text-muted">Allowed: JPG, PNG, PDF, DOC, DOCX</small></div></div>

                    {{-- Memo Numbers (multiple add/remove) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Memo / Invoice Number</label>
                            <div id="memo-container">
                            @forelse($memoValues as $i => $memo)
                                <div class="d-flex align-items-start gap-2 mb-2 memo-row">
                                <input type="text" name="memo_no[]" class="form-control" value="{{ $memo }}" readonly>
                                @error('memo_no.'.$i)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                </div>
                            @empty
                                <div class="d-flex align-items-start gap-2 mb-2 memo-row">
                                <input type="text" name="memo_no[]" class="form-control" readonly>
                                </div>
                            @endforelse
                            </div>
                            @error('memo_no')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Product + Fee Repeater (NO price auto-fill) --}}
                    <div class="col-12">
                        <div class="form-group">
                            <label class="d-flex align-items-center justify-content-between">
                                <span>Fee Type & Fees</span>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">+ Add Item</button>
                            </label>

                            <div id="items-repeater">
                                @foreach($initialItems as $i => $row)
                                    @php
                                        $prodVal = $row['product'] ?? '';
                                        $feeVal  = $row['fee'] ?? '';
                                    @endphp
                                    <div class="row g-2 align-items-end items-row mb-2" data-index="{{ $i }}">
                                        <div class="col-md-6">
                                            <label class="form-label">Fee Type</label>
                                            <select name="items[{{ $i }}][product]"
                                                    class="form-control @error('items.'.$i.'.product') is-invalid @enderror"
                                                    data-placeholder="Select Product" >
                                                <option value="">Select Fee Type</option>
                                                @foreach($productMap as $value => $label)
                                                    <option value="{{ $value }}" {{ $prodVal === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$i.'.product')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Fee</label>
                                            <input type="number" step="0.01"
                                                   name="items[{{ $i }}][fee]"
                                                   class="form-control @error('items.'.$i.'.fee') is-invalid @enderror"
                                                   value="{{ $feeVal }}" placeholder="Enter fee" >
                                            @error('items.'.$i.'.fee')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 d-flex gap-2">
                                            @if($i === 0)
                                                <button type="button" class="btn btn-outline-secondary w-100 add-item">+</button>
                                            @else
                                                <button type="button" class="btn btn-outline-danger w-100 remove-item">−</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Array-level errors --}}
                            @error('items')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($visitInfo) ? 'Update' : 'Create' }} Visit Information
                        </button>
                        <a href="{{ route('visit-info.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- === Self-contained assets + init (no @push needed) === --}}
<link id="select2-css-cdn" rel="stylesheet" href="" />
<style>
    /* Make Select2 blend with Bootstrap form-control */
    .select2-container .select2-selection--single,
    .select2-container .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        padding: 3px 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; right: 6px; }
    .is-invalid + .select2-container .select2-selection { border-color: #dc3545 !important; }
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

(function() {
    // --- CDN URLs ---
    const JQ_URL   = "https://code.jquery.com/jquery-3.7.1.min.js";
    const S2_CSS   = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css";
    const S2_JS    = "https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js";

    function loadCssOnce(href) {
        if ([...document.styleSheets].some(s => s.href === href)) return;
        const l = document.getElementById('select2-css-cdn') || document.createElement('link');
        l.rel = 'stylesheet'; l.href = href; l.id = 'select2-css-cdn';
        document.head.appendChild(l);
    }
    function loadJs(src, cb) {
        const s = document.createElement('script');
        s.src = src; s.async = true; s.onload = cb; document.body.appendChild(s);
    }

    function initSelects($) {
        $('.js-select2').select2({
            placeholder: function() { return $(this).data('placeholder') || 'Select'; },
            allowClear: true, width: '100%'
        });
        $('.js-select3').select2({
            placeholder: function() { return $(this).data('placeholder') || 'Select'; },
            allowClear: true, width: '100%'
        });
        $('.js-select2-multiple').select2({
            placeholder: function() { return $(this).data('placeholder') || 'Select'; },
            width: '100%'
        });
    }

    function initMemoHandlers($) {
        const $memoContainer = $('#memo-container');

        $(document).off('click.addMemo').on('click.addMemo', '.add-memo', function () {
            const row = `
                <div class="d-flex align-items-start gap-2 mb-2 memo-row">
                    <input type="text" name="memo_no[]" class="form-control" placeholder="Enter memo number">
                    <button type="button" class="btn btn-outline-danger remove-memo">−</button>
                </div>`;
            $memoContainer.append(row);
        });

        $(document).off('click.removeMemo').on('click.removeMemo', '.remove-memo', function () {
            $(this).closest('.memo-row').remove();
        });
    }

    function initItemsRepeater($) {
        const $wrap = $('#items-repeater');

        function nextIndex() {
            let max = -1;
            $wrap.find('.items-row').each(function () {
                const idx = parseInt($(this).attr('data-index'), 10);
                if (!isNaN(idx) && idx > max) max = idx;
            });
            return max + 1;
        }

        function rowTemplate(i) {
            // Build product options by cloning first select's options (includes labels/translations)
            let options = '<option value=""></option>';
            const existing = $wrap.find('select[name^="items["]').first();
            if (existing.length) {
                existing.find('option').each(function() {
                    const opt = $(this).clone();
                    options += opt.prop('outerHTML');
                });
                // remove duplicated blank
                options = options.replace(/(<option value=""><\/option>)+/, '<option value=""></option>');
            }

            return `
                <div class="row g-2 align-items-end items-row mb-2" data-index="${i}">
                    <div class="col-md-6">
                        <label class="form-label">Fee Type</label>
                        <select name="items[${i}][product]" class="form-control js-select2" data-placeholder="Select Product" >
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fee</label>
                        <input type="number" step="0.01" name="items[${i}][fee]" class="form-control" placeholder="Enter fee" >
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger w-100 remove-item">−</button>
                    </div>
                </div>`;
        }

        // Top-level add button
        $('#add-item-row').off('click.addItemTop').on('click.addItemTop', function () {
            const i = nextIndex();
            $wrap.append(rowTemplate(i));
            if (typeof $.fn.select2 !== 'undefined') {
                $wrap.find('.items-row:last .js-select2').select2({ placeholder: 'Select Product', width: '100%', allowClear: true });
            }
        });

        // First-row plus button
        $(document).off('click.addItemRow').on('click.addItemRow', '.add-item', function () {
            const i = nextIndex();
            $wrap.append(rowTemplate(i));
            if (typeof $.fn.select2 !== 'undefined') {
                $wrap.find('.items-row:last .js-select2').select2({ placeholder: 'Select Product', width: '100%', allowClear: true });
            }
        });

        // Remove row
        $(document).off('click.removeItemRow').on('click.removeItemRow', '.remove-item', function () {
            $(this).closest('.items-row').remove();
        });
    }

    function bootstrapAll() {
        const $ = window.jQuery;
        if (! $) return;

        if (typeof $.fn.select2 === 'undefined') {
            loadCssOnce(S2_CSS);
            loadJs(S2_JS, function() {
                initSelects($);
                initMemoHandlers($);
                initItemsRepeater($);
            });
        } else {
            initSelects($);
            initMemoHandlers($);
            initItemsRepeater($);
        }
    }

    window.addEventListener('DOMContentLoaded', function () {
        if (!window.jQuery) {
            loadJs(JQ_URL, bootstrapAll);
        } else {
            bootstrapAll();
        }
    });

    // Expose re-init for PJAX/Turbo
    window.reInitVisitForm = bootstrapAll;
})();
</script>
<script>
$(function () {
  const row = (v='') =>
    `<div class="d-flex align-items-start gap-2 mb-2 memo-row">
        <input type="text" name="memo_no[]" class="form-control" value="${v||''}" readonly>
     </div>`;

  function paint(list){
    const $box = $('#memo-container');
    $box.empty();
    if(!list || !list.length) return $box.append(row(''));
    $.each(list, function(i,v){ $box.append(row(v)); });
  }

  $(document).on('change', '#app_customer_id', function(){
    const cid = $(this).val();
    if(!cid){ paint([]); return; }
    const url = "{{ route('customers.invoices', ['customer' => 'CID']) }}".replace('CID', encodeURIComponent(cid));
    $.getJSON(url).done(function(res){ 
        $('#bf-number').val(res?.beneficiary_number || '');
        $('#customer_number').val(res?.group_number || '');
        paint(res?.data || []); 
    }).fail(function(){ paint([]); });
  });

  $(document).on('select2:select', '#app_customer_id', function(){ $(this).trigger('change'); });
});
</script>


@endsection
