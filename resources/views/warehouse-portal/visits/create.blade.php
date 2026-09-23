<x-warehouse-layout title="Manual Visit Entry">
    @php
        $customerOptions = $customers->mapWithKeys(fn ($customer) => [(string) $customer->id => ['value' => (string) $customer->id, 'label' => $customer->name, 'description' => $customer->mobile ?: 'No Number']]);
        $customerRows = $customers->map(fn ($customer) => ['id' => (string) $customer->id, 'number' => $customer->beneficiary_number, 'group' => $customer->group_number])->values();
    @endphp
    <div class="mx-auto max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-5 sm:px-7">
            <div><h1 class="m-0 text-xl font-extrabold text-slate-900">Visit Information</h1><p class="mb-0 mt-1 text-sm text-slate-500">Add a manual daily visit for your beneficiary.</p></div>
            <a href="{{ route('warehouse.daily-visits') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">← Back</a>
        </div>
        <form method="POST" action="{{ route('warehouse.daily-visits.store') }}" enctype="multipart/form-data" class="space-y-6 p-5 sm:p-7">
            @csrf
            @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Please correct the required fields.</div>@endif
            <div class="grid gap-5 md:grid-cols-2">
                <div><x-warehouse.searchable-select name="app_customer_id" id="manual-customer" label="Beneficiary *" :options="$customerOptions" :selected="old('app_customer_id', $selectedCustomerId ?? '')" placeholder="Select Customer" search-placeholder="Search beneficiary" required /></div>
                <label class="text-sm font-semibold text-slate-700">Visit Date &amp; Time *<input type="datetime-local" name="visit_date" required value="{{ old('visit_date', now()->format('Y-m-d\TH:i')) }}" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                <label class="text-sm font-semibold text-slate-700">Beneficiary Number<input id="beneficiary-number" name="beneficiary_number" value="{{ old('beneficiary_number') }}" placeholder="Beneficiary Number" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                <label class="text-sm font-semibold text-slate-700">Beneficiary Group<input id="customer-number" name="customer_number" value="{{ old('customer_number') }}" placeholder="Group Number" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                <label class="text-sm font-semibold text-slate-700">Visit Type *<select name="visit_type" required class="mt-2 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Select Visit Type</option>@foreach(['C.F', 'Re.V','Reg.v','N.V', 'A.s'] as $type)<option value="{{ $type }}" @selected(old('visit_type') === $type)>{{ $type }}</option>@endforeach</select></label>
                <label class="text-sm font-semibold text-slate-700">Invoice / Memo No.<input name="memo_no" value="{{ old('memo_no') }}" placeholder="Invoice or memo number" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
            </div>
            <label class="block text-sm font-semibold text-slate-700">Description<textarea name="description" rows="3" placeholder="Short visit description" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('description') }}</textarea></label>
            <label class="block text-sm font-semibold text-slate-700">Document / File <span class="font-normal text-slate-500">(Max 10MB)</span><input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="mt-2 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"><span class="mt-1 block text-xs font-normal text-slate-500">Allowed: JPG, PNG, PDF, DOC, DOCX</span></label>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5"><div class="flex items-center justify-between"><h2 class="m-0 text-base font-extrabold text-slate-900">Fee Types</h2><button type="button" id="add-fee" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white">+ Add Fee</button></div><div id="fee-rows" class="mt-4 space-y-3"><div class="grid gap-3 sm:grid-cols-[1fr_180px_auto]"><input name="items[0][product]" placeholder="Medicine, Injection..." class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><input name="items[0][fee]" type="number" min="0" step="0.01" placeholder="Amount" class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><button type="button" class="remove-fee hidden rounded-xl border border-rose-200 px-3 text-rose-600">Remove</button></div></div></div>
            <div class="flex justify-end gap-3 border-t border-slate-200 pt-5"><a href="{{ route('warehouse.daily-visits') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a><button type="submit" class="rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">Save Visit</button></div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customer = document.getElementById('manual-customer');
            const customerRows = @json($customerRows);
            window.addEventListener('warehouse-searchable-select-change', function (event) { if (event.detail.id !== 'manual-customer') return; const customer = customerRows.find(row => row.id === String(event.detail.value)); document.getElementById('beneficiary-number').value = customer?.number || ''; document.getElementById('customer-number').value = customer?.group || ''; });
            let index = 1; const rows = document.getElementById('fee-rows');
            document.getElementById('add-fee')?.addEventListener('click', function () { const row = document.createElement('div'); row.className = 'grid gap-3 sm:grid-cols-[1fr_180px_auto]'; row.innerHTML = `<input name="items[${index}][product]" placeholder="Medicine, Injection..." class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><input name="items[${index}][fee]" type="number" min="0" step="0.01" placeholder="Amount" class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><button type="button" class="remove-fee rounded-xl border border-rose-200 px-3 text-rose-600">Remove</button>`; rows.appendChild(row); index++; });
            rows?.addEventListener('click', event => { if (event.target.classList.contains('remove-fee')) event.target.closest('.grid').remove(); });
        });
    </script>
</x-warehouse-layout>
