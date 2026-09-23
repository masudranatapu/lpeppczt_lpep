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
            <div class="bg-img"><img src="assets/images/page-titles/1.jpg" alt="background"></div>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('frontend.index') }}">{{ __('frontend.home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('frontend.contact_us') }}</li>
                            </ol>
                        </nav>
                        <h1 class="pagetitle__heading">{{ __('frontend.contact_us') }}</h1>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.page-title -->

        <!-- ==========================
        contact 1
    =========================== -->
        <section id="contact1" class="contact text-center">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6 offset-lg-3">
                        <div class="heading text-center mb-50">
                            <span class="heading__subtitle">{{ __('frontend.GetInTouch') }}</span>
                            <h2 class="heading__title">{{ __('frontend.contact_us') }}</h2>
                            <div class="divider__line divider__theme divider__center mb-0"></div>
                            <p class="heading__desc">{{ __('frontend.contact_us_description') }}</p>
                        </div><!-- /.heading -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-8 offset-lg-2">
                        <form>
                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group"><input type="text" class="form-control"
                                            placeholder="Name"></div>
                                </div><!-- /.col-lg-6 -->
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group"><input type="email" class="form-control"
                                            placeholder="Email"></div>
                                </div><!-- /.col-lg-6 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group"><input type="text" class="form-control"
                                            placeholder="Phone"></div>
                                </div><!-- /.col-lg-6 -->
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group"><input type="text" class="form-control"
                                            placeholder="Company"></div>
                                </div><!-- /.col-lg-6 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <textarea class="form-control" placeholder="Request Details"></textarea>
                                    </div>
                                </div><!-- /.col-lg-12 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <button type="submit" class="btn btn__secondary btn__hover3">{{ __('frontend.SubmitRequest') }}</button>
                                </div><!-- /.col-lg-12 -->
                            </div><!-- /.row -->
                        </form>
                    </div><!-- /.col-lg-8 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.contact 1 -->

        <!-- =========================
            Google Map
    =========================  -->
        <section id="googleMap" class="google-map p-0">
            <div id="map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3648.665576103059!2d90.39623101543287!3d23.86600629026607!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7768d77ff63%3A0x732f4a43fcb2c093!2sAkaar%20IT%20Ltd.!5e0!3m2!1sen!2sbd!4v1659424153382!5m2!1sen!2sbd"
                    style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <script src="assets/js/google-map.js"></script>
            <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqrqPZOVegy1VIdyIcndxZY9YGoK-x0Yo&callback=initMap" async
                defer></script> -->
        </section><!-- /.GoogleMap -->
        <!-- /.GoogleMap -->

        <!-- ==========================
       Contact panels
    ============================ -->
        <!-- <section id="contactPanels" class="contact-panels text-center pb-70">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-4">
            <div class="contact-panel">
              <div class="contact__panel-header">
                <h4 class="contact__panel-title">Office Address</h4>
              </div>
              <ul class="contact__list list-unstyled">
                <li> Phone: <a href="tel:01304177009">01304177009</a>, <a href="tel:01614001122">01614001122</a> </li>
                <li>Email: <a href="mailto:info@makdelivery.com">info@makdelivery.com</a></li>
                <li>Address: House #05, Road #17, Sector #12, Uttara, Dhaka-1230, Bangladesh.</li>
              </ul>
              <a href="#" class="btn btn__primary btn__hover3">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </section> -->


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
