<table>
    <thead>
        <tr>
            <th colspan="6" style="font-size: 16px; height: 22px; text-align: center; vertical-align: center;">
                Product Wise Sold Report
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 12px; height: 18px; text-align: center; vertical-align: center;">
                Time: {{ date('Y-m-d') }}, {{ date('h:i a') }}
            </th>
        </tr>
        <tr>
            <th style="width: 6px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sl') }}
            </th>
            {{-- <th style="width: 22px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.branch1') }}
            </th> --}}
            <th style="width: 26px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.product_name') }}
            </th>
            <th style="width: 26px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sellingqty') }}
            </th>
            <th style="width: 26px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.sellingprice') }}
            </th>
            <th style="width: 26px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.purchaseprice') }}
            </th>
            <th style="width: 26px; font-weight: bold; text-align: center; vertical-align: center;">
                {{ __('page.profit_loss') }}
            </th>
        </tr>
    </thead>

    <tbody>
        @foreach ($collection as $item)
            <tr>
                <td style="text-align: center; vertical-align: center;">{{ $item['DT_RowIndex'] }}</td>
                {{-- <td style="text-align: center; vertical-align: center;">{{ $item['business_name'] }}</td> --}}
                <td style="text-align: center; vertical-align: center;">{!! $item['product_name'] !!}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['final_sold_quantity'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_selling_price'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['total_purchase_price'] }}</td>
                <td style="text-align: center; vertical-align: center;">{{ $item['margin'] }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="1"></td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">Total</td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('final_sold_quantity') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_selling_price') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('total_purchase_price') }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: center;">
                {{ $collection->sum('margin') }}
            </td>
        </tr>
    </tfoot>
</table>
