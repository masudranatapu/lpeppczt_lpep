<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Purchase Report
            </th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[1] }}
            </th>
            <th style="width: 12px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[2] }}
            </th>
            {{-- <th style="width: 16px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[3] }}
            </th> --}}
            <th style="width: 16px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[4] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[5] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[6] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[7] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[8] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[9] }}
            </th>
            <th style="width: 12px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.pureport')[10] }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['purchase_date'] }}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business']['name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">{{ $item['invoice_no'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['supplier']['name'] }}</td>
                <td style="text-align: center; vertical-align: center;">{!! $item['purchase_product_names'] !!}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_pay'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['due'] }}</td>
                <td style="text-align: center; vertical-align: center;">{!! $item['payment_status'] !!}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="4"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">Total</td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_pay') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('due') }}
            </td>
            <td></td>
        </tr>
    </tfoot>
</table>
