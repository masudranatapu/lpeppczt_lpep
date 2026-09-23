<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- End Google Tag Manager -->
    <title>Reset Otp | Amarsolution</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('loginasset') }}/assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="{{ asset('loginasset') }}/assets/fonts/font-awesome/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="{{ asset('loginasset') }}/assets/fonts/flaticon/font/flaticon.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon" >

    <!-- Google fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800%7CPoppins:400,500,700,800,900%7CRoboto:100,300,400,400i,500,700">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('loginasset') }}/assets/css/style.css">
    <link rel="stylesheet" type="text/css" id="style_sheet" href="{{ asset('loginasset') }}/assets/css/skins/default.css">
    <style>
        .splitter {
                padding: 0 5px;
                color: white;
                font-size: 24px;
            }
        input {
                width: 60px;
                height: 50px;
                background-color: lighten($BaseBG, 5%);
                border: none;
                line-height: 50px;
                text-align: center;
                font-size: 24px;
                font-family: 'Raleway', sans-serif;
                font-weight: 200;
                color: #3f3535;
                margin: 0 2px;
                border-radius: 3px;
            }

        .prompt {
            margin-bottom: 20px;
            font-size: 20px;
            color: white;
        }
    </style>
</head>
<body id="top">

<!-- Login 14 start -->
<div class="login-14">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-7 col-lg-7 col-md-12 bg-img none-992">
                <div class="info">
                    <h1>Welcome <span>to Amarsolution</span></h1>
                    <p>
                        Amar Solution is an online software where possible to manage all types of shops perfectly. It plans to connect clients who have shops but they need to manage. We will focus on providing our clients with User-friendly, Fast & Secure software that helps them manage their shop easily.
                    </p>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5 col-md-12 bg-color-10"  style="background: #44c9a4">
                <div class="form-section" style="" id="opt-section">
                    <div class="logo clearfix">
                        <a href="/">
                            <img src="{{ asset('images/logo.png') }}" style="height: 80px" alt="logo">
                        </a>
                    </div>
                    <h3 style="color: #fff">Enter Your OTP</h3>
                    @if (session("error_message"))
                        <div class="alert alert-danger" style="padding: 0.25rem 0.25rem">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <div class="login-inner-form">
                        <form method="post" class="digit-group" id="otpFrom" data-group-name="digits" data-autosubmit="false" autocomplete="off">
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <input type="text" id="digit-1" name="digit_1" data-next="digit-2"  required/>
                            <input type="text" id="digit-2" name="digit_2" data-next="digit-3" data-previous="digit-1" required />
                            <input type="text" id="digit-3" name="digit_3" data-next="digit-4" data-previous="digit-2" required />
                            <input type="text" id="digit-4" name="digit_4" data-next="digit-5" data-previous="digit-3" required />
                            <div id="otperror" class="mt-2"></div>
                            <div class="mt-4" style="width: 225px;margin-left: 100px;">
                                <button type="submit" class="btn-md btn-theme btn-block">Submit</button>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="form-section" style="display: none" id="password-reset-section">
                    <div class="logo clearfix">
                        <a href="/">
                            <img src="{{ asset('images/logo.png') }}" style="height: 80px" alt="logo">
                        </a>
                    </div>
                    <h3 style="color: #fff">Recover Your Password</h3>
                    @if (session("error_message"))
                        <div class="alert alert-danger" style="padding: 0.25rem 0.25rem">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <div class="login-inner-form" >
                        <form action="{{ route('change-password') }}" id="reset-password-form" method="POST">
                            @csrf
                            <input type="hidden" name="phone" value="{{ $phone }}">
                            <div class="form-group form-box">
                                <input type="password" name="password" class="input-text" placeholder="Password" required>
                                <i class="flaticon-mobile"></i>
                                <span class="text-danger" id="passwordErrorMessage"></span>
                            </div>
                            <div class="form-group form-box">
                                <input type="password" name="confirm_password" class="input-text" placeholder="Confirm Password" required>
                                <i class="flaticon-mobile"></i>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-md btn-theme btn-block">Submit</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login 14 end -->

<!-- External JS libraries -->
<script src="{{ asset('loginasset') }}/assets/js/jquery-2.2.0.min.js"></script>
<script src="{{ asset('loginasset') }}/assets/js/popper.min.js"></script>
<script src="{{ asset('loginasset') }}/assets/js/bootstrap.min.js"></script>
<script src="{{ asset('js/forget-password.js') }}"></script>
<script>
    $('.digit-group').find('input').each(function() {
        $(this).attr('maxlength', 1);
        $(this).on('keyup', function(e) {
            var parent = $($(this).parent());

            if(e.keyCode === 8 || e.keyCode === 37) {
                var prev = parent.find('input#' + $(this).data('previous'));

                if(prev.length) {
                    $(prev).select();
                }
            } else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
                var next = parent.find('input#' + $(this).data('next'));

                if(next.length) {
                    $(next).select();
                } else {
                    if(parent.data('autosubmit')) {
                        parent.submit();
                    }
                }
            }
        });
    });
</script>
<script>
    var _token = "{{ csrf_token() }}";
</script>
<!-- Custom JS Script -->
</body>
</html>