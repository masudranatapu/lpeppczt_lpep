<section id="cta1" class="cta cta-1 border-top pt-40 pb-10">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-4">
                <div class="contact-panel contact-panel-2">
                    <div class="contact__panel-header d-flex align-items-center">
                        <i class="fa-solid fa-location-dot"></i>
                        <h4 class="contact__panel-title">{{ __('frontend.OfficeLocation') }}</h4>
                    </div>
                    <div id="accordion">
                        <div class="accordion-item">
                            <div class="accordion__item-header opened" data-toggle="collapse"
                                data-target="#collapse1">
                                <a class="accordion__item-title" href="#">{{ __('frontend.OfficeAddress') }}</a>
                            </div><!-- /.accordion-item-header -->
                            <div id="collapse1" class="collapse  show" data-parent="#accordion">
                                <div class="accordion__item-body">
                                    <ul class="contact__list list-unstyled">
                                        <li>Phone: <a href="tel:+8809639132756">+88 096 391 327 56</a>,</li>
                                        <li>Email: <a href="mailto:info@lpep.com.bd">info@l-pep.org</a></li>
                                        <li>{{ __('frontend.footer_address') }} </li>
                                        <!-- <li>Hours: Mon-Fri: 8am – 7pm</li> -->
                                    </ul>
                                </div><!-- /.accordion-item-body -->
                            </div>
                        </div><!-- /.accordion-item -->
                    </div>
                </div><!-- /.contact-panel -->
            </div><!-- /.col-lg-4 -->
            <div class="col-sm-12 col-md-12 col-lg-3 text-right">
                <h2 class="cta__title">{{ __('frontend.sign_up_motto') }}</h2>
            </div><!-- /.col-lg-3 -->
            <div class="col-sm-12 col-md-12 col-lg-5">
                <form>
                    <div class="form-group d-flex">
                        <input type="text" class="form-control mr-30" placeholder="Your Email Address">
                        <button class="btn btn__primary btn__hover3">{{ __('frontend.SignUp') }}</button>
                    </div>
                </form>
            </div><!-- /.col-lg-5 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.cta1 -->

<!-- =========================
    Google Map
=========================  -->
<section id="googleMap" class="google-map p-0">
    <div id="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.8415478194643!2d90.36927179999999!3d23.753029200000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755bf5190b163ad%3A0x5a7ae5bc7d133767!2sRangs%20Nasim%20Square%2C%2046%20Rd%2027%2C%20Dhaka%201209!5e0!3m2!1sen!2sbd!4v1667907099524!5m2!1sen!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <script src="assets/js/google-map.js"></script>
</section><!-- /.GoogleMap -->
