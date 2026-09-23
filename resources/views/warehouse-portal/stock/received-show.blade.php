<x-warehouse-layout title="Received Stock Details">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.hero-header eyebrow="Received Stock" title="Transfer Details" description="Review the products received by this Area Office." :back-href="route('warehouse.stock.received')" back-label="Back to Received List" />
        <div class="grid gap-4 border-t border-slate-200 bg-slate-50 px-4 py-5 sm:grid-cols-2 lg:grid-cols-4 sm:px-6">
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Invoice No.</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $transfer->invoice_no }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Received Date</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $transfer->transfer_date ? date('d M Y', strtotime($transfer->transfer_date)) : '-' }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Products / Total Quantity</p><p class="mb-0 mt-1 font-semibold text-slate-900">{{ $transfer->items->count() }} / {{ number_format((float) $transfer->total_quantity, 2) }}</p></div>
            <div><p class="m-0 text-xs font-bold uppercase text-slate-500">Total Selling Price</p><p class="mb-0 mt-1 font-semibold text-emerald-700">{{ number_format((float) $transfer->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</p></div>
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

                @forelse ($transfer->items as $item)
                    <tr>
                        <x-warehouse.table.td>{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-semibold">{{ $item->product?->product_name ?? '-' }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format((float) $item->quantity, 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td>{{ number_format((float) ($item->product?->selling_price ?? 0), 2) }} BDT</x-warehouse.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-warehouse.table.td colspan="4" class="py-8 text-center">No products found.</x-warehouse.table.td>
                    </tr>
                @endforelse

                <x-slot name="footer">
                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                        <x-warehouse.table.td colspan="2" class="text-right font-bold">Total</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-extrabold">{{ number_format((float) $transfer->items->sum('quantity'), 2) }}</x-warehouse.table.td>
                        <x-warehouse.table.td class="font-extrabold text-emerald-700">{{ number_format((float) $transfer->items->sum(fn ($item) => (float) $item->quantity * (float) ($item->product?->selling_price ?? 0)), 2) }} BDT</x-warehouse.table.td>
                    </tr>
                </x-slot>
            </x-warehouse.table>
        </div>
    </div>
</x-warehouse-layout>
