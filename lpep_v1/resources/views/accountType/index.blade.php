@extends('layouts.dashboard')
@section('content')

    <div class="row">

        <div class="col-md-10 m-auto">
            <div class="card-box mt-4">
                <h4 class="header-title m-t-0 m-b-30 mb-4">{{ __('page.accountlist')[0] }}</h4>

                <form method="POST" action="{{ route('account.type.store') }}"
                    class="">
                @csrf

                @php
                    $business = \App\Models\Business::findOrFail(1);
                @endphp
                <div class="
                    form-group row">
                    <div class="col-md-6">
                        <label for="">Account Type</label>
                        <select name="account_type" id="balance_type" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Cash">Cash</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                            <option value="Card">Card</option>
                            <option value="Bank Account">Bank Account</option>
                        </select>
                    </div>
            </div>

            <div id="account_info">

            </div>


            </form>
        </div>
    </div>


    </div>

@endsection

@section('script')
    <script>
        $("body").on('change', "#balance_type", function() {
            let type = $(this).val()
            let _token = "{{ csrf_token() }}"
            $.post("{{ route('account.type.check') }}", {
                _token: _token,
                type: type
            }, function(data) {
                $("#account_info").html(data)
                $(".select3").select2();
            })
        })
    </script>
@endsection
