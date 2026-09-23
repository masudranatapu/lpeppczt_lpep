@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')

    <div class="row ">

        {{-- {{ auth()->user()->userPermission->role->role_name === 'Agent' }} --}}
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="small-box bg-green whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $total_customer }}</span></h4>
                    <p>Total Beneficiary</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="small-box bg-pase whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $total_product }}</span></h4>
                    <p>{{ __('home.home')[1] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-shopping-bag"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="small-box bg-bringal whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $total_supplier }}</span></h4>
                    <p>{{ __('home.home')[2] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="small-box bg-darkgreen whitecolor mt-3">
                <div class="inner">
                    <h4><span class="count-number">{{ $today_sale }}</span> </h4>
                    <p>{{ __('home.home')[3] }}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

    </div>
@if (!isRole(ROLE_AGENT))
    <div class="card">
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
                            <h4><span class="count-number">{{ $total_asset_amount }}</span> </h4>
                            <p>Assets Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="small-box bg-green whitecolor mt-3">
                        <div class="inner">
                            <h4><span class="count-number">{{ $total_price }}</span></h4>
                            <p>Stock Amount</p>
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
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="small-box bg-red whitecolor mt-3">
                        <div class="inner">
                            <h4><span class="count-number">{{ $totalIncome }}</span></h4>
                            <p>Total Income</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="small-box bg-green whitecolor mt-3">
                        <div class="inner">
                            <h4><span
                                    class="count-number">{{ ($total_asset_amount + $total_price + $sale + $totalIncome) - ($expense + $salary) }}</span>
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

    <div class="row">

        <div class="col-md-6 col-lg-6">
            <div class=" card-box table-responsive" style="border-top: 3px solid #157c34;">
                <h4 class="m-t-0 header-title mb-3"><b>{{ __('home.home')[4] }}</b></h4>
                <div id="chairt-container"></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class=" card-box table-responsive" style="border-top: 3px solid #157c34;">
                <h4 class="m-t-0 header-title mb-3"><b>{{ __('home.home')[5] }}</b></h4>
                <canvas id="barChart" class="mt-3 mb-5"></canvas>
            </div>
        </div>
    </div>
@endif

@endsection


@section('script')

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>

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

    <script>


        $(function() {
            let datas = <?php echo json_encode($best_sale_product); ?>;
            let products = <?php echo json_encode($best_sale_product_name); ?>;
            let barCanvas = $("#barChart");
            let barChart = new Chart(barCanvas, {
                type: 'bar',
                data: {
                    // labels: ['product1','product2','product3','product4','product5','p6','p7','p8','p9','p10'],
                    labels: products,
                    datasets: [{
                        label: "Top 10 Selling Product This Month",
                        data: datas,
                        backgroundColor: ['read', 'orange', 'yellow', 'green', 'blue', 'indigo',
                            'violet', 'purple', 'pink', 'gray'
                        ]
                    }],
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    },
                }
            })
        });
    </script>

    <script>
        let datas = <?php echo json_encode($datas); ?>
        // let datas = "{{ json_encode($datas) }}"
        // console.log(datas)
        Highcharts.chart('chairt-container', {
            title: {
                text: ""
            },
            subtitle: {
                text: ""
            },
            xAxis: {
                categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "July", "Aug", "Sep", "Oct", "Nov", "Dec"]
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: "middle"
            },
            plotOptions: {
                series: {
                    allowPointSelect: true
                }
            },
            series: [{
                name: "okk",
                data: datas
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: "horizontal",
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        })
    </script>

@endsection
