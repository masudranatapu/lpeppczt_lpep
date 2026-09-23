<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Sale Report
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[0] }}
            </th>
            <th style="width: 16px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[1] }}
            </th>
            {{-- <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[2] }}
            </th> --}}
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[3] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[4] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[5] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[6] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[7] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sreport')[8] }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['sale_date'] }}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business_name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">{{ $item['invoice'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['customer_name'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['paying_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['due'] }}</td>
                <td style="text-align: center; vertical-align: center;">{!! $item['payment_status'] !!}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">Total</td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('paying_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('due') }}
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
