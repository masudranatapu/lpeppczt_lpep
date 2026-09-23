<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- End Google Tag Manager -->
    <title>Reset Password | Amarsolution</title>
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
                <div class="form-section">
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
                    <div class="login-inner-form">
                        <form action="{{ route('reset.password') }}" method="POST">
                            @csrf
                            <div class="form-group form-box">
                                <input type="number" name="phone" class="input-text" placeholder="Phone Number" required>
                                <i class="flaticon-mobile"></i>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-md btn-theme btn-block">Send Otp</button>
                            </div>
                        </form>
                    </div>
                    <p style="color: #fff">Already a member? <a href="/login" style="color: #fff" class="thembo"> Login here</a></p>
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
<!-- Custom JS Script -->
</body>
</html>