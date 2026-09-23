<!doctype html>
<html lang="en">

<head>
    <title>LPEP</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <style type="text/css">
        *::before,
        *::after {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'Share-Regular';
            src: url('/fonts/Share-Regular.ttf');
        }

        @page {
            size: 38mm 25mm;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        body {
            font-family: "Share-Regular";
            text-transform: uppercase;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .page {
            box-sizing: border-box;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .label {
            width: 38mm;
            height: 25mm;
            text-align: center;
            border: 1px dotted;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .label-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .page-break {
            clear: left;
            display: block;
            page-break-after: always;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        @media print {
            .label {
                min-width: 100vw;
                min-height: 100vh;
                margin: -1px !important;
                padding: 0 !important;
                box-sizing: border-box;
                border: none;
                /*margin-top: -1px;*/
            }
        }

    </style>
</head>

<body>
    <div id="result" style="margin: 0; padding: 0; box-sizing:border-box;">
        @php
            $products = json_decode($data, true);
        @endphp
        <div class="page" id="page2">
            <div class="label-list">
                @foreach ($products as $key => $print)
                    @for ($i = 0; $i < $print['qty']; $i++)
                        <div class="label">
                            <span
                                style="font-size: 15px;">{{ $printData == 'product_name' ? $print['product_name'] : $print['category_name'] }}</span>
                            {!! DNS1D::getBarcodeSVG($print['barcode'], 'C128B', 1, 24, 'black', false) !!}
                            <span style="font-size: 15px; margin-bottom: -1px">
                                {{ $print['barcode'] }}
                            </span>
                            <span style="font-size: 15px;">
                                TK {{ $print['price'] }}
                            </span>
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>
    </div>

    <script>
        window.print()
    </script>
</body>

</html>
