<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="description" content="Logisti ">
    <link href="assets/images/favicon/favicon.png" rel="icon">
    <title>LPEP </title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Roboto:400,700%7cWork+Sans:400,600,700&display=swap">
    <link rel="stylesheet" href="assets/css/libraries.css" />
    <link rel="stylesheet" href="assets/css/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" href="assets/css/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/icomoon/icomoon.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
   {{-- <div class="preloader">
        <div class="loading"><span></span><span></span><span></span><span></span></div>
    </div><!-- /.preloader --> --}}
    <div class="wrapper">
        <!-- =========================
        Header
    =========================== -->
        @include('frontend.includes.header')

        <!-- ============================
        Slider
    ============================== -->
        <section id="slider1" class="slider slider-1">
            <div class="owl-carousel thumbs-carousel carousel-arrows" data-slider-id="slider1" data-dots="false"
                data-autoplay="true" data-nav="true" data-transition="fade" data-animate-out="fadeOut"
                data-animate-in="fadeIn">
                <div class="slide-item align-v-h bg-overlay">
                    <div class="bg-img"><img src="assets/images/sliders/2.jpg" alt="slide img"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-8">
                                <div class="slide__content">
                                    <h2 class="slide__title">{{ __('frontend.slider4') }}</h2>
                                    <a href="#" class="btn btn__white mr-40">{{ __('frontend.our_services') }}</a>
                                    <a class="btn btn__video popup-video"
                                        href="https://www.youtube.com/watch?v=iCXaMrLviTQ">
                                        <div class="video__player">
                                            <span class="video__player-animation"></span>
                                            <i class="fa fa-play"></i>
                                        </div>{{ __('frontend.our_video') }}
                                    </a>
                                </div><!-- /.slide-content -->
                            </div><!-- /.col-lg-8 -->

                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.slide-item -->
                <div class="slide-item align-v-h bg-overlay">
                    <div class="bg-img"><img src="assets/images/sliders/3.jpg" alt="slide img"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-8">
                                <div class="slide__content">
                                    <h2 class="slide__title">{{ __('frontend.slider3') }}</h2>
                                    <a href="#" class="btn btn__white mr-40">{{ __('frontend.get_started') }}</a>
                                    <a class="btn btn__video popup-video"
                                        href="https://www.youtube.com/watch?v=EGsjVpy2fW0">
                                        <div class="video__player">
                                            <span class="video__player-animation"></span>
                                            <i class="fa fa-play"></i>
                                        </div>{{ __('frontend.our_video') }}
                                    </a>
                                </div><!-- /.slide-content -->
                            </div><!-- /.col-lg-8 -->

                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.slide-item -->
                <div class="slide-item align-v-h bg-overlay">
                    <div class="bg-img"><img src="assets/images/sliders/10.jpg" alt="slide img"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-8">
                                <div class="slide__content">
                                    <h2 class="slide__title">{{ __('frontend.slider1') }}</h2>
                                    <a href="#" class="btn btn__white mr-40">{{ __('frontend.our_services') }}</a>
                                    <a class="btn btn__video popup-video"
                                        href="https://www.youtube.com/watch?v=iCXaMrLviTQ">
                                        <div class="video__player">
                                            <span class="video__player-animation"></span>
                                            <i class="fa fa-play"></i>
                                        </div>{{ __('frontend.our_video') }}
                                    </a>
                                </div><!-- /.slide-content -->
                            </div><!-- /.col-lg-8 -->

                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.slide-item -->
                <div class="slide-item align-v-h bg-overlay">
                    <div class="bg-img"><img src="assets/images/sliders/4.jpg" alt="slide img"></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-8">
                                <div class="slide__content">
                                    <h2 class="slide__title">{{ __('frontend.slider2') }}</h2>
                                    <a href="#" class="btn btn__white mr-40">{{ __('frontend.get_started') }}</a>
                                    <a class="btn btn__video popup-video"
                                        href="https://www.youtube.com/watch?v=EGsjVpy2fW0">
                                        <div class="video__player">
                                            <span class="video__player-animation"></span>
                                            <i class="fa fa-play"></i>
                                        </div>{{ __('frontend.our_video') }}
                                    </a>
                                </div><!-- /.slide-content -->
                            </div><!-- /.col-lg-8 -->

                        </div><!-- /.row -->
                    </div><!-- /.container -->
                </div><!-- /.slide-item -->
            </div><!-- /.carousel -->
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12  d-none d-lg-block">
                        <div class="owl-thumbs thumbs-dots" data-slider-id="slider1">
                            <button class="owl-thumb-item">
                                <img src="./assets/images/services/1.png" alt="Delivery">
                                <span>{{ __('frontend.fast_delivery') }}</span>
                            </button>
                            <button class="owl-thumb-item">
                                <img src="./assets/images/services/3.png" alt="Client">
                                <span>{{ __('frontend.clients_focused') }}</span>
                            </button>
                            <button class="owl-thumb-item">
                                <img src="./assets/images/services/5.png" alt="Track">
                                <span>{{ __('frontend.real_time_tracking') }}</span>
                            </button>
                            <button class="owl-thumb-item">
                                <img src="./assets/images/services/6.png" alt="Payment">
                                <span>{{ __('frontend.next_day_payment') }}</span>
                            </button>
                        </div><!-- /.owl-thumbs -->
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.slider -->

        <!-- ========================
      About 4
    =========================== -->
        <section id="about4" class="about about-2 about-4 pb-40">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-7">
                        <div class="row heading heading-2">
                            <div class="col-sm-12 col-md-12 col-sm-12">
                                <h2 class="heading__title">{{ __('frontend.title1') }}</h2>
                            </div><!-- /.col-lg-12 -->
                            <div class="col-sm-12 col-md-5 col-lg-5">
                                <div class="carousel owl-carousel carousel-dots" data-slide="1" data-slide-md="1"
                                    data-slide-sm="1" data-autoplay="true" data-nav="false" data-dots="true"
                                    data-space="0" data-loop="true" data-speed="700">
                                    <div class="fancybox-item">
                                        <div class="fancybox__icon">
                                            <i class="fa-solid fa-globe"></i>
                                        </div><!-- /.fancybox-icon -->
                                        <div class="fancybox__content">
                                            <h4 class="fancybox__title">{{ __('frontend.our_mission') }}</h4>
                                            <p class="fancybox__desc">{{ __('frontend.mission_description') }}</p>
                                        </div><!-- /.fancybox-content -->
                                    </div><!-- /.fancybox-item -->
                                    <div class="fancybox-item">
                                        <div class="fancybox__icon">
                                            <i class="fa-solid fa-truck"></i>
                                        </div><!-- /.fancybox-icon -->
                                        <div class="fancybox__content">
                                            <h4 class="fancybox__title">{{ __('frontend.vision') }}</h4>
                                            <p class="fancybox__desc">{{ __('frontend.vision_description') }}</p>
                                        </div><!-- /.fancybox-content -->
                                    </div><!-- /.fancybox-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.col-lg-5 -->
                            <div class="col-sm-12 col-md-7 col-lg-7">
                                <p class="heading__desc mb-30">{{ __('frontend.title2') }}</p>

                                <p>{{ __('frontend.title3') }}</p>
                                <img src="assets/images/about/singnture.png" alt="singnture" class="signature mb-30">
                            </div><!-- /.col-lg-7 -->
                        </div><!-- /.row -->
                    </div><!-- /.col-lg-7 -->
                    <div class="col-sm-12 col-md-9 col-lg-5">
                        <div class="about__img mb-60">
                            <img src="assets/images/about/about-01.png" alt="about img" class="img-fluid">
                            <span>{{ __('frontend.more_about_us') }}</span>
                        </div><!-- /.about-img -->
                    </div><!-- /.col-lg-5 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.About 4 -->

        <!-- =========================
       video 1
      =========================== -->
        <section id="video1" class="video video-1 bg-overlay bg-parallax counters-white">
            <div class="bg-img"><img src="assets/images/backgrounds/3.jpg" alt="background"></div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="video__btn mb-45">
                            <a class="popup-video" href="https://www.youtube.com/watch?4=&v=TKnufs85hXk">
                                <span class="video__player-animation"></span>
                                <div class="video__player">
                                    <i class="fa fa-play"></i>
                                </div>
                            </a>
                        </div><!-- /.video -->
                    </div><!-- /.col-lg-12 -->
                    <div class="col-sm-12 col-md-12 col-lg-5">
                        <div class="heading">
                            <span class="heading__subtitle color-white">{{ __('frontend.your_package_your_rules') }}</span>
                            <h3 class="heading__title color-white">{{ __('frontend.title4') }}</h3>
                        </div><!-- /.heading -->
                    </div><!-- /.col-xl-5 -->
                    <div class="col-sm-12 col-md-12 col-lg-7">
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="counter-item">
                                    <div class="counter__icon"><i class="fa-solid fa-play"></i></div>
                                    <h4><span class="counter">5,000</span><span>+</span></h4>
                                    <p class="counter__desc">GO TO GOOGLE PLAY</p>
                                </div><!-- /.counter-item -->
                            </div><!-- /.col-lg-4 -->
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="counter-item">
                                    <div class="counter__icon"><i class="fa-solid fa-download"></i></div>
                                    <h4><span class="counter">80</span><span>+</span></h4>
                                    <p class="counter__desc">DOWNLOAD THE APP</p>
                                </div><!-- /.counter-item -->
                            </div><!-- /.col-lg-4 -->
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="counter-item">
                                    <div class="counter__icon"><i class="fa-solid fa-square-check"></i></div>
                                    <h4><span class="counter">10</span><span>+</span></h4>
                                    <p class="counter__desc">CREATE AN ACCOUNT</p>
                                </div><!-- /.counter-item -->
                            </div><!-- /.col-lg-4 -->
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="counter-item">
                                    <div class="counter__icon"><i class="fa-solid fa-star"></i></div>
                                    <h4><span class="counter">440</span><span>K+</span></h4>
                                    <p class="counter__desc">ENJOY AND RATE</p>
                                </div><!-- /.counter-item -->
                            </div><!-- /.col-lg-4 -->
                        </div><!-- /.row -->
                    </div><!-- /.col-xl-6 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.video 1 -->

        <section id="fancyboxLayout2" class="fancybox-layout2 pb-60">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-10 col-xl-4">
                        <div class="heading heading-3 mb-50">
                            <span class="heading__subtitle">{{ __('frontend.your_package_your_rules') }}</span>
                            <h2 class="heading__title">{{ __('frontend.title5') }}</h2>
                        </div>
                    </div><!-- /.col-xl-4 -->
                </div><!-- /.row -->
                <div class="row">
                    <!-- fancybox item #1 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/1.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.cattle_registration') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.cattle_registration_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-3 -->
                    <!-- fancybox item #2 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/2.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.insemination_information') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.insemination_information_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-4 -->
                    <!-- fancybox item #3 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/3.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.find_out') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.find_out_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-4 -->
                    <!-- fancybox item #4 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/4.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.milk_production') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.milk_production_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-4 -->
                    <!-- fancybox item #5 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/5.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.income_and_expense') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.income_and_expense_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-4 -->
                    <!-- fancybox item #6 -->
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="fancybox-item">
                            <div class="fancybox__icon">
                                <img src="./assets/images/services/6.png" alt="icon">
                            </div><!-- /.fancybox-icon -->
                            <div class="fancybox__content">
                                <h4 class="fancybox__title">{{ __('frontend.detailed_report') }}</h4>
                                <p class="fancybox__desc">{{ __('frontend.detailed_report_details') }}</p>
                            </div><!-- /.fancybox-content -->
                        </div><!-- /.fancybox-item -->
                    </div><!-- /.col-lg-4 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.fancybox Layout2 -->

        <!-- =====================
       Clients 1
    ======================== -->
            @include('frontend.includes.our-partners')

        <!-- =========================
      Carousel Tabs
      =========================== -->
        <section id="carouselTabs" class="carousel-tabs pb-70">
            <div class="pricing-bg">
                <div class="bg-img bg-overlay"><img src="assets/images/backgrounds/1.jpg" alt="background"></div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6 offset-lg-3">
                        <div class="heading text-center mb-50">
                            <span class="heading__subtitle color-white">{{ __('frontend.latest_case_studies') }}</span>
                            <h2 class="heading__title color-white">{{ __('frontend.featured_projects') }}</h2>
                            <div class="divider__line divider__white divider__center"></div>
                        </div><!-- /.heading -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <nav class="nav nav-tabs nav-tabs-white">
                            <a class="nav__link active" data-toggle="tab" href="#tab1">{{ __('frontend.live_cattle') }}</a>
                            <a class="nav__link" data-toggle="tab" href="#tab2">{{ __('frontend.milk_sweet_dairy') }}</a>
                            <a class="nav__link" data-toggle="tab" href="#tab3">{{ __('frontend.vegetables_fruits') }}</a>
                            <a class="nav__link" data-toggle="tab" href="#tab4">{{ __('frontend.machines_tools_electronics') }}</a>
                            <a class="nav__link" data-toggle="tab" href="#tab5">{{ __('frontend.gardening_materials') }}</a>
                            <a class="nav__link" data-toggle="tab" href="#tab6">{{ __('frontend.vet_medicine') }}</a>
                        </nav>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab1">
                                <div class="projects-carousel-3  carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/1.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.analytics') }}</a><a href="#">{{ __('frontend.Optimization') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.LanePairingAnalysis') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #2 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/2.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #3 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/3.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Logistics') }}</a><a href="#">{{ __('frontend.analytics') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.MinimizeCostManufacturing') }}</a></h4>
                                            <p class="project__desc">{{ __('frontend.project_details2') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #4 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/4.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                            <div class="tab-pane fade" id="tab2">
                                <div class="projects-carousel-3  carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/15.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Analytics') }}</a><a href="#">{{ __('frontend.Optimization') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.LanePairingAnalysis') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #2 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/16.png" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #3 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/11.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Logistics') }}</a><a href="#">{{ __('frontend.Analytics') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.MinimizeCostManufacturing') }}</a></h4>
                                            <p class="project__desc">{{ __('frontend.project_details2') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #4 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/4.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                            <div class="tab-pane fade" id="tab3">
                                <div class="projects-carousel-3  carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/4.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Analytics') }}</a><a href="#">{{ __('frontend.Optimization') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.LanePairingAnalysis') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #2 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/6.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #3 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/1.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Logistics') }}</a><a href="#">{{ __('frontend.Analytics') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.MinimizeCostManufacturing') }}</a></h4>
                                            <p class="project__desc">{{ __('frontend.project_details2') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #4 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/2.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                            <div class="tab-pane fade" id="tab4">
                                <div class="projects-carousel-3  carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/5.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Analytics') }}</a><a href="#">{{ __('frontend.Optimization') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.LanePairingAnalysis') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #2 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/6.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #3 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/1.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Logistics') }}</a><a href="#">{{ __('frontend.Analytics') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.MinimizeCostManufacturing') }}</a></h4>
                                            <p class="project__desc">{{ __('frontend.project_details2') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #4 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/4.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                            <div class="tab-pane fade" id="tab5">
                                <div class="projects-carousel-3  carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/6.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #3 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/1.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Logistics') }}</a><a href="#">{{ __('frontend.Analytics') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.MinimizeCostManufacturing') }}</a></h4>
                                            <p class="project__desc">{{ __('frontend.project_details2') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #4 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/4.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                            <div class="tab-pane fade" id="tab6">
                                <div class="projects-carousel-3 carousel owl-carousel carousel-dots" data-slide="3"
                                    data-slide-md="2" data-slide-sm="1" data-autoplay="true" data-nav="false"
                                    data-dots="true" data-space="30" data-loop="true" data-speed="800">
                                    <!-- project item #1 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/3.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Analytics') }}</a><a href="#">{{ __('frontend.Optimization') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.LanePairingAnalysis') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                    <!-- project item #2 -->
                                    <div class="project-item">
                                        <div class="project__img">
                                            <img src="assets/images/case-studies/grid/6.jpg" alt="project img">
                                            <a href="#" class="zoom__icon"></a>
                                        </div><!-- /.project-img -->
                                        <div class="project__content">
                                            <div class="project__cat">
                                                <a href="#">{{ __('frontend.Warehousing') }}</a><a href="#">{{ __('frontend.Distribution') }}</a>
                                            </div><!-- /.project-cat -->
                                            <h4 class="project__title"><a href="#">{{ __('frontend.WarehouseHandInventory') }}</a>
                                            </h4>
                                            <p class="project__desc">{{ __('frontend.project_details1') }}</p>
                                        </div><!-- /.project-content -->
                                    </div><!-- /.project-item -->
                                </div><!-- /.carousel -->
                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.Carousel Tabs  -->

        <!-- =========================
            Testimonial #2
    =========================  -->
            @include('frontend.includes.testimonial')

        <!-- ======================
      Blog Grid
    ========================= -->
    @include('frontend.includes.blogs')

        <!-- =========================
            cta 1
    =========================  -->
    @include('frontend.includes.map-section')
        <!-- ========================
      Footer
    ========================== -->
            @include('frontend.includes.footer')
        <button id="scrollTopBtn"><i class="fa fa-long-arrow-up"></i></button>

        <div class="module__search-container">
            <i class="fa fa-times close-search"></i>
            <form class="module__search-form">
                <input type="text" class="search__input" placeholder="Search Here">
                <button class="module__search-btn"><i class="fa fa-search"></i></button>
            </form>
        </div><!-- /.module-search-container -->

    </div><!-- /.wrapper -->

    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/liga.js"></script>
    <script src="assets/js/icomoon.js"></script>
    <script src="assets/js/main.js"></script>
    @include('frontend.includes.language-change-script')
</body>

</html>
