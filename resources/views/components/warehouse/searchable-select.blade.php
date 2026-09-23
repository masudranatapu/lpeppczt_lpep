@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search...',
    'required' => false,
])

@php
    $inputId = $id ?? $name ?? ('searchable-select-' . uniqid());
    $normalizedOptions = collect($options)->map(function ($option, $value) {
        if (is_array($option)) {
            return [
                'value' => (string) ($option['value'] ?? $value),
                'label' => (string) ($option['label'] ?? $option['value'] ?? $value),
                'description' => (string) ($option['description'] ?? ''),
            ];
        }

        return ['value' => (string) $value, 'label' => (string) $option, 'description' => ''];
    })->values();
@endphp

<div
    x-data="warehouseSearchableSelect({
        id: @js($inputId),
        initialValue: @js((string) ($selected ?? '')),
        placeholder: @js($placeholder),
        options: @js($normalizedOptions),
    })"
    @click.outside="open = false"
    @warehouse-searchable-select-reset.stop="reset()"
    @warehouse-searchable-select-reset.window="if ($event.detail.id === id) reset()"
    @warehouse-searchable-select-options.window="if ($event.detail.id === id) { options = $event.detail.options || []; value = $event.detail.selected ? String($event.detail.selected) : ''; search = ''; open = false; }"
    {{ $attributes->class('relative') }}
>
    @if ($label)
        <x-warehouse.input-label :for="$inputId . '-button'" :value="$label" class="mb-2 text-slate-700" />
    @endif

    <input
        type="hidden"
        id="{{ $inputId }}"
        @if($name) name="{{ $name }}" @endif
        x-model="value"
        @if($required) data-searchable-required="true" @endif
    >

    <button
        type="button"
        id="{{ $inputId }}-button"
        @click="open = !open; if (open) $nextTick(() => $refs.search.focus())"
        :aria-expanded="open"
        class="flex h-11 w-full items-center justify-between gap-3 rounded-xl border border-slate-300 bg-white px-4 text-left text-sm shadow-sm transition hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20"
    >
        <span class="min-w-0 truncate" :class="value ? 'font-semibold text-slate-900' : 'text-slate-400'" x-text="selectedLabel"></span>
        <svg class="h-4 w-4 shrink-0 text-slate-400 transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m5 7.5 5 5 5-5" /></svg>
    </button>

    <div x-cloak x-show="open" x-transition class="absolute z-[100] mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-100 p-2">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8.5" cy="8.5" r="5.5"/><path stroke-linecap="round" d="m13 13 4 4"/></svg>
                <input x-ref="search" x-model="search" type="search" placeholder="{{ $searchPlaceholder }}" class="h-10 w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-10 pr-3 text-sm text-slate-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
            </div>
        </div>
        <div class="max-h-64 overscroll-contain overflow-y-auto p-1.5" style="scrollbar-gutter: stable;">
            <template x-for="option in filteredOptions" :key="option.value">
                <button type="button" @click="choose(option)" class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-left transition hover:bg-red-50" :class="value === option.value ? 'bg-red-50 text-red-700' : 'text-slate-700'">
                    <span class="min-w-0"><span class="block truncate text-sm font-semibold" x-text="option.label"></span><span x-show="option.description" class="mt-0.5 block truncate text-xs text-slate-400" x-text="option.description"></span></span>
                    <svg x-show="value === option.value" class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="m4 10 3.5 3.5L16 5" /></svg>
                </button>
            </template>
            <p x-show="filteredOptions.length === 0" class="m-0 px-3 py-6 text-center text-sm text-slate-500">No matching options found.</p>
        </div>
    </div>

    @if($name)
        @error($name)<x-warehouse.input-error :messages="$message" class="mt-2" />@enderror
    @endif
</div>

@once
    <script>
        window.warehouseSearchableSelect = function (config) {
            return {
                id: config.id,
                open: false,
                search: '',
                value: String(config.initialValue || ''),
                placeholder: config.placeholder,
                options: config.options || [],
                get selectedLabel() {
                    const selected = this.options.find(option => String(option.value) === String(this.value));
                    return selected ? selected.label : this.placeholder;
                },
                get filteredOptions() {
                    const query = this.search.trim().toLowerCase();
                    if (!query) return this.options;
                    return this.options.filter(option => `${option.label} ${option.description}`.toLowerCase().includes(query));
                },
                choose(option) {
                    this.value = String(option.value);
                    this.open = false;
                    this.search = '';
                    this.$dispatch('warehouse-searchable-select-change', { id: this.id, value: this.value, label: option.label });
                },
                reset() {
                    this.value = '';
                    this.search = '';
                    this.open = false;
                },
            };
        };
    </script>
@endonce
