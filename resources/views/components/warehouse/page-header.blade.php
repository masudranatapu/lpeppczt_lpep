@props([
    'title',
    'description' => null,
    'backHref' => null,
    'backLabel' => 'Back',
    'backIcon' => 'arrow-left',
    'backVariant' => 'secondary',
    'wrapperClass' => 'border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.15),_transparent_34%),linear-gradient(135deg,_#ffffff_0%,_#fff1f2_100%)] px-4 py-5 sm:px-6',
    'contentClass' => 'flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between',
    'titleClass' => 'mt-2 text-lg font-bold tracking-tight text-slate-900 sm:text-2xl',
    'descriptionClass' => 'mt-2 text-sm text-slate-600',
])

<div {{ $attributes->class([$wrapperClass]) }}>
    @if ($backHref)
        <div class="flex items-center justify-between gap-4">
            <x-warehouse.button
                :href="$backHref"
                :variant="$backVariant"
                class="rounded-xl border-red-200 bg-white text-red-700 hover:border-red-300 hover:bg-red-50"
                :icon="$backIcon"
            >
                {{ $backLabel }}
            </x-warehouse.button>

            <div class="ml-auto text-right">
                <h1 class="{{ $titleClass }}">
                    {{ $title }}
                </h1>

                @if ($description)
                    <p class="{{ $descriptionClass }}">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        @if (isset($actions))
            <div class="mt-4 flex justify-end">
                {{ $actions }}
            </div>
        @endif
    @else
        <div class="{{ $contentClass }}">
            <div>
                <h1 class="{{ $titleClass }}">
                    {{ $title }}
                </h1>

                @if ($description)
                    <p class="{{ $descriptionClass }}">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if (isset($actions))
                <div>
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
</div>
