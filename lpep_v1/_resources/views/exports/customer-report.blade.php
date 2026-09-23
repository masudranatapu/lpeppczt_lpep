<table>
    <thead>
        <tr>
            <th colspan="8" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Customer Report
            </th>
        </tr>
        <tr>
            <th colspan="8" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[0] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[1] }}
            </th>
            {{-- <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[2] }}
            </th> --}}
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[3] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[4] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[5] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[6] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[7] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.coreport')[8] }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['name'] }}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business']['name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">{{ $item['supplier_business_name'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['mobile'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['sale_count'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_sale'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_paying_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['due'] }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                Total
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('sale_count') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_sale') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_paying_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('due') }}
            </td>
        </tr>
    </tfoot>
</table>
