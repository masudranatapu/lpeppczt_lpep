<x-warehouse-layout title="Import Beneficiaries">
    <div class="space-y-6">
        @if($report)
            <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-7">
                    <h2 class="m-0 text-base font-extrabold text-slate-900">Import Result @if(!empty($report['lsp']))<span class="font-semibold text-slate-500">— {{ $report['lsp'] }}</span>@endif</h2>
                </div>
                <div class="grid gap-4 p-5 sm:grid-cols-3 sm:px-7">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3"><div class="text-xs font-bold uppercase tracking-wide text-emerald-700">New</div><div class="mt-1 text-2xl font-extrabold text-emerald-800">{{ $report['created'] }}</div></div>
                    <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3"><div class="text-xs font-bold uppercase tracking-wide text-sky-700">Updated / Moved</div><div class="mt-1 text-2xl font-extrabold text-sky-800">{{ $report['updated'] }}</div></div>
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3"><div class="text-xs font-bold uppercase tracking-wide text-amber-700">Skipped</div><div class="mt-1 text-2xl font-extrabold text-amber-800">{{ $report['skipped'] }}</div></div>
                </div>
                @if(count($report['errors']))
                    <div class="overflow-x-auto border-t border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                <tr><th class="px-4 py-3">Excel Row</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Reason</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($report['errors'] as $error)
                                    <tr><td class="px-4 py-2">{{ $error['row'] }}</td><td class="px-4 py-2">{{ $error['name'] }}</td><td class="px-4 py-2 text-rose-700">{{ $error['message'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($report['skipped'] > count($report['errors']))
                        <p class="border-t border-slate-200 px-5 py-3 text-xs text-slate-500 sm:px-7">Showing the first {{ count($report['errors']) }} of {{ $report['skipped'] }} skipped rows.</p>
                    @endif
                @endif
            </section>
        @endif

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header title="Import Beneficiaries" description="Upload the beneficiary Excel file exported from the admin panel.">
                <x-slot name="actions">
                    <div class="flex flex-wrap gap-2">
                        <x-warehouse.button href="{{ route('warehouse.beneficiaries.import.template') }}" variant="secondary">Download Template</x-warehouse.button>
                        <x-warehouse.button href="{{ route('warehouse.beneficiaries.index') }}" variant="secondary">← Back</x-warehouse.button>
                    </div>
                </x-slot>
            </x-warehouse.page-header>

            <form method="POST" action="{{ route('warehouse.beneficiaries.import.store') }}" enctype="multipart/form-data" class="p-5 sm:p-7">
                @csrf

                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
                @endif

                <div class="grid gap-5 md:grid-cols-2">
                    @if(!$isSalesman)
                        <label class="block text-sm font-semibold text-slate-700">Assign to LSP *
                            <select name="salesman_id" required class="mt-2 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm @error('salesman_id') border-rose-500 @enderror">
                                <option value="">Select LSP</option>
                                @foreach($salesmen as $lsp)
                                    <option value="{{ $lsp->user_id }}" {{ (string) old('salesman_id') === (string) $lsp->user_id ? 'selected' : '' }}>{{ $lsp->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif
                    <label class="block text-sm font-semibold text-slate-700">Excel File (.xlsx, .xls, .csv) *
                        <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-emerald-700 @error('file') border-rose-500 @enderror">
                    </label>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    <p class="m-0 font-bold text-slate-800">How the import works</p>
                    <ul class="mb-0 mt-2 list-disc space-y-1 pl-5">
                        <li>A row with an existing <strong>ID</strong> updates that beneficiary and moves it to the selected LSP. Its farms, cattle, calves, sales and visit history stay linked.</li>
                        <li><strong>Never delete or change the ID column.</strong> A file without the ID column is rejected. A row whose ID belongs to a different person (name and mobile both differ) is skipped.</li>
                        <li>A row with an empty ID creates a new beneficiary, unless someone with the same name and mobile already exists under any agent or LSP; that row is skipped so no copy is made.</li>
                        <li>Name and Mobile are always required. New beneficiaries also need Beneficiary Number (1–40) and Group Number (1–28).</li>
                        <li>A group can have at most 40 members per LSP; extra rows are skipped.</li>
                        <li>The Agent ID and Agent Name columns are ignored.</li>
                    </ul>
                </div>

                <div class="mt-6 flex justify-end border-t border-slate-200 pt-5">
                    <x-loading-submit type="submit" class="inline-flex justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-500" loading-text="Importing...">Import Beneficiaries</x-loading-submit>
                </div>
            </form>
        </section>
    </div>
</x-warehouse-layout>
