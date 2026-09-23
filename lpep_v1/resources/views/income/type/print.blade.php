<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>L-PEP</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 6px 20px;
        }

        tr th {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 6px 20px;
            background-color: #eeeded;
            color: #444;
        }

        .content td,
        .content th {
            border-top: 1px solid transparent;
            padding: 2px 10px 2px 15px;
        }



        @page {
            size: A4;
            margin-top: 15mm;
            margin-right: 10mm;
            margin-bottom: 15mm;
            margin-left: 10mm;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .flex {
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>

<body>
    <div>
        <div style="text-align: center;">
            <img src="/{{ currentBranch()->logo }}" style="height: 80px; width: 80px;">
            <h4 style="text-align: center;">Income History Of "{{ $type->income_type }}" </h4>
        </div>
        <table>
            <tbody>
                <thead>
                    <th rowspan="2"><strong>SL No.</strong></th>
                    <th rowspan="2"><strong>Date</strong></th>
                    <th rowspan="2"><strong>Amount</strong></th>
                </thead>

                @foreach ($incomes as $income)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($income->income_date)->format('d') }}
                            <small>{{ \Carbon\Carbon::parse($income->income_date)->format('M') }}</small>
                        </td>
                        <td>{{ $income->income_date }}
                            <small>({{ \Carbon\Carbon::parse($income->income_date)->format('l') }})</small>
                        </td>
                        <td id="sub_total">
                            {{ incomeTypeInlineTotal($type->income_type, $income->details) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <strong>{{ __('Total') }} =</strong>
                    </td>
                    <td>
                        <strong id="total"></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('table tbody tr').forEach(row => {
                let subTotalText = row.querySelector('#sub_total')?.innerText.trim();
                let subTotal = parseFloat(subTotalText);
                if (!isNaN(subTotal)) {
                    total += subTotal;
                }
            });
            document.getElementById('total').innerText = total.toFixed(2);
        }
        calculateTotal();

        setTimeout(() => window.print(), 100);
    });
</script>
