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
                margin-bottom:1.4rem;
            }
            .pricing-divider {
                border-radius: 20px;
                background: #C64545;
                padding: 1em 0 4em;
                position: relative;
            }
            .blue .pricing-divider{
                background: #2D5772; 
            }
            .green .pricing-divider {
                background: #1AA85C; 
            }
            .red b {
                color:#C64545
            }
            .blue b {
                color:#2D5772
            }
            .green b {
                color:#1AA85C
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
            .btn-custom  {
                background:#C64545; color:#fff; border-radius:20px
            }

            .img-float {
                width:50px; position:absolute;top:-3.5rem;right:1rem
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
        <p class="lead">Would you like to activate it now Please Contact Your Branch Admin?</p>
    </div>



    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; {{ date("Y") }} Akaar IT</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script>
        (function (window, document) {
            var loader = function () {
                var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        })(window, document);
    </script>
</html>
