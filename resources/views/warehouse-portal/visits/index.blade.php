<x-warehouse-layout title="Daily Visits">
    <style>
        .daily-visits-table { border-collapse: separate; border-spacing: 0; }
        .daily-visits-table thead th { border-bottom: 1px solid #dbe4ef; background: #f8fafc; color: #475569; font-size: 11px; font-weight: 800; letter-spacing: .035em; text-transform: uppercase; white-space: nowrap; }
        .daily-visits-table tbody td { border-bottom: 1px solid #edf2f7; color: #172033; font-size: 13px; vertical-align: middle; }
        .daily-visits-table tbody tr:last-child td { border-bottom: 0; }
        .daily-visits-table tbody tr { transition: background .15s ease; }
        .daily-visits-table tbody tr:hover { background: #f8fbff; }
        .daily-visits-table .sl-column { width: 64px; min-width: 64px; text-align: center; }
        .daily-visits-table .sl-number { display: inline-flex; height: 28px; width: 28px; align-items: center; justify-content: center; border: 1px solid #d8e5f2; border-radius: 50%; background: #f8fafc; color: #334155; font-size: 12px; font-weight: 800; }
        .daily-visits-table .amount-column { white-space: nowrap; color: #0f766e; font-weight: 800; }
        .daily-visits-table .cell-number-column { min-width: 150px; }
        .daily-visits-table .beneficiary-name-column { min-width: 145px; }
        .daily-visits-table .description-column { min-width: 320px; width: 320px; white-space: normal; overflow-wrap: anywhere; line-height: 1.45; }
        .daily-visits-table .fee-types-column { min-width: 190px; }
        .daily-visits-table .actions-column { min-width: 125px; }
    </style>
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <x-warehouse.page-header title="Visit Information" description="Daily visit records for the current month.">
            <x-slot name="actions"><a target="_blank" href="{{ route('warehouse.daily-visits.print', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700"><x-warehouse.icon name="printer" class="h-4 w-4" />Print / PDF</a></x-slot>
        </x-warehouse.page-header>
        <div x-data="{ filtersOpen: false }" class="border-t border-slate-200 bg-slate-50">
            <button type="button" @click="filtersOpen = true" class="flex w-full items-center justify-between px-4 py-3 text-sm font-bold text-slate-700 lg:hidden"><span>Filter</span><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 6h16M7 12h10M10 18h4" /></svg></button>
            <div x-cloak x-show="filtersOpen" x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40 lg:hidden" @click="filtersOpen = false"></div>
            <form method="GET" action="{{ route('warehouse.daily-visits') }}" class="fixed inset-y-0 right-0 z-[90] hidden w-[min(92vw,380px)] overflow-y-auto bg-white px-5 py-5 shadow-2xl lg:static lg:block lg:w-auto lg:overflow-visible lg:bg-transparent lg:px-4 lg:py-4 lg:shadow-none sm:lg:px-6" :class="filtersOpen ? '!block' : ''">
                <div class="mb-5 flex items-center justify-between lg:hidden"><h2 class="m-0 text-lg font-extrabold text-slate-900">Filter</h2><button type="button" @click="filtersOpen = false" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100" aria-label="Close filter"><span class="text-xl">&times;</span></button></div>
                <div class="grid gap-4 lg:grid-cols-[minmax(220px,1fr)_210px_160px_160px_130px_auto] lg:items-end">
                <div><x-warehouse.input-label for="search" value="Visit Information" class="mb-2 text-slate-700" /><input type="text" id="search" name="search" value="{{ $search }}" placeholder="Customer, phone, beneficiary" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"></div>
                <div>
                    @php($lspOptions = $salesmen->mapWithKeys(fn ($salesman) => [$salesman->id => $salesman->name])->all())
                    <x-warehouse.searchable-select name="salesman_id" id="daily-visits-lsp-filter" label="LSP" :options="$lspOptions" :selected="$salesmanId" placeholder="All LSPs" search-placeholder="Search LSP" />
                </div>
                <div><x-warehouse.input-label for="from_date" value="Visit Date From" class="mb-2 text-slate-700" /><input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"></div>
                <div><x-warehouse.input-label for="to_date" value="Visit Date To" class="mb-2 text-slate-700" /><input type="date" id="to_date" name="to_date" value="{{ $toDate }}" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"></div>
                <div><x-warehouse.input-label for="per_page" value="Show" class="mb-2 text-slate-700" /><select name="per_page" id="per_page" class="block h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none"><option value="10" {{ $perPage === '10' ? 'selected' : '' }}>10</option><option value="25" {{ $perPage === '25' ? 'selected' : '' }}>25</option><option value="50" {{ $perPage === '50' ? 'selected' : '' }}>50</option><option value="100" {{ $perPage === '100' ? 'selected' : '' }}>100</option><option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option></select></div>
                <div class="flex gap-2"><x-loading-submit type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white" icon="funnel" loading-text="Filtering...">Filter</x-loading-submit><x-warehouse.button href="{{ route('warehouse.daily-visits') }}" variant="secondary" class="px-4 py-3" icon="rotate-ccw">Reset</x-warehouse.button></div>
                </div>
            </form>
        </div>
        <div class="grid grid-cols-2 gap-2 border-t border-slate-200 bg-white px-3 py-2 sm:gap-3 sm:px-6 sm:py-3">
            <div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 sm:block sm:rounded-xl sm:px-4 sm:py-3">
                <div class="text-[10px] font-semibold uppercase leading-tight tracking-wide text-slate-500 sm:text-xs">Total Amount</div>
                <div class="mt-0 truncate text-xs font-bold text-slate-900 sm:mt-1 sm:text-xl">{{ number_format($totalAmount, 2) }} BDT</div>
            </div>
            <div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 sm:block sm:rounded-xl sm:px-4 sm:py-3">
                <div class="text-[10px] font-semibold uppercase leading-tight tracking-wide text-slate-500 sm:text-xs">Period</div>
                <div class="mt-0 truncate text-[10px] font-semibold text-slate-900 sm:mt-1 sm:text-sm">{{ $fromDate }} to {{ $toDate }}</div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="daily-visits-table min-w-[1450px] w-full text-left text-sm">
                <thead><tr><th class="sl-column px-3 py-3">SL</th><th class="cell-number-column whitespace-normal px-4 py-3"><span class="block">Beneficiary</span><span class="block">Cell Number</span></th><th class="beneficiary-name-column whitespace-normal px-4 py-3"><span class="block">Beneficiary</span><span class="block">Name</span></th><th class="whitespace-normal px-4 py-3"><span class="block">Beneficiary</span><span class="block">Group</span></th><th class="whitespace-normal px-4 py-3"><span class="block">Beneficiary</span><span class="block">Number</span></th><th class="whitespace-normal px-4 py-3"><span class="block">Visit Date &amp;</span><span class="block">Time</span></th><th class="whitespace-normal px-4 py-3"><span class="block">Invoice</span><span class="block">No</span></th><th class="whitespace-normal px-4 py-3"><span class="block">Total</span><span class="block">Amount</span></th><th class="fee-types-column whitespace-normal px-4 py-3">Fee Types</th><th class="description-column whitespace-normal px-4 py-3">Description</th><th class="whitespace-normal px-4 py-3">File</th><th class="actions-column whitespace-normal px-4 py-3">Actions</th></tr></thead>
                <tbody>
            <?php $rowNumber = $isPaginated ? $visits->firstItem() : 1; ?>
            <?php if (count($visits) > 0): ?>
            <?php foreach ($visits as $visit): ?>
                <tr class="hover:bg-slate-50">
                    <td class="sl-column px-3 py-2.5"><span class="sl-number">{{ $rowNumber++ }}</span></td>
                    <td class="px-4 py-2.5">{{ $visit->appCustomer?->mobile ?: ($visit->customer_number ?: 'No Number') }}</td>
                    <td class="px-4 py-2.5 font-semibold text-slate-900">{{ $visit->appCustomer?->name ?: '-' }}</td>
                    <td class="px-4 py-2.5">{{ $visit->appCustomer?->group_number ?: 'No number' }}</td>
                    <td class="px-4 py-2.5">{{ $visit->beneficiary_number ?: ($visit->appCustomer?->beneficiary_number ?: '-') }}</td>
                    <td class="whitespace-nowrap px-4 py-2.5">{{ $visit->display_visit_date }}</td>
                    <td class="whitespace-nowrap px-4 py-2.5 font-medium text-slate-700">{{ is_array($visit->memo_no) && count($visit->memo_no) ? implode(', ', $visit->memo_no) : '—' }}</td>
                    <td class="amount-column px-4 py-2.5">{{ number_format((float) $visit->fees->sum('amount'), 2) }}</td>
                    <?php
                        $feeSummary = $visit->fees->map(function ($fee) {
                            return '<span class="inline-flex w-fit items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs shadow-sm"><strong class="text-emerald-800">' . e($fee->fee_type ?: '-') . ':</strong><span class="whitespace-nowrap font-semibold text-emerald-950">' . number_format((float) $fee->amount, 2) . ' BDT</span></span>';
                        })->implode('');
                    ?>
                    <td class="fee-types-column px-4 py-2.5"><div class="flex flex-col items-start gap-1">{!! $feeSummary ?: '<span class="text-slate-400">No fees</span>' !!}</div></td>
                    <td class="description-column px-4 py-2.5 text-slate-600">{{ $visit->description ?: 'N/A' }}</td>
                    <td class="px-4 py-2.5">@if($visit->attachment_path)<a href="{{ asset(str_starts_with($visit->attachment_path, 'uploads/') ? $visit->attachment_path : 'storage/' . $visit->attachment_path) }}" target="_blank" rel="noopener" title="Open attachment" aria-label="Open attachment" class="inline-flex h-10 w-10 items-center justify-center rounded bg-sky-500 text-white transition hover:bg-sky-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h8.25L18 7.5v12.75H6V3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V8h4M9 12h6M9 15h6"/></svg></a>@else<span class="text-slate-500">N/A</span>@endif</td>
                    <td class="px-4 py-2.5"><div class="flex items-center gap-2"><a href="{{ route('warehouse.daily-visits.show', $visit) }}" title="View visit details" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100" aria-label="View visit details"><x-warehouse.icon name="eye" class="h-4 w-4" /></a>@if($isSalesman)<a href="{{ route('warehouse.daily-visits.edit', $visit) }}" title="Edit visit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 transition hover:bg-sky-100" aria-label="Edit visit"><x-warehouse.icon name="square-pen" class="h-4 w-4" /></a><form method="POST" action="{{ route('warehouse.daily-visits.destroy', $visit) }}" onsubmit="return confirm('Delete this visit?')" class="m-0">@csrf @method('DELETE')<button type="submit" title="Delete visit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100" aria-label="Delete visit"><x-warehouse.icon name="trash-2" class="h-4 w-4" /></button></form>@endif</div></td>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="12" class="py-12 text-center text-slate-500">No daily visits found.</td></tr>
            <?php endif; ?>
                </tbody>
            </table>
        </div>
        @if($isPaginated)<div class="border-t border-slate-200 px-4 py-4 sm:px-6">{{ $visits->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>@endif
    </div>
</x-warehouse-layout>
