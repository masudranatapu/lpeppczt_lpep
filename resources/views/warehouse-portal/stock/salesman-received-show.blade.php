<x-warehouse-layout title="Received Stock Details">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Received Stock Details" description="Review the products received in this assignment." />
        <div class="grid gap-3 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:grid-cols-2 lg:grid-cols-4 sm:px-6">
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Invoice No.</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $assignment->invoice_no }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Received Date &amp; Time</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $assignment->created_at ? date('Y-m-d H:i', strtotime($assignment->created_at)) : ($assignment->assignment_date ?: '-') }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Products / Total Quantity</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $assignment->items->count() }} / {{ number_format((float) $assignment->total_quantity, 2) }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Total Selling Price</p><p class="mb-0 mt-1 font-semibold text-emerald-700">{{ number_format((float) $assignment->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</p></div>
        </div>
        <div class="p-4 sm:p-6">
            <x-warehouse.table min-width="min-w-[700px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th>SL</x-warehouse.table.th>
                        <x-warehouse.table.th>Product</x-warehouse.table.th>
                        <x-warehouse.table.th>Quantity</x-warehouse.table.th>
                        <x-warehouse.table.th>Selling Price</x-warehouse.table.th>
                    </tr>
                </x-slot>

                @forelse ($assignment->items as $item)
                    <tr>
                        <x-warehouse.table.td>{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold">{{ $item->product?->product_name ?? '-' }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format((float) ($item->quantity ?? 0), 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format((float) ($item->product?->selling_price ?? 0), 2) }} BDT</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-warehouse.table.td colspan="4" class="py-8 text-center">No products found.</x-warehouse.table.td>
                    </tr>
                @endforelse

                <x-slot name="footer">
                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                        <x-warehouse.table.td colspan="2" class="text-right font-bold text-slate-700">Total</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-extrabold text-slate-900">{{ number_format((float) $assignment->items->sum('quantity'), 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-extrabold text-emerald-700">{{ number_format((float) $assignment->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</x-warehouse.table.td>
                    </tr>
                </x-slot>
            </x-warehouse.table>

            <div class="mt-4">
                <x-warehouse.button href="{{ route('warehouse.stock.lsp-received') }}" variant="secondary">Back to Received List</x-warehouse.button>
            </div>
        </div>
    </div>
</x-warehouse-layout>
