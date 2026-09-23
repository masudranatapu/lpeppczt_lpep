<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Product Report
            </th>
        </tr>
        <tr>
            <th colspan="7" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[1] }}
            </th>
            <th style="width: 24px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[2] }}
            </th>
            {{-- <th style="width: 17px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[3] }}
            </th> --}}
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[4] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[5] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.return') }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[6] }}
            </th>
            <th style="width: 18px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.preport')[7] }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                <td style="text-align: center; vertical-align: center;">{!! $item['product_name'] !!}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business']['name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">
                    {{ '(' . $item['final_purchase_quantity'] . ') ' . $item['final_purchase_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">
                    {{ '(' . $item['final_sold_quantity'] . ') ' . $item['final_sold_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">
                    {{ '(' . $item['final_sold_return_quantity'] . ') ' . $item['final_sold_return_amount'] }}
                </td>
                <td style="text-align: center; vertical-align: center;">{{ $item['margin_amount'] }}</td>
                <td style="text-align: center; vertical-align: center;">
                    {{ '(' . $item['margin_quantity'] . ') ' . $item['margin_selling_price'] }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="1"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">Total</td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ "({$collection->sum('final_purchase_quantity')}) {$collection->sum('final_purchase_amount')}" }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ "({$collection->sum('final_sold_quantity')}) {$collection->sum('final_sold_amount')}" }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ "({$collection->sum('final_sold_return_quantity')}) {$collection->sum('final_sold_return_amount')}" }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('margin_amount') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ "({$collection->sum('margin_quantity')}) {$collection->sum('margin_selling_price')}" }}
            </td>
        </tr>
    </tfoot>
</table>
