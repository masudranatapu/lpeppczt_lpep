<x-warehouse-layout title="Area Manager Daily Report">
    @php($exportQuery = ['month' => $month])
    <div class="space-y-5">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header title="Area Manager Daily Report" description="Daily earning of every LSP at {{ $warehouse->name }}, generated from LSP sales.">
                <x-slot name="actions">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('warehouse.reports.area-manager-daily.pdf', $exportQuery) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">PDF</a>
                        <a href="{{ route('warehouse.reports.area-manager-daily.excel', $exportQuery) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">Excel</a>
                    </div>
                </x-slot>
            </x-warehouse.page-header>

            <form method="GET" class="flex flex-wrap items-end gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
                <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Month
                    <input type="month" name="month" value="{{ $month }}" required class="mt-1 block h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal">
                </label>
                <x-loading-submit type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white" loading-text="Loading...">Show</x-loading-submit>
                <span class="text-xs text-slate-500">Daily target: <strong>{{ number_format($target) }} BDT</strong> (set by Admin in Area Office settings)</span>
            </form>

            <div class="overflow-x-auto p-4 sm:p-6">
                <div class="min-w-[1000px]">
                    @include('warehouse-portal.reports.partials.area-manager-daily-sheet')
                </div>
            </div>
        </section>
    </div>
</x-warehouse-layout>
