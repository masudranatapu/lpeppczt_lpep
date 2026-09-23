@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'disabled' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div>
    @if ($label)
        <x-warehouse.input-label :for="$id" :value="$label" class="mb-2 text-slate-700" />
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @disabled($disabled)
        {{ $attributes->class('block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-500/20') }}
    >
        @if (!is_null($placeholder))
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            @php
                $value = $optionValue;

                if (is_int($optionValue)) {
                    $value = $optionLabel;
                }
            @endphp

            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
