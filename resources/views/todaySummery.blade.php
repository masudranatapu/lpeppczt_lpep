
    <div class="modal-body">
        <h2 style="text-align: center">Todays Summary</h2>
        <div class="box-body table-responsive">

            <table class="table table-responsive">
                <tbody>
                    <tr>
                        <td style="width: 80%;">Total  Purchase(Only Paid Amount)</td>
                        <td><span>tk {{ $total_purchase }}</span></td>
                    </tr>
                    <tr>
                        <td>Total  Sale(Only Paid Amount)</td>
                        <td><span>tk {{ $total_sale }}</span></td>
                    </tr>
                    <tr>
                        <td>Total  Expense</td>
                        <td><span>tk {{ $total_expense }}</span></td>
                    </tr>
                    <tr>
                        <td>Total  Supplier Due Payment</td>
                        <td><span>tk {{ $supplier_payment }}</span></td>
                    </tr>
                    <tr>
                        <td>Total  Customer Due Receive</td>
                        <td><span>tk {{ $customer_receive }}</span></td>
                    </tr>
                    <tr>
                        <td>Total  Sale Return</td>
                        <td><span>tk {{ $return_sale_total }}</span></td>
                    </tr>

                    <tr>
                        <td>Balance = (Sale + Customer Due Receive) - (Purchase + Supplier Due Payment + Expense))</td>
                        <td><span>tk {{ ($total_sale + $customer_receive) - ($total_purchase + $supplier_payment + $total_expense) }}</span></td>
                    </tr>
                </tbody>
            </table>

            <br>

        </div>
    </div>
