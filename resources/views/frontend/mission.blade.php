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
                                <li class="breadcrumb-item active" aria-current="page">{{ __('frontend.mission') }}</li>
                            </ol>
                        </nav>
                        <h1 class="pagetitle__heading">{{ __('frontend.mission') }}</h1>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.page-title -->

        <!-- ========================
      About 2
    =========================== -->
        <section id="about2" class="about about-2 pb-30">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-9 col-lg-5">
                        <div class="about__img mb-60">
                            <img src="assets/images/about/2.jpg" alt="about img" class="img-fluid">
                        </div><!-- /.about-img -->
                    </div><!-- /.col-lg-5 -->
                    <div class="col-sm-12 col-md-12 col-lg-7">
                        <div class="row heading heading-2">
                            <div class="col-sm-12 col-md-12 col-sm-12">
                                <p class="heading__desc mb-30">{{ __('frontend.mission_description2') }}</p>
                            </div><!-- /.col-lg-12 -->
                            <div class="col-sm-12">
                                <ul class="vision-list">
                                    <li>{{ __('frontend.mission_highlight1') }}</li>
                                    <li>{{ __('frontend.mission_highlight2') }}</li>
                                    <li>{{ __('frontend.mission_highlight3') }}</li>
                                    <li>{{ __('frontend.mission_highlight4') }}</li>
                                    <li>{{ __('frontend.mission_highlight5') }}</li>
                                    <li>{{ __('frontend.mission_highlight6') }}</li>
                                    <li>{{ __('frontend.mission_highlight7') }}</li>
                                </ul>
                            </div><!-- /.col-lg-7 -->
                        </div><!-- /.row -->
                    </div><!-- /.col-lg-7 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.About 2 -->

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
