@props([
    'minWidth' => 'min-w-[1100px]',
    'tableClass' => 'w-full divide-y divide-slate-200',
    'headClass' => 'bg-slate-100',
    'bodyClass' => 'divide-y divide-slate-200 bg-white',
    'bodyId' => null,
    'wrapperClass' => '',
    'overflow' => true,
])

<div class="{{ trim(($overflow ? 'overflow-x-auto ' : '') . $wrapperClass) }}">
    <table {{ $attributes->class([$minWidth, $tableClass]) }}>
        @isset($header)
            <thead class="{{ $headClass }}">
                {{ $header }}
            </thead>
        @endisset

        <tbody @if($bodyId) id="{{ $bodyId }}" @endif class="{{ $bodyClass }}">
            {{ $slot }}
        </tbody>

        @isset($footer)
            <tfoot>
                {{ $footer }}
            </tfoot>
        @endisset
    </table>
</div>
