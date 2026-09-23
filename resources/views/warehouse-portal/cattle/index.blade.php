<x-warehouse-layout title="Cattle">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div><h1 class="m-0 text-xl font-extrabold text-slate-900">Cattle</h1><p class="mb-0 mt-1 text-sm text-slate-500">Cattle records for your farms.</p></div>
            <a href="{{ route('warehouse.cattle.create') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-700">+ Add Cattle</a>
        </div>

        @if(session('message'))<div class="m-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">{{ session('message') }}</div>@endif

        <form method="GET" class="border-b border-slate-200 bg-slate-50 p-4 sm:px-5"><div class="flex flex-col gap-3 sm:flex-row"><select name="per_page" class="h-11 rounded-xl border border-slate-300 bg-white px-3 text-sm">@foreach(['10','25','50','100'] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>@endforeach</select><input name="search" value="{{ $search }}" placeholder="Search tag, name or farm" class="h-11 flex-1 rounded-xl border border-slate-300 bg-white px-4 text-sm"><button class="rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white">Search</button><a href="{{ route('warehouse.cattle') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700">Reset</a></div></form>

<div class="overflow-x-auto"><table class="min-w-[620px] w-full text-left text-sm"><thead class="bg-emerald-700 text-xs font-bold uppercase tracking-wide text-white"><tr><th class="px-5 py-3">SL</th><th class="px-5 py-3">Farm</th><th class="px-5 py-3">Tag</th><th class="px-5 py-3">Name</th><th class="px-5 py-3">Action</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($cattle as $item)<tr class="hover:bg-emerald-50/40"><td class="px-5 py-3">{{ $cattle->firstItem() + $loop->index }}</td><td class="px-5 py-3">{{ $item->farm?->name ?: '-' }}</td><td class="px-5 py-3 font-semibold text-slate-900">{{ $item->tag }}</td><td class="px-5 py-3">{{ $item->name }}</td><td class="px-5 py-3 text-slate-400">-</td></tr>@empty<tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">No cattle found.</td></tr>@endforelse</tbody></table></div>
        <div class="border-t border-slate-200 px-4 py-4 sm:px-5">{{ $cattle->onEachSide(1)->links('warehouse-portal.partials.pagination') }}</div>
    </div>
</x-warehouse-layout>
