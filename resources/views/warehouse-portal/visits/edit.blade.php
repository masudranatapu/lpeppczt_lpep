<x-warehouse-layout title="Edit Visit">
    <div class="relative overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm sm:rounded-3xl">
        <x-warehouse.hero-header eyebrow="Daily Visit" title="Edit Visit Information" description="Update the visit details and fee information." :back-href="route('warehouse.daily-visits')" back-label="Back to Visit List" />
        <form method="POST" action="{{ route('warehouse.daily-visits.update', $visit) }}" class="space-y-4 p-4 sm:space-y-5 sm:p-6">
            @csrf @method('PUT')
            <div class="grid gap-3 md:grid-cols-2 md:gap-4">
                <label class="text-sm font-semibold text-slate-700">Beneficiary Name
                    <select disabled class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 bg-slate-100 px-3 text-sm"><option>{{ $visit->appCustomer?->name ?? '-' }}</option></select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Visit Date &amp; Time *
                    <input type="datetime-local" name="visit_date" required value="{{ old('visit_date', $visit->visit_date?->format('Y-m-d\TH:i')) }}" class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </label>
                <label class="text-sm font-semibold text-slate-700">Beneficiary Number *
                    <select name="beneficiary_number" required class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        @for($number = 1; $number <= 40; $number++)
                            <option value="{{ $number }}" {{ (string) old('beneficiary_number', $visit->beneficiary_number ?: $visit->appCustomer?->beneficiary_number) === (string) $number ? 'selected' : '' }}>{{ $number }}</option>
                        @endfor
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Beneficiary Group Number *
                    <select name="customer_number" required class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        @for($number = 1; $number <= 28; $number++)
                            <option value="{{ $number }}" {{ (string) old('customer_number', $visit->customer_number ?: $visit->appCustomer?->group_number) === (string) $number ? 'selected' : '' }}>{{ $number }}</option>
                        @endfor
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Visit Type *
                    <select name="visit_type" required class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        <option value="">Select Visit Type</option>
                        @foreach(['C.F', 'Re.V','Reg.v','N.V','A.s'] as $type)
                            <option value="{{ $type }}" {{ old('visit_type', $visit->visit_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Memo / Invoice Number
                    <input value="{{ is_array($visit->memo_no) ? implode(', ', $visit->memo_no) : '-' }}" readonly class="mt-1.5 h-10 w-full rounded-lg border border-slate-300 bg-slate-100 px-3 text-sm">
                </label>
            </div>
            <label class="block text-sm font-semibold text-slate-700">Description<textarea name="description" rows="2" class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $visit->description) }}</textarea></label>
            @php($feeTypes = collect(['Membership Fee','Deworming','Anestrus','Fattening','Treatment and others','Artificial Insemination','Medicine','Nill'])->mapWithKeys(fn ($type) => [$type => $type]))
            <div><div class="flex items-center justify-between"><h2 class="m-0 text-base font-extrabold text-slate-900">Fee Information</h2><button type="button" id="add-fee" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">+ Add Item</button></div><div id="fee-items" class="mt-3 space-y-3">
                @forelse($visit->fees as $fee)
                    <div class="grid gap-3 md:grid-cols-[1fr_180px_auto]"><x-warehouse.searchable-select name="items[{{ $loop->index }}][product]" id="visit-fee-type-{{ $loop->index }}" :options="$feeTypes" :selected="$fee->fee_type" placeholder="Select Fee Type" search-placeholder="Search fee type" /><input type="number" step="0.01" min="0" name="items[{{ $loop->index }}][fee]" value="{{ $fee->amount }}" placeholder="Enter fee" class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><button type="button" class="remove-fee h-11 rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700">Remove</button></div>
                @empty
                    <div class="grid gap-3 md:grid-cols-[1fr_180px_auto]"><x-warehouse.searchable-select name="items[0][product]" id="visit-fee-type-0" :options="$feeTypes" placeholder="Select Fee Type" search-placeholder="Search fee type" /><input type="number" step="0.01" min="0" name="items[0][fee]" placeholder="Enter fee" class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><button type="button" class="remove-fee h-11 rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700">Remove</button></div>
                @endforelse
            </div><template id="fee-row-template"><div class="grid gap-3 md:grid-cols-[1fr_180px_auto]"><x-warehouse.searchable-select name="items[__INDEX__][product]" id="visit-fee-type-__INDEX__" :options="$feeTypes" placeholder="Select Fee Type" search-placeholder="Search fee type" /><input type="number" step="0.01" min="0" name="items[__INDEX__][fee]" placeholder="Enter fee" class="h-11 rounded-xl border border-slate-300 px-3 text-sm"><button type="button" class="remove-fee h-11 rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700">Remove</button></div></template></div>
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end sm:gap-3"><a href="{{ route('warehouse.daily-visits') }}" class="inline-flex w-full justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 sm:w-auto">Cancel</a><x-loading-submit type="submit" class="w-full rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white sm:w-auto" loading-text="Updating Visit...">Update Visit</x-loading-submit></div>
        </form>
    </div>
</x-warehouse-layout>

<script>
let feeIndex = {{ $visit->fees->count() ?: 1 }};
const feeTypes = ['Membership Fee', 'Deworming', 'Anestrus', 'Fattening', 'Treatment and others', 'Artificial Insemination', 'Medicine', 'Nill'];
document.getElementById('add-fee')?.addEventListener('click', () => { const fragment = document.getElementById('fee-row-template').content.cloneNode(true); const wrapper = document.createElement('div'); wrapper.appendChild(fragment); wrapper.innerHTML = wrapper.innerHTML.replaceAll('__INDEX__', feeIndex); const row = wrapper.firstElementChild; document.getElementById('fee-items').appendChild(row); if (window.Alpine) Alpine.initTree(row); feeIndex++; });
document.addEventListener('click', event => { if (event.target.classList.contains('remove-fee')) event.target.closest('.grid').remove(); });
</script>
