<x-warehouse-layout :title="'Return ' . $return->invoice_no">
    @php
        $returnDateTime = \Carbon\Carbon::parse($return->return_date_time)->format('M d, Y h:i A');
        $itemCount = $return->items->count();
        $formatQuantity = fn ($quantity) => rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.');
    @endphp

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            :title="'Return ' . $return->invoice_no"
            description="Review returned products and quantities restored to warehouse stock."
        >
            <x-slot name="actions">
                <x-warehouse.button href="{{ route('warehouse.salesman-returns.index') }}" variant="secondary" icon="arrow-left" class="rounded-xl px-4 py-2.5">
                    Back To Returns
                </x-warehouse.button>
            </x-slot>
        </x-warehouse.page-header>

        <div class="grid gap-3 border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:px-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Invoice</p>
                <p class="mb-0 mt-2 text-xl font-bold text-slate-900">{{ $return->invoice_no }}</p>
                <p class="mb-0 mt-1 text-xs text-slate-500">{{ $returnDateTime }}</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">LSP</p>
                <p class="mb-0 mt-2 text-xl font-bold text-sky-900">{{ $return->salesman?->name ?: '-' }}</p>
                <p class="mb-0 mt-1 text-xs text-sky-700/80">{{ $return->salesman?->email ?: 'No email provided' }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Products / Qty</p>
                <p class="mb-0 mt-2 text-xl font-bold text-emerald-900">{{ $itemCount }} / {{ $formatQuantity($return->total_quantity) }}</p>
                <p class="mb-0 mt-1 text-xs text-emerald-700/80">Returned to warehouse stock</p>
            </div>
        </div>

        <div class="grid gap-3 border-t border-slate-200 px-4 py-4 sm:grid-cols-2 sm:px-6">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Area Office</p>
                <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $return->warehouse?->name ?: '-' }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Notes</p>
                <p class="mb-0 mt-1 text-sm text-slate-700">{{ $return->notes ?: 'No notes added' }}</p>
            </div>
        </div>

        <section class="border-t border-slate-200 px-4 py-5 sm:px-6">
            <x-warehouse.table min-width="min-w-[640px]">
                <x-slot name="header"><tr><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Unit Size</x-warehouse.table.th><x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Returned Qty</x-warehouse.table.th></tr></x-slot>
                @forelse($return->items as $item)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $item->product?->product_name ?: '-' }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $item->product?->unit?->short_name ?: $item->product?->unit?->actual_name ?: '-' }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $formatQuantity($item->quantity) }}</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="4" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No returned products" description="This return does not contain any product rows." /></x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
        </section>
    </div>
</x-warehouse-layout>
