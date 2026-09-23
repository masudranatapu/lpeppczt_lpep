<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <link rel="shortcut icon" href="/logo.jpeg">
    <title>Pos</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="{!! asset('backend') !!}/plugins/morris/morris.css">
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @include('inc.select2-custom-style')
    <link href="{!! asset('backend') !!}/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css"
        rel="stylesheet" />

    <link href="{!! asset('backend') !!}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{!! asset('backend') !!}/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{!! asset('backend') !!}/css/style.css" rel="stylesheet" type="text/css" />
    @stack('css')
    <script src="{!! asset('backend') !!}/js/modernizr.min.js"></script>
</head>


<body class="fixed-left">
    <!-- Begin page -->
    <div id="wrapper">
        @yield('pos')
    </div>

    <!-- jQuery  -->
    <script src="{!! asset('backend') !!}/js/jquery.min.js"></script>
    <script src="{!! asset('backend') !!}/js/popper.min.js"></script>
    <script src="{!! asset('backend') !!}/js/bootstrap.min.js"></script>
    <script src="{!! asset('backend') !!}/js/detect.js"></script>
    <script src="{!! asset('backend') !!}/js/fastclick.js"></script>
    <script src="{!! asset('backend') !!}/js/jquery.blockUI.js"></script>
    <script src="{!! asset('backend') !!}/js/waves.js"></script>
    <script src="{!! asset('backend') !!}/js/jquery.nicescroll.js"></script>
    <script src="{!! asset('backend') !!}/js/jquery.slimscroll.js"></script>
    <script src="{!! asset('backend') !!}/js/jquery.scrollTo.min.js"></script>

    <script type="text/javascript" src="{!! asset('backend') !!}/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- App js -->
    <script src="{!! asset('backend') !!}/js/jquery.core.js"></script>
    <script src="{!! asset('backend') !!}/js/jquery.app.js"></script>

    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.easy-autocomplete.min.js') }}"></script>
    <script src="{{ asset('js/lodash.min.js') }}"></script>

    {{-- Sweetalert --}}
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    @yield('script')

    <script type="text/javascript">
        @if (session('message'))
            var alert = "{{ session('type') }}";
            var message = "{{ session('message') }}";
            switch (alert) {
                case "error":
                    toastr.error(message)
                    break;
                case "warning":
                    toastr.warning(message)
                    break;
                case "info":
                    toastr.info(message)
                    break;
                default:
                    toastr.success(message)
                    break;
            }
        @endif

        //Delete item
        $(document).on("click", "#delete", function(e) {
            e.preventDefault();
            var link = $(this).attr("href");
            swal({
                    title: "Are you Want to Delete?",
                    text: "Once Delete, This will be permanently Delete!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = link;
                        event.preventDefault();
                    } else {
                        swal("Cancelled", "Your Data Is Safe :)", "error");
                    }
                });
        });
    </script>

    <script type="text/javascript">
        $(".select2").select2({
            ordering: false,
        });
    </script>


    @stack('js')
</body>

</html>
