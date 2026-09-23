<x-warehouse-layout title="Create Product Return">
    @php
        $oldItems = collect(old('items', []))->keyBy('product_id');
        $formatQuantity = fn ($quantity) => rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.');
        $salesmanOptions = $salesmen->mapWithKeys(function ($salesman) {
            return [(string) $salesman->id => [
                'value' => (string) $salesman->id,
                'label' => $salesman->name,
                'description' => $salesman->email ?: 'Area Office salesman',
            ]];
        });
    @endphp

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            title="Return Product"
            description="Return available salesman products back into warehouse stock."
            :back-href="route('warehouse.salesman-returns.index')"
            back-label="Returns"
        />

        <form method="GET" action="{{ route('warehouse.salesman-returns.create') }}" class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
            <div class="grid gap-4 lg:grid-cols-[minmax(260px,1fr)_auto] lg:items-end">
                <div>
                    <x-warehouse.searchable-select
                        name="salesman_id"
                        id="salesman_id"
                        label="LSP"
                        :options="$salesmanOptions"
                        :selected="$selectedSalesmanId"
                        placeholder="Search and select salesman"
                        search-placeholder="Search salesman name or email"
                        required
                    />
                </div>
                <x-loading-submit type="submit" class="inline-flex w-full px-5 py-3 lg:w-auto items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 text-sm font-semibold text-white shadow-sm hover:bg-red-500" icon="funnel" loading-text="Loading Products...">Load Products</x-loading-submit>
            </div>
        </form>

        @if($selectedSalesman)
            <form method="POST" action="{{ route('warehouse.salesman-returns.store') }}">
                @csrf
                <input type="hidden" name="warehouse_salesman_id" value="{{ $selectedSalesman->id }}">

                <div class="grid gap-4 border-t border-slate-200 px-4 py-4 sm:px-6 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="m-0 text-xs font-semibold uppercase tracking-wide text-slate-400">LSP</p><p class="mb-0 mt-1 font-bold text-slate-900">{{ $selectedSalesman->name }}</p></div>
                    <div>
                        <x-warehouse.input-label for="return_date_time" value="Return Date Time" class="mb-2 text-slate-700" />
                        <x-warehouse.text-input id="return_date_time" name="return_date_time" type="datetime-local" value="{{ old('return_date_time', now()->format('Y-m-d\TH:i')) }}" class="block h-11 w-full rounded-xl" required />
                        @error('return_date_time')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                    </div>
                    <div>
                        <x-warehouse.input-label for="notes" value="Notes (optional)" class="mb-2 text-slate-700" />
                        <x-warehouse.text-input id="notes" name="notes" type="text" value="{{ old('notes') }}" placeholder="Return note or reference" class="block h-11 w-full rounded-xl" />
                        @error('notes')<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
                    </div>
                </div>

                @error('items')<div class="border-t border-red-100 bg-red-50 px-5 py-3"><x-warehouse.input-error :messages="$message" /></div>@enderror

                <x-warehouse.table min-width="min-w-[860px]">
                    <x-slot name="header"><tr><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Unit</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Transfer Date Time</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Available Qty</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Return Qty</x-warehouse.table.th></tr></x-slot>
                    @forelse($products as $product)
                        @php $oldItem = $oldItems->get($product->id); @endphp
                        <tr class="hover:bg-slate-50">
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $loop->iteration }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $product->product_name }}<input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $product->id }}"></x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $product->unit?->short_name ?: $product->unit?->actual_name ?: '-' }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $product->last_transfer_at ? \Carbon\Carbon::parse($product->last_transfer_at)->format('d-m-Y h:i A') : '-' }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5"><span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-sm font-bold text-emerald-700 ring-1 ring-emerald-200">{{ number_format($product->available_quantity, 2) }}</span></x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">
                                <input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $oldItem['quantity'] ?? '' }}" min="0" max="{{ $product->available_quantity }}" step="0.01" placeholder="0" class="h-10 w-28 rounded-xl border border-slate-300 px-3 text-right text-sm font-semibold text-slate-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            </x-warehouse.table.td>
                        </tr>
                    @empty
                        <tr><x-warehouse.table.td colspan="6" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No returnable products" description="This salesman has no available stock to return." /></x-warehouse.table.td></tr>
                    @endforelse
                </x-warehouse.table>

                <div class="sticky bottom-0 z-20 flex flex-col gap-2 border-t border-slate-200 bg-slate-900 px-4 py-3 sm:flex-row sm:items-center sm:justify-end sm:px-6">
                    <x-warehouse.button href="{{ route('warehouse.salesman-returns.index') }}" variant="secondary" class="w-full sm:w-auto">Cancel</x-warehouse.button>
                    <x-loading-submit type="submit" class="inline-flex w-full min-w-[180px] items-center justify-center gap-2 rounded-xl border border-transparent bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:w-auto" icon="save" loading-text="Saving Return..." :disabled="$products->isEmpty()">Save Return</x-loading-submit>
                </div>
            </form>
        @else
            <div class="border-t border-slate-200 py-12 text-center">
                <x-warehouse.empty-state title="Select a salesman" description="Load a salesman to see available products and create a stock return." />
            </div>
        @endif
    </div>
</x-warehouse-layout>
