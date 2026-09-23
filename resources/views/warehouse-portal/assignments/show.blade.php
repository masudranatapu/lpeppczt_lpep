<x-warehouse-layout :title="'Assignment ' . $assignment->invoice_no">
    @php
        $assignmentDate = $assignment->assignment_date instanceof \DateTimeInterface
            ? $assignment->assignment_date->format('M d, Y')
            : \Carbon\Carbon::parse($assignment->assignment_date)->format('M d, Y');
        $itemCount = $assignment->items->count();
        $formatQuantity = function ($quantity) {
            return rtrim(rtrim(number_format((float) $quantity, 2, '.', ''), '0'), '.');
        };
    @endphp

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header
            :title="'Assignment ' . $assignment->invoice_no"
            description="Review assignment information and the salesman’s current product stock."
        >
            <x-slot name="actions">
                <x-warehouse.button href="{{ route('warehouse.salesman-assignments.index') }}" variant="secondary" icon="arrow-left" class="rounded-xl px-4 py-2.5">
                    Back To Assignments
                </x-warehouse.button>
            </x-slot>
        </x-warehouse.page-header>

        <div class="grid gap-3 border-t border-slate-200 bg-slate-50/70 px-4 py-4 sm:px-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Invoice</p>
                <p class="mb-0 mt-2 text-xl font-bold text-slate-900">{{ $assignment->invoice_no }}</p>
                <p class="mb-0 mt-1 text-xs text-slate-500">{{ $assignmentDate }}</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">LSP</p>
                <p class="mb-0 mt-2 text-xl font-bold text-sky-900">{{ $assignment->salesman?->name ?: '-' }}</p>
                <p class="mb-0 mt-1 text-xs text-sky-700/80">{{ $assignment->salesman?->email ?: 'No email provided' }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm">
                <p class="m-0 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Products / Qty</p>
                <p class="mb-0 mt-2 text-xl font-bold text-emerald-900">{{ $itemCount }} / {{ $formatQuantity($assignment->total_quantity) }}</p>
                <p class="mb-0 mt-1 text-xs text-emerald-700/80">Products and quantity in this assignment</p>
            </div>
        </div>

        <div class="grid gap-3 border-t border-slate-200 px-4 py-4 sm:grid-cols-2 sm:px-6">
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Area Office</p>
                <p class="mb-0 mt-1 text-sm font-semibold text-slate-900">{{ $assignment->warehouse?->name ?: '-' }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <p class="m-0 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Notes</p>
                <p class="mb-0 mt-1 text-sm text-slate-700">{{ $assignment->notes ?: 'No notes added' }}</p>
            </div>
        </div>

        <section class="border-t border-slate-200 px-4 py-5 sm:px-6">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div>
                        <h2 class="m-0 text-lg font-bold tracking-tight text-slate-900">Current Stock</h2>
                        <p class="mb-0 mt-0.5 text-sm text-slate-500">Product quantities included in this assignment for {{ $assignment->salesman?->name ?: 'this salesman' }}.</p>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200">{{ $itemCount }} {{ \Illuminate\Support\Str::plural('Product', $itemCount) }}</span>
                </div>

                <x-warehouse.table min-width="min-w-[640px]">
                    <x-slot name="header">
                        <tr>
                            <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">SL</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Product</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Unit Size</x-warehouse.table.th>
                            <x-warehouse.table.th padding-class="px-4 py-2 sm:px-5">Qty</x-warehouse.table.th>
                        </tr>
                    </x-slot>

                    @forelse($assignment->items as $item)
                        <tr class="hover:bg-slate-50">
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-medium text-slate-900">{{ $loop->iteration }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $item->product?->product_name ?: '-' }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5">{{ $item->product?->unit?->short_name ?: $item->product?->unit?->actual_name ?: '-' }}</x-warehouse.table.td>
                            <x-warehouse.table.td padding-class="px-4 py-1.5 sm:px-5" class="font-semibold text-slate-900">{{ $formatQuantity($item->quantity) }}</x-warehouse.table.td>
                        </tr>
                    @empty
                        <tr><x-warehouse.table.td colspan="4" :nowrap="false" class="py-12 text-center"><x-warehouse.empty-state title="No assigned products" description="This assignment does not contain any product rows." /></x-warehouse.table.td></tr>
                    @endforelse
                </x-warehouse.table>
            </div>
        </section>
    </div>
</x-warehouse-layout>
