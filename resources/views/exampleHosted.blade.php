<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="SSLCommerz">
    <title>Pos Payment</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .bg-gradient {
            background: #C9D6FF;
            background: -webkit-linear-gradient(to right, #E2E2E2, #C9D6FF);
            background: linear-gradient(to right, #E2E2E2, #C9D6FF);
        }

        ul li {
            margin-bottom: 1.4rem;
        }

        .pricing-divider {
            border-radius: 20px;
            background: #C64545;
            padding: 1em 0 4em;
            position: relative;
        }

        .blue .pricing-divider {
            background: #2D5772;
        }

        .green .pricing-divider {
            background: #1AA85C;
        }

        .red b {
            color: #C64545
        }

        .blue b {
            color: #2D5772
        }

        .green b {
            color: #1AA85C
        }

        .pricing-divider-img {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 80px;
        }

        .deco-layer {
            -webkit-transition: -webkit-transform 0.5s;
            transition: transform 0.5s;
        }

        .btn-custom {
            background: #C64545;
            color: #fff;
            border-radius: 20px
        }

        .img-float {
            width: 50px;
            position: absolute;
            top: -3.5rem;
            right: 1rem
        }

        .princing-item {
            transition: all 150ms ease-out;
        }

        .princing-item:hover {
            transform: scale(1.05);
        }

        .princing-item:hover .deco-layer--1 {
            -webkit-transform: translate3d(15px, 0, 0);
            transform: translate3d(15px, 0, 0);
        }

        .princing-item:hover .deco-layer--2 {
            -webkit-transform: translate3d(-15px, 0, 0);
            transform: translate3d(-15px, 0, 0);
        }

    </style>
</head>

<body class="bg-light">
    <div class="container">



        <div class="py-5 text-center">
            <h2 style="color: red;">Your Software Is Now Expired</h2>
            <p class="lead">Would you like to activate it now ?</p>
        </div>



        <div class="container-fluid p-3">
            <div class="row m-auto text-center w-75">


                @php
                    $branch = \App\Models\Business::where('user_id', $user_id)->count();
                @endphp

                @if ($branch == 1)
                    <div class="col-4 princing-item red">
                        <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" value="{{ $user_id }}" name="user_id" />
                            <input type="hidden" value="500" name="amount" />
                            <input type="hidden" value="1" name="package" />
                            <div class="pricing-divider ">
                                <h3 class="text-light">START-UP</h3>
                                <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span
                                        class="h1">৳</span> 500 <span class="h5">/mo</span></h4>
                                <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                    id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                                    width='300px' x='0px' xml:space='preserve'
                                    xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'
                                    y='0px'>
                                    <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF'
                                        opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z' fill='#FFFFFF'
                                        opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                    <path class='deco-layer deco-layer--4' d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z'
                                        fill='#FFFFFF'></path>
                                </svg>
                            </div>
                            <div class="card-body bg-white mt-0 shadow">
                                <ul class="list-unstyled mb-5 position-relative">
                                    <li><b>Unlimited</b> Users Included</li>
                                    <li><b>No Business</b> Branch Created</li>
                                    <li><b>Help center access</b></li>
                                    <li>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <b class="ml-2">Validity</b>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="validity" id="" style="width: 100%;">
                                                    <option value="1">1 Month</option>
                                                    <option value="3">3 Month</option>
                                                    <option value="6">6 Month</option>
                                                    <option value="12">12 Month</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <button type="submit" class="btn btn-lg btn-block  btn-custom ">Purchase</button>
                            </div>
                        </form>
                    </div>




                    <div class="col-4 princing-item blue">
                        <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" value="{{ $user_id }}" name="user_id" />
                            <input type="hidden" value="1000" name="amount" />
                            <input type="hidden" value="2" name="package" />
                            <div class="pricing-divider ">
                                <h3 class="text-light">BUSINESS</h3>
                                <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span
                                        class="h1">৳</span> 1000 <span class="h5">/month</span>
                                </h4>
                                <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                    id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                                    width='300px' x='0px' xml:space='preserve'
                                    xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'
                                    y='0px'>
                                    <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                            c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z'
                                        fill='#FFFFFF' opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                            c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z'
                                        fill='#FFFFFF' opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                            H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                    <path class='deco-layer deco-layer--4'
                                        d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                            c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                                </svg>
                            </div>

                            <div class="card-body bg-white mt-0 shadow">
                                <ul class="list-unstyled mb-5 position-relative">
                                    <li><b>Unlimited</b> Users Included</li>
                                    <li><b>5 Business</b> Branch Created</li>
                                    <li><b>Help center access</b></li>
                                    <li>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <b class="ml-2">Validity</b>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="validity" id="" style="width: 100%;">
                                                    <option value="1">1 Month</option>
                                                    <option value="3">3 Month</option>
                                                    <option value="6">6 Month</option>
                                                    <option value="12">12 Month</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <button type="submit" class="btn btn-lg btn-block  btn-custom ">Purchase</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-4 princing-item green">
                        <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" value="{{ $user_id }}" name="user_id" />
                            <input type="hidden" value="1500" name="amount" />
                            <input type="hidden" value="3" name="package" />

                            <div class="pricing-divider ">
                                <h3 class="text-light">PRO</h3>
                                <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span
                                        class="h1">৳</span> 1500 <span class="h5">/month</span>
                                </h4>
                                <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px'
                                    id='Layer_1' preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100'
                                    width='300px' x='0px' xml:space='preserve'
                                    xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'
                                    y='0px'>
                                    <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                        c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF'
                                        opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                        c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z'
                                        fill='#FFFFFF' opacity='0.6'></path>
                                    <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                        H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                                    <path class='deco-layer deco-layer--4'
                                        d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                    c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                                </svg>
                            </div>

                            <div class="card-body bg-white mt-0 shadow">
                                <ul class="list-unstyled mb-5 position-relative">
                                    <li><b>Unlimited</b> Users Included</li>
                                    <li><b>Unlimited</b> Branch Created</li>
                                    <li><b>Help center access</b></li>
                                    <li>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <b class="ml-2">Validity</b>
                                            </div>
                                            <div class="col-md-8">
                                                <select name="validity" id="" style="width: 100%;">
                                                    <option value="1">1 Month</option>
                                                    <option value="3">3 Month</option>
                                                    <option value="6">6 Month</option>
                                                    <option value="12">12 Month</option>
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <button type="submit" class="btn btn-lg btn-block btn-custom">Purchase</button>
                            </div>
                    </div>
                    </form>
            </div>

        @elseif($branch>1 && $branch <= 5) <div class="col-4 princing-item blue">
                <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <input type="hidden" value="{{ $user_id }}" name="user_id" />
                    <input type="hidden" value="1000" name="amount" />
                    <input type="hidden" value="2" name="package" />
                    <div class="pricing-divider ">
                        <h3 class="text-light">BUSINESS</h3>
                        <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span
                                class="h1">৳</span> 1000 <span class="h5">/month</span></h4>
                        <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1'
                            preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px'
                            xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink'
                            xmlns='http://www.w3.org/2000/svg' y='0px'>
                            <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                            c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z'
                                fill='#FFFFFF' opacity='0.6'></path>
                            <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                            c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z'
                                fill='#FFFFFF' opacity='0.6'></path>
                            <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                            H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                            <path class='deco-layer deco-layer--4'
                                d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                            c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                        </svg>
                    </div>

                    <div class="card-body bg-white mt-0 shadow">
                        <ul class="list-unstyled mb-5 position-relative">
                            <li><b>Unlimited</b> Users Included</li>
                            <li><b>5 Business</b> Branch Created</li>
                            <li><b>Help center access</b></li>
                            <li>
                                <div class="row">
                                    <div class="col-md-4">
                                        <b class="ml-2">Validity</b>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="validity" id="" style="width: 100%;">
                                            <option value="1">1 Month</option>
                                            <option value="3">3 Month</option>
                                            <option value="6">6 Month</option>
                                            <option value="12">12 Month</option>
                                        </select>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-lg btn-block  btn-custom ">Purchase</button>
                    </div>
                </form>
        </div>

        <div class="col-4 princing-item green">
            <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <input type="hidden" value="{{ $user_id }}" name="user_id" />
                <input type="hidden" value="1500" name="amount" />
                <input type="hidden" value="3" name="package" />

                <div class="pricing-divider ">
                    <h3 class="text-light">PRO</h3>
                    <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span class="h1">৳</span>
                        1500 <span class="h5">/month</span></h4>
                    <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1'
                        preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px'
                        xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink'
                        xmlns='http://www.w3.org/2000/svg' y='0px'>
                        <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                        c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF'
                            opacity='0.6'></path>
                        <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                        c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z'
                            fill='#FFFFFF' opacity='0.6'></path>
                        <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                        H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                        <path class='deco-layer deco-layer--4'
                            d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                    c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                    </svg>
                </div>

                <div class="card-body bg-white mt-0 shadow">
                    <ul class="list-unstyled mb-5 position-relative">
                        <li><b>Unlimited</b> Users Included</li>
                        <li><b>Unlimited</b> Branch Created</li>
                        <li><b>Help center access</b></li>
                        <li>
                            <div class="row">
                                <div class="col-md-4">
                                    <b class="ml-2">Validity</b>
                                </div>
                                <div class="col-md-8">
                                    <select name="validity" id="" style="width: 100%;">
                                        <option value="1">1 Month</option>
                                        <option value="3">3 Month</option>
                                        <option value="6">6 Month</option>
                                        <option value="12">12 Month</option>
                                    </select>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <button type="submit" class="btn btn-lg btn-block btn-custom">Purchase</button>
                </div>
        </div>
        </form>
    </div>

@elseif($branch > 5)

    <div class="col-4 princing-item green">
        <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
            <input type="hidden" value="{{ $user_id }}" name="user_id" />
            <input type="hidden" value="1500" name="amount" />
            <input type="hidden" value="3" name="package" />

            <div class="pricing-divider ">
                <h3 class="text-light">PRO</h3>
                <h4 class="my-0 display-2 text-light font-weight-normal mb-3"><span class="h1">৳</span> 1500
                    <span class="h5">/month</span></h4>
                <svg class='pricing-divider-img' enable-background='new 0 0 300 100' height='100px' id='Layer_1'
                    preserveAspectRatio='none' version='1.1' viewBox='0 0 300 100' width='300px' x='0px'
                    xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'
                    y='0px'>
                    <path class='deco-layer deco-layer--1' d='M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729
                                        c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z' fill='#FFFFFF'
                        opacity='0.6'></path>
                    <path class='deco-layer deco-layer--2' d='M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729
                                        c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z'
                        fill='#FFFFFF' opacity='0.6'></path>
                    <path class='deco-layer deco-layer--3' d='M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716
                                        H42.401L43.415,98.342z' fill='#FFFFFF' opacity='0.7'></path>
                    <path class='deco-layer deco-layer--4'
                        d='M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428
                                    c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z' fill='#FFFFFF'></path>
                </svg>
            </div>

            <div class="card-body bg-white mt-0 shadow">
                <ul class="list-unstyled mb-5 position-relative">
                    <li><b>Unlimited</b> Users Included</li>
                    <li><b>Unlimited</b> Branch Created</li>
                    <li><b>Help center access</b></li>
                    <li>
                        <div class="row">
                            <div class="col-md-4">
                                <b class="ml-2">Validity</b>
                            </div>
                            <div class="col-md-8">
                                <select name="validity" id="" style="width: 100%;">
                                    <option value="1">1 Month</option>
                                    <option value="3">3 Month</option>
                                    <option value="6">6 Month</option>
                                    <option value="12">12 Month</option>
                                </select>
                            </div>
                        </div>
                    </li>
                </ul>
                <button type="submit" class="btn btn-lg btn-block btn-custom">Purchase</button>
            </div>
    </div>
    </form>
    </div>


    @endif




    </div>







    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; {{ date('Y') }} Akaar IT</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script>
        (function(window, document) {
            var loader = function() {
                var script = document.createElement("script"),
                    tag = document.getElementsByTagName("script")[0];
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(
                    7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload",
                loader);
        })(window, document);
    </script>

</html>
