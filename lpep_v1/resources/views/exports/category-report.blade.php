<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Category Report
            </th>
        </tr>
        <tr>
            <th colspan="7" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[1] }}
            </th>
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[2] }}
            </th>
            {{-- <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[3] }}
            </th> --}}
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[4] }}
            </th>
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[5] }}
            </th>
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[6] }}
            </th>
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[7] }}
            </th>
            <th style="width: 20px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.creport')[8] }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                <td style="text-align: center; vertical-align: center;">{!! $item['category_name'] !!}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business_name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">{{ $item['final_purchase_quantity'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['final_sold_quantity'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['final_purchase_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['final_sold_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['margin'] }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="1"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">Total</td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('final_purchase_quantity') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('final_purchase_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('final_sold_quantity') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('final_sold_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('margin') }}
            </td>
        </tr>
    </tfoot>
</table>
