<x-warehouse-layout title="LSP Stock Distribution">
    <div class="space-y-5">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header
                title="LSP Stock Distribution"
                description="See who received this product, how much was sold or returned, and the current quantity held by each LSP."
            >
                <x-slot name="actions">
                    <x-warehouse.button href="{{ route('warehouse.stock') }}" variant="secondary" icon="arrow-left" class="rounded-xl px-5 py-3">Back to Stock</x-warehouse.button>
                </x-slot>
            </x-warehouse.page-header>

            <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
                <p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">Product</p>
                <h3 class="mb-0 mt-1 text-xl font-extrabold text-slate-900">{{ $product->product_name }}</h3>
                <p class="mb-0 mt-1 text-sm text-slate-500">{{ $warehouse->name }} · {{ $product->unit?->short_name ?: $product->unit?->actual_name ?: 'Unit' }}</p>
            </div>

            <div class="grid gap-3 border-t border-slate-200 bg-white px-4 py-4 sm:grid-cols-2 xl:grid-cols-4 sm:px-6">
                @foreach ([
                    ['label' => 'Total LSP Assign', 'value' => $totals->assigned, 'color' => 'text-blue-700'],
                    ['label' => 'Total Sold', 'value' => $totals->sold, 'color' => 'text-emerald-700'],
                    ['label' => 'Current LSP Stock', 'value' => $totals->current, 'color' => 'text-violet-700'],
                ] as $item)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="m-0 text-xs font-bold uppercase tracking-wider text-slate-500">{{ $item['label'] }}</p>
                        <p class="mb-0 mt-1 text-2xl font-extrabold {{ $item['color'] }}">{{ number_format((float) $item['value'], 2) }}</p>
                    </div>
                @endforeach
            </div>

            <x-warehouse.table min-width="min-w-[760px]">
                <x-slot name="header">
                    <tr>
                        <x-warehouse.table.th>SL</x-warehouse.table.th>
                        <x-warehouse.table.th>LSP</x-warehouse.table.th>
                        <x-warehouse.table.th>LSP Assign</x-warehouse.table.th>
                        <x-warehouse.table.th>Sold</x-warehouse.table.th>
                        <x-warehouse.table.th>Current Stock</x-warehouse.table.th>
                    </tr>
                </x-slot>

                @forelse($distribution as $salesman)
                    <tr class="hover:bg-slate-50">
                        <x-warehouse.table.td>{{ $loop->iteration }}</x-warehouse.table.td>
                        <x-warehouse.table.td>
                            <div class="font-bold text-slate-900">{{ $salesman->name }}</div>
                            <div class="text-xs text-slate-500">{{ $salesman->username ?: $salesman->email }}</div>
                        </x-warehouse.table.td>
                        <x-warehouse.table.td><span class="inline-flex min-w-[72px] justify-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-bold text-blue-700">{{ number_format((float) $salesman->net_assigned_quantity, 2) }}</span></x-warehouse.table.td>
                        <x-warehouse.table.td><span class="lsp-sale-pulse inline-flex min-w-[72px] justify-center rounded-full border border-red-300 bg-red-50 px-3 py-1 text-sm font-bold text-red-700">{{ number_format((float) $salesman->sold_quantity, 2) }}</span></x-warehouse.table.td>
                        <x-warehouse.table.td>
                            <span class="inline-flex min-w-[72px] justify-center rounded-full border border-violet-200 bg-violet-50 px-3 py-1 text-sm font-extrabold text-violet-700">{{ number_format((float) $salesman->current_quantity, 2) }}</span>
                        </x-warehouse.table.td>
                    </tr>
                @empty
                    <tr><x-warehouse.table.td colspan="5" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No LSP movement found" description="This product has not been assigned to an LSP yet." /></x-warehouse.table.td></tr>
                @endforelse
            </x-warehouse.table>
        </section>
    </div>
    <style>
        .lsp-sale-pulse { animation: lsp-sale-portal-pulse 1.8s ease-in-out infinite; }
        @keyframes lsp-sale-portal-pulse {
            0%, 100% { border-color: #fecaca; box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
            50% { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220, 38, 38, .13); }
        }
        @media (prefers-reduced-motion: reduce) { .lsp-sale-pulse { animation: none; } }
    </style>
</x-warehouse-layout>
