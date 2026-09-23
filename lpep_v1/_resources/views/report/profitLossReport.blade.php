@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="m-t-0 header-title mb-3"><b>{{ __('page.report')[0] }}</b></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="get">
                                <div class="row justify-content-end">
                                    <div class="col-3">
                                        <input class="form-control datepicker startdate" value="{{ request('start') ?? '' }}"
                                            type="text" name="start" placeholder="Start Date" data-date-format="mm-dd-yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-3">
                                        <input class="form-control datepicker enddate" value="{{ request('end') ?? '' }}" type="text"
                                            name="end" placeholder="End Date" data-date-format="mm-dd-yyyy" autocomplete="off">
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" style="cursor: pointer;padding: 6px 5px;" class="btn btn-outline-primary">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-green whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $purchase }}</span></h4>
                                    <p>{{ __('page.report')[1] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-green whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $sale }}</span></h4>
                                    <p>{{ __('page.report')[2] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-darkgreen whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $return }}</span> </h4>
                                    <p>{{ __('page.report')[3] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-green whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $receive_payment }}</span></h4>
                                    <p>{{ __('page.report')[4] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-red whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $send_payment }}</span></h4>
                                    <p>{{ __('page.report')[5] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="small-box bg-darkgreen whitecolor mt-3">
                                <div class="inner">
                                    <h4><span class="count-number">{{ $expense + $salary }}</span> </h4>
                                    <p>{{ __('page.report')[6] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="small-box bg-green whitecolor mt-3">
                                <div class="inner">
                                    <h4><span
                                            class="count-number">{{ $sale - ($expense + $salary + $send_payment + $return + $purchase) }}</span>
                                    </h4>
                                    <p>{{ __('page.report')[0] }}</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        $(function() {
            $(".datepicker").datepicker({
                autoclose: true,
                todayHighlight: true,
                dateFormat: 'yyyy-MM-dd',
                format: 'yyyy-mm-dd',
            })
        });
        $(".startdate").change(function() {
            $(".enddate").val($(this).val())
        })
    </script>
    <script>
        $(function() {
            $('#tablefixed').responsiveTable({
                addFocusBtn: false
            });
        });
    </script>
@endsection
