@extends('layouts.dashboard')

@section('content')


<div class="row">
    <div class="col-md-12">
        <div class="card-box table-responsive mt-4" style="border-top: 3px solid #296bc2;">
            <h3 class="mb-4">{{ __('page.saleinreturn')[0] }}</h3>
            <form action="{{ route('sale.in.return.post') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input class="form-control" id="" placeholder="{{ __('page.saleinreturn')[1] }}" autofocus name="orderid" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-3 m-auto">
                        <button type="submit" class="ml-5 btn btn-outline-primary" style="cursor: pointer;">{{ __('page.saleinreturn')[2] }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


@endsection