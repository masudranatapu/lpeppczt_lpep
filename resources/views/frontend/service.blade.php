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
        <!-- ========================
       page title
    =========================== -->
        <section id="page-title" class="page-title bg-overlay bg-parallax">
            <div class="bg-img"><img src="assets/images/page-titles/14.jpg" alt="background"></div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('frontend.index') }}">{{ __('frontend.home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('frontend.service') }}</li>
                            </ol>
                        </nav>
                        <h1 class="pagetitle__heading">{{ __('frontend.service') }}</h1>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.page-title -->

         <!-- ===========================
      fancybox Layout2
    ============================= -->
    <section id="service" class="pb-60">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-8">
                        <div class="heading heading-3 mb-50">
                            <span class="heading__subtitle">{{ __('frontend.your_package_your_rules') }}</span>
                            <h2 class="heading__title">{{ __('frontend.title5') }}</h2>
                        </div>
                    </div><!-- /.col-xl-4 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-12">
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title1') }}</h6>
                            <b>{{ __('frontend.service_description1') }}</b>
                            <ul class="vision-list mt-3">
                                <li>{{ __('frontend.maintainence1') }}</li>
                                <li>{{ __('frontend.maintainence2') }}</li>
                                <li>{{ __('frontend.maintainence3') }}</li>
                                <li>{{ __('frontend.maintainence4') }}</li>
                                <li>{{ __('frontend.maintainence5') }}</li>
                                <li>{{ __('frontend.maintainence6') }}</li>
                            </ul>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title2') }}</h6>
                            <p>{{ __('frontend.service_description2') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title3') }}</h6>
                            <p>{{ __('frontend.service_description3') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title4') }}</h6>
                            <p>{{ __('frontend.service_description4') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title5') }}</h6>
                            <p>{{ __('frontend.service_description5') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title6') }}</h6>
                            <p>{{ __('frontend.service_description6') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title7') }}</h6>
                            <p>{{ __('frontend.service_description7') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title8') }}</h6>
                            <p>{{ __('frontend.service_description8') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title9') }}</h6>
                            <p>{{ __('frontend.service_description9') }}</p>
                        </div>
                        <div class="single-service">
                            <h6>{{ __('frontend.service_title10') }}</h6>
                            <p> {{ __('frontend.service_description10') }}</p>
                        </div>
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.fancybox Layout2 -->

        <!-- ========================

      About 2
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

        <!-- =====================
       Clients 1
    ======================== -->
    @include('frontend.includes.our-partners')
        <!-- ======================
           banner 1
      ========================= -->
      @include('frontend.includes.information-banner')
        <!-- =========================
            Testimonial #2
    =========================  -->
    @include('frontend.includes.testimonial')

        <!-- ======================
      Blog Grid
    ========================= -->
    @include('frontend.includes.blogs')
        <!-- /.blog Grid -->

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
