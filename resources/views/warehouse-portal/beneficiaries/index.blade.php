<x-warehouse-layout title="Beneficiaries">
    <div class="space-y-6">
        @if(session('error'))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-800">{{ session('error') }}</div>
        @endif
        @if(session('message'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('message') }}</div>
        @endif
        @if(!($createOnly ?? false))
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <x-warehouse.page-header title="Beneficiary" description="Manage beneficiaries, livestock and service information.">
                <x-slot name="actions">
                    <a href="{{ route('warehouse.beneficiaries.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500">
                        <span class="text-lg leading-none">+</span> Add New Customer
                    </a>
                </x-slot>
            </x-warehouse.page-header>

            <form method="GET" class="border-t border-slate-200 bg-slate-50 p-4 sm:px-6">
                <div class="grid gap-3 md:grid-cols-5">
                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Beneficiary Number
                        <select name="beneficiary_number" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal normal-case">
                            <option value="">Select Beneficiary Number</option>
                            @foreach($beneficiaryNumbers as $number)<option value="{{ $number }}" @selected((string) $beneficiaryNumber === (string) $number)>{{ $number }}</option>@endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Group Number
                        <select name="group_number" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal normal-case">
                            <option value="">Select Group Number</option>
                            @foreach($groupNumbers as $number)<option value="{{ $number }}" @selected((string) $groupNumber === (string) $number)>Group {{ $number }}</option>@endforeach
                        </select>
                    </label>
                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Search
                        <input name="search" value="{{ $search }}" placeholder="Name, mobile, village..." class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal normal-case">
                    </label>
                    <div class="flex items-end gap-2"><x-loading-submit type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white" loading-text="Filtering...">Filter</x-loading-submit><x-warehouse.button href="{{ route('warehouse.beneficiaries.index') }}" variant="secondary" class="h-10 px-4">Reset</x-warehouse.button></div>
                </div>
            </form>

            <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-6">
                <div class="flex items-center gap-2 text-sm text-slate-600">Show <form method="GET" class="inline"><input type="hidden" name="salesman_id" value="{{ $salesmanId }}"><input type="hidden" name="beneficiary_number" value="{{ $beneficiaryNumber }}"><input type="hidden" name="group_number" value="{{ $groupNumber }}"><input type="hidden" name="search" value="{{ $search }}"><select name="per_page" onchange="this.form.submit()" class="h-9 rounded-lg border border-slate-300 bg-white px-2"><option value="10" @selected($perPage === '10')>10</option><option value="25" @selected($perPage === '25')>25</option><option value="50" @selected($perPage === '50')>50</option><option value="100" @selected($perPage === '100')>100</option><option value="all" @selected($perPage === 'all')>All</option></select></form> entries</div>
                <div class="text-sm text-slate-500">Total: <strong class="text-slate-800">{{ $totalCustomers }}</strong> beneficiaries</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1500px] divide-y divide-slate-200 text-xs">
                    <thead class="bg-emerald-700 text-left font-bold uppercase tracking-wide text-white">
                        <tr>
                            <th class="px-3 py-3">SL</th><th class="px-3 py-3">Beneficiary Cell Number</th><th class="px-3 py-3">Beneficiary Name</th><th class="px-3 py-3">Beneficiary Number</th><th class="px-3 py-3">Beneficiary Group Number</th><th colspan="2" class="px-3 py-3 text-center">Address</th><th colspan="5" class="px-3 py-3 text-center">Beneficiary Livestock Details</th><th colspan="7" class="px-3 py-3 text-center">Services</th><th class="px-3 py-3">Action</th>
                        </tr>
                        <tr class="bg-emerald-800"><th></th><th></th><th></th><th></th><th></th><th class="px-3 py-2">Village</th><th class="px-3 py-2">Unions</th><th class="px-3 py-2">Cow</th><th class="px-3 py-2">Bull</th><th class="px-3 py-2">Bakna</th><th class="px-3 py-2">Goat</th><th class="px-3 py-2">Khasi</th><th class="px-3 py-2">Membership</th><th class="px-3 py-2">Deworming</th><th class="px-3 py-2">Bringing</th><th class="px-3 py-2">Fattening</th><th class="px-3 py-2">Treatment</th><th class="px-3 py-2">AI</th><th class="px-3 py-2">Medicine</th><th></th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($customers as $customer)
                            <tr class="transition hover:bg-emerald-50/40">
                                <td class="px-3 py-3">{{ method_exists($customers, 'firstItem') ? $customers->firstItem() + $loop->index : $loop->iteration }}</td><td class="px-3 py-3">{{ $customer->mobile ?: 'No Number' }}</td><td class="px-3 py-3 font-semibold text-slate-900">{{ $customer->name }}</td><td class="px-3 py-3">{{ $customer->beneficiary_number }}</td><td class="px-3 py-3">{{ $customer->group_number }}</td><td class="px-3 py-3">{{ $customer->village }}</td><td class="px-3 py-3">{{ $customer->unions }}</td><td class="px-3 py-3">{{ $customer->cow ?: 0 }}</td><td class="px-3 py-3">{{ $customer->bull ?: 0 }}</td><td class="px-3 py-3">{{ $customer->bakna ?: 0 }}</td><td class="px-3 py-3">{{ $customer->goat ?: 0 }}</td><td class="px-3 py-3">{{ $customer->khasi ?: 0 }}</td><td class="px-3 py-3">{{ $customer->membership }}</td><td class="px-3 py-3">{{ $customer->deworming }}</td><td class="px-3 py-3">{{ $customer->bringing }}</td><td class="px-3 py-3">{{ $customer->fattening }}</td><td class="px-3 py-3">{{ $customer->treatment }}</td><td class="px-3 py-3">{{ $customer->ai }}</td><td class="px-3 py-3">{{ $customer->medicine }}</td><td class="px-3 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('warehouse.beneficiaries.edit', $customer) }}" title="Edit beneficiary" aria-label="Edit beneficiary" class="mr-2 inline-flex h-8 w-8 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 transition hover:bg-sky-100"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.651 3.651M4 20h4l10.5-10.5a2.586 2.586 0 0 0-3.657-3.657L4.343 16.343 4 20Z"/></svg></a>
                                    <form method="POST" action="{{ route('warehouse.beneficiaries.destroy', $customer) }}" class="inline" onsubmit="return confirm('Delete this beneficiary?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete beneficiary" aria-label="Delete beneficiary" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16m-10 4v5m4-5v5M9 7V4h6v3m-9 0 1 13h8l1-13"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="20" class="px-5 py-12 text-center text-slate-500">No beneficiaries found.</td></tr>
                        @endforelse
                        @if($customers->count())<tr class="bg-slate-100 font-bold text-slate-800"><td colspan="7" class="px-3 py-3 text-right">Total</td><td>{{ $livestockTotals->cow }}</td><td>{{ $livestockTotals->bull }}</td><td>{{ $livestockTotals->bakna }}</td><td>{{ $livestockTotals->goat }}</td><td>{{ $livestockTotals->khasi }}</td><td colspan="8"></td></tr>@endif
                    </tbody>
                </table>
            </div>
            @if(method_exists($customers, 'links'))
                <div class="border-t border-slate-100 px-4 py-3 sm:px-6">{{ $customers->links('warehouse-portal.partials.pagination') }}</div>
            @endif
        </section>
        @endif

        @if(($createOnly ?? false) || $editing)
        <section id="beneficiary-form" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <div>
                    <h1 class="m-0 text-xl font-extrabold text-slate-900">{{ $editing ? 'Edit Customer' : 'Add New Customer' }}</h1>
                    <p class="mb-0 mt-1 text-sm text-slate-500">Beneficiary details and service history for this Area Office.</p>
                </div>
                <a href="{{ route('warehouse.beneficiaries.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">← Back</a>
            </div>

            <form method="POST" action="{{ $editing ? route('warehouse.beneficiaries.update', $editing) : route('warehouse.beneficiaries.store') }}" class="p-5 sm:p-7">
                @csrf
                @if($editing) @method('PUT') @endif

                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Please correct the highlighted customer information.</div>
                @endif

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <input type="hidden" name="salesman_id" value="{{ old('salesman_id', $editing?->agent_id ?: $salesmen->first()?->user_id) }}">
                    <label class="block text-sm font-semibold text-slate-700">Beneficiary Name *
                        <input name="name" required value="{{ old('name', $editing?->name) }}" placeholder="Enter Name" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm @error('name') border-rose-500 @enderror">
                        @error('name')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Beneficiary Cell Number *
                        <input name="mobile" required value="{{ old('mobile', $editing?->mobile) }}" placeholder="Enter Mobile No." class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm @error('mobile') border-rose-500 @enderror">
                        @error('mobile')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Beneficiary Number *
                        <select name="beneficiary_number" required class="mt-2 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm @error('beneficiary_number') border-rose-500 @enderror">
                            <option value="">Select Beneficiary Number</option>
                            @foreach($beneficiaryNumbers as $number)
                                <option value="{{ $number }}" {{ (string) old('beneficiary_number', $editing?->beneficiary_number) === (string) $number ? 'selected' : '' }}>{{ $number }}</option>
                            @endforeach
                        </select>
                        @error('beneficiary_number')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Beneficiary Group Number *
                        <select name="group_number" id="gnumber" required class="mt-2 h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm @error('group_number') border-rose-500 @enderror">
                            <option value="">Select Group Number</option>
                            @foreach($groupNumbers as $number)
                                <option value="{{ $number }}" {{ (string) old('group_number', $editing?->group_number) === (string) $number ? 'selected' : '' }}>{{ $number }}</option>
                            @endforeach
                        </select>
                        @error('group_number')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Email
                        <input name="email" type="email" value="{{ old('email', $editing?->email ?? ($warehouseEmail ?? '')) }}" placeholder="Email address" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm @error('email') border-rose-500 @enderror">
                        @error('email')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Password {{ $editing ? '' : '*' }}
                        <input name="password" type="password" {{ $editing ? '' : 'required' }} placeholder="{{ $editing ? 'Leave blank to keep current password' : 'Enter password' }}" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm @error('password') border-rose-500 @enderror">
                        @error('password')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Village
                        <input name="village" value="{{ old('village', $editing?->village) }}" placeholder="Enter village" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                    </label>
                    <label class="block text-sm font-semibold text-slate-700">Union
                        <input name="union" value="{{ old('union', $editing?->unions) }}" placeholder="Enter Union" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                    </label>
                </div>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <h2 class="m-0 text-base font-extrabold text-slate-900">Number of Livestock</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        @foreach(['cow' => 'Cow', 'bull' => 'Bull', 'bakna' => 'Bakna', 'goat' => 'Goat', 'khasi' => 'Khasi'] as $field => $label)
                            <label class="block text-sm font-semibold text-slate-700">{{ $label }}
                                <input name="{{ $field }}" type="number" min="0" value="{{ old($field, $editing?->{$field}) }}" placeholder="Enter number of {{ strtolower($label) }}s" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-200 pt-6">
                    <h2 class="m-0 text-base font-extrabold text-slate-900">Memo No. of Services Provided</h2>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach(['membership' => 'Membership', 'deworming' => 'Deworming / Vaccination', 'bringing' => 'Bringing to the Fore', 'fattening' => 'Fattening', 'treatment' => 'Treatment & Other', 'ai' => 'Artificial Insemination (AI)', 'medicine' => 'Medicine'] as $field => $label)
                            <label class="block text-sm font-semibold text-slate-700">{{ $label }}
                                <input name="{{ $field }}" value="{{ old($field, $editing?->{$field}) }}" placeholder="Enter text of {{ strtolower($label) }}" class="mt-2 h-11 w-full rounded-xl border border-slate-300 px-3 text-sm">
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    @if($editing)<a href="{{ route('warehouse.beneficiaries.index') }}" class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a>@endif
                    <x-loading-submit type="submit" class="inline-flex justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-500" loading-text="Saving...">{{ $editing ? 'Update Customer' : 'Save Customer' }}</x-loading-submit>
                </div>
            </form>
        </section>
        @endif
    </div>
</x-warehouse-layout>
