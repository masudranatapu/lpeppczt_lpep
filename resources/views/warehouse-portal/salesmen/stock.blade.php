<x-warehouse-layout :title="'Current Stock - ' . $salesman->name">
    <div class="space-y-5">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header
                :title="'Current Stock: ' . $salesman->name"
                description="{{ $warehouse->name }} stock after assignments, sales, and returns."
            >
                <x-slot name="actions">
                    <x-warehouse.button href="{{ route('warehouse.salesmen.index') }}" variant="secondary" icon="arrow-left" class="rounded-xl px-4 py-2.5">
                        Back To LSPs
                    </x-warehouse.button>
                </x-slot>
            </x-warehouse.page-header>

            <section class="grid gap-4 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:grid-cols-2 sm:px-6 xl:grid-cols-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Products</p><p class="mb-0 mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary->product_count) }}</p></div>
                <div class="rounded-2xl border border-blue-200 bg-white p-4 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-blue-700">LSP Assign</p><p class="mb-0 mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary->assigned_quantity, 2) }}</p></div>
                <div class="rounded-2xl border border-amber-200 bg-white p-4 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-amber-700">Sold</p><p class="mb-0 mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary->sold_quantity, 2) }}</p></div>
                <div class="rounded-2xl border border-rose-200 bg-white p-4 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-rose-700">LSP Return</p><p class="mb-0 mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($summary->returned_quantity, 2) }}</p></div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"><p class="m-0 text-xs font-bold uppercase tracking-wider text-emerald-700">Current Stock</p><p class="mb-0 mt-2 text-2xl font-extrabold text-emerald-900">{{ number_format($summary->current_quantity, 2) }}</p></div>
            </section>

            <x-warehouse.table min-width="min-w-[900px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Unit</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Assigned Qty</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Sold Qty</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Returned Qty</x-warehouse.table.th>
                        <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Current Qty</x-warehouse.table.th>
                    </tr>
                </x-slot>
                @forelse($stock as $product)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $product->product_name }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $product->unit?->short_name ?: $product->unit?->actual_name ?: '-' }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format($product->assigned_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format($product->sold_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ number_format($product->returned_quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-bold text-emerald-700">{{ number_format($product->current_quantity, 2) }}</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="7" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No stock found" description="This salesman has not received any assigned stock yet." /></x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
        </div>
    </div>
</x-warehouse-layout>
