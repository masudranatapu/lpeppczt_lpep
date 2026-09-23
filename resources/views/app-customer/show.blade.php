<div class="modal-header bg-primary p-2">
    <h5 class="modal-title text-white">Beneficiary Details</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h6>Entry Date: {{ date('Y-m-d h:i a', strtotime($customer->created_at))}}</h6>
            <table class="table table-bordered">
                <tr>
                    <th>Beneficiary Name</th>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <th>Beneficiary Cell Number</th>
                    <td>{{ $customer->mobile }}</td>
                </tr>
                <tr>
                    <th>Beneficiary Email</th>
                    <td>{{ $customer->email }}</td>
                </tr>
                <tr>
                    <th>Beneficiary Number</th>
                    <td>{{ $customer->beneficiary_number }}</td>
                </tr>
                <tr>
                    <th>Group Number</th>
                    <td>{{ $customer->group_number }}</td>
                </tr>
                <tr>
                    <th>Registration Date</th>
                    <td>{{ $customer->date }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Livestock Information</th>
                </tr>
                <tr>
                    <th>Cow</th>
                    <td>{{ $customer->cow }}</td>
                </tr>
                <tr>
                    <th>Bull</th>
                    <td>{{ $customer->bull }}</td>
                </tr>
                <tr>
                    <th>Bakna</th>
                    <td>{{ $customer->bakna }}</td>
                </tr>
                <tr>
                    <th>Goat</th>
                    <td>{{ $customer->goat }}</td>
                </tr>
                <tr>
                    <th>Khasi</th>
                    <td>{{ $customer->khasi }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th colspan="4" class="text-center bg-light">Services</th>
                </tr>
                <tr>
                    <th>Membership</th>
                    <td>{{ $customer->membership ?? 'N/A' }}</td>
                    <th>Deworming</th>
                    <td>{{ $customer->deworming ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Bringing</th>
                    <td>{{ $customer->bringing ?? 'N/A' }}</td>
                    <th>Fattening</th>
                    <td>{{ $customer->fattening ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Treatment</th>
                    <td>{{ $customer->treatment ?? 'N/A' }}</td>
                    <th>AI</th>
                    <td>{{ $customer->ai ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Medicine</th>
                    <td>{{ $customer->medicine ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @if($customer->farms->count() > 0)
    <div class="row mt-3">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center bg-light">Farms</th>
                </tr>
                @foreach($customer->farms as $farm)
                <tr>
                    <th>Farm {{ $loop->iteration }}</th>
                    <td>{{ $farm->name }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
