@php($span = count($columns) + 4)
@php($lspCount = max(count($lsps), 1))
<table>
    <tr><th colspan="{{ $span }}" style="text-align:center;font-size:14px;font-weight:bold;">LPEP Renewable Energy Bangladesh Ltd.</th></tr>
    <tr><th colspan="{{ $span }}" style="text-align:center;font-weight:bold;">Area Manager Daily Earning Report</th></tr>
    <tr><td colspan="{{ $span }}" style="text-align:center;">{{ $areaLabel }}</td></tr>
    <tr><td colspan="{{ $span }}" style="text-align:right;font-weight:bold;">Month: {{ $monthLabel }}</td></tr>
    <tr>
        <th style="background:#f4b183;font-weight:bold;border:1px solid #000000;">Date</th>
        <th style="background:#f4b183;font-weight:bold;border:1px solid #000000;">Name</th>
        @foreach($columns as $label)<th style="background:#f4b183;font-weight:bold;border:1px solid #000000;">{{ $label }}</th>@endforeach
        <th style="background:#f4b183;font-weight:bold;border:1px solid #000000;">Total</th>
        <th style="background:#f4b183;font-weight:bold;border:1px solid #000000;">Summary</th>
    </tr>
    @foreach($days as $day)
        @foreach($day['rows'] as $i => $row)
            <tr>
                @if($i === 0)
                    <td rowspan="{{ $lspCount }}" style="border:1px solid #000000;font-weight:bold;vertical-align:middle;">{{ $day['date']->format('d.m.y') }}</td>
                @endif
                <td style="border:1px solid #000000;">{{ $row['name'] }}</td>
                @foreach($columns as $key => $label)<td style="border:1px solid #000000;">{{ $row[$key] ?: '' }}</td>@endforeach
                <td style="border:1px solid #000000;font-weight:bold;">{{ $row['total'] ?: '' }}</td>
                @if($i === 0)
                    <td rowspan="{{ $lspCount }}" style="border:1px solid #000000;vertical-align:top;">Total Target: {{ round($day['target']) }}<br>Total Sales: {{ round($day['sales']) }}<br>Treat &amp; Ot: {{ round($day['treatment']) }}<br>% Achievement: {{ number_format($day['achievement'], 2) }}%<br>Average/Achiev: {{ number_format($day['average_achievement'], 2) }}%</td>
                @endif
            </tr>
        @endforeach
    @endforeach
    @if(count($days))
        <tr>
            <td colspan="2" style="border:1px solid #000000;font-weight:bold;background:#fde9d9;">Month Total</td>
            @foreach($columns as $key => $label)<td style="border:1px solid #000000;font-weight:bold;background:#fde9d9;">{{ round($monthTotals[$key], 2) }}</td>@endforeach
            <td style="border:1px solid #000000;font-weight:bold;background:#fde9d9;">{{ round($monthTotals['total'], 2) }}</td>
            <td style="border:1px solid #000000;font-weight:bold;background:#fde9d9;">Target: {{ round($monthTarget) }}<br>Achievement: {{ number_format($monthAchievement, 2) }}%</td>
        </tr>
    @endif
    <tr><td colspan="{{ $span }}"></td></tr>
    <tr><td colspan="{{ $span }}"></td></tr>
    <tr><td colspan="3" style="font-weight:bold;">Signature Of Area Manager</td></tr>
</table>
