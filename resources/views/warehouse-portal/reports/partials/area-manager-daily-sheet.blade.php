@php
    $fmt = fn ($value) => abs($value) < 0.005 ? '' : (fmod(round($value, 2), 1) == 0 ? number_format($value) : number_format($value, 2));
    $money = fn ($value) => number_format(round($value), 0) . ' BDT';
    $lspCount = max(count($lsps), 1);
@endphp
<style>
    .amr-sheet { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 11px; }
    .amr-head { text-align: center; margin-bottom: 6px; }
    .amr-company { font-size: 17px; font-weight: bold; margin: 0; }
    .amr-title { font-size: 13px; font-weight: bold; margin: 2px 0 0; }
    .amr-area { font-size: 11px; margin: 2px 0 0; }
    .amr-month { text-align: right; font-weight: bold; font-size: 11px; margin: 0 0 3px; }
    .amr-table { width: 100%; border-collapse: collapse; }
    .amr-table th, .amr-table td { border: 1px solid #4b5563; padding: 4px 5px; }
    .amr-table th { background: #f4b183; font-weight: bold; text-align: center; font-size: 11px; }
    .amr-table td.amr-num { text-align: right; white-space: nowrap; }
    .amr-table td.amr-date { text-align: center; font-weight: bold; vertical-align: middle; white-space: nowrap; }
    .amr-table td.amr-name { white-space: nowrap; }
    .amr-table td.amr-total { font-weight: bold; }
    .amr-table td.amr-summary { vertical-align: top; font-size: 10.5px; line-height: 1.9; white-space: nowrap; }
    .amr-table tr.amr-gap td { border-left: 0; border-right: 0; padding: 3px; }
    .amr-table tr.amr-month-total td { background: #fde9d9; font-weight: bold; }
    .amr-sign { margin-top: 40px; font-weight: bold; }
    .amr-sign span { display: inline-block; border-top: 1px solid #111827; padding-top: 4px; min-width: 190px; }
    .amr-empty { padding: 18px; text-align: center; color: #6b7280; }
</style>
<div class="amr-sheet">
    <div class="amr-head">
        <p class="amr-company">LPEP Renewable Energy Bangladesh Ltd.</p>
        <p class="amr-title">Area Manager Daily Earning Report</p>
        <p class="amr-area">{{ $areaLabel }}</p>
    </div>
    <p class="amr-month">Month: {{ $monthLabel }}</p>

    <table class="amr-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                @foreach($columns as $label)<th>{{ $label }}</th>@endforeach
                <th>Total</th>
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
            @forelse($days as $day)
                @foreach($day['rows'] as $i => $row)
                    <tr>
                        @if($i === 0)
                            <td class="amr-date" rowspan="{{ $lspCount }}">{{ $day['date']->format('d.m.y') }}</td>
                        @endif
                        <td class="amr-name">{{ $row['name'] }}</td>
                        @foreach($columns as $key => $label)<td class="amr-num">{{ $fmt($row[$key]) }}</td>@endforeach
                        <td class="amr-num amr-total">{{ $fmt($row['total']) }}</td>
                        @if($i === 0)
                            <td class="amr-summary" rowspan="{{ $lspCount }}">
                                Total Target: <b>{{ $money($day['target']) }}</b><br>
                                Total Sales: <b>{{ $money($day['sales']) }}</b><br>
                                Treat &amp; Ot: <b>{{ $money($day['treatment']) }}</b><br>
                                % Achievement: <b>{{ number_format($day['achievement'], 2) }}%</b><br>
                                Average/Achiev: <b>{{ number_format($day['average_achievement'], 2) }}%</b>
                            </td>
                        @endif
                    </tr>
                @endforeach
                @if(!$loop->last)
                    <tr class="amr-gap"><td colspan="{{ count($columns) + 4 }}"></td></tr>
                @endif
            @empty
                <tr><td class="amr-empty" colspan="{{ count($columns) + 4 }}">No working days in this month yet.</td></tr>
            @endforelse
            @if(count($days))
                <tr class="amr-month-total">
                    <td colspan="2">Month Total</td>
                    @foreach($columns as $key => $label)<td class="amr-num">{{ $fmt($monthTotals[$key]) }}</td>@endforeach
                    <td class="amr-num">{{ $fmt($monthTotals['total']) }}</td>
                    <td class="amr-summary">
                        Target: <b>{{ $money($monthTarget) }}</b><br>
                        Achievement: <b>{{ number_format($monthAchievement, 2) }}%</b>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="amr-sign"><span>Signature Of Area Manager</span></div>
</div>
