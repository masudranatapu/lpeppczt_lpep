<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="/logo.jpeg">

    <title>LPEP</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="{!! asset('backend') !!}/plugins/morris/morris.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/remove-number-arrows.css') }}">

    <!-- form Uploads -->

    <link href="{!! asset('backend') !!}/plugins/fileuploads/css/dropify.min.css" rel="stylesheet" type="text/css" />

    <link href="{!! asset('backend') !!}/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
    <link href="{!! asset('backend') !!}/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{!! asset('backend') !!}/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css"
        rel="stylesheet" />

    <!-- DataTables -->
    <link href="{!! asset('backend') !!}/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{!! asset('backend') !!}/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{!! asset('backend') !!}/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />
    <!-- App css -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/responsive-table/css/rwd-table.min.css">
    <link href="{!! asset('backend') !!}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{!! asset('backend') !!}/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="{!! asset('backend') !!}/css/style.css" rel="stylesheet" type="text/css" />
    <link href="{!! asset('backend') !!}/css/fontawesome/css/all.min.css" rel="stylesheet" type="text/css" />

    <script src="{!! asset('backend') !!}/js/modernizr.min.js"></script>

    @include('inc.themStyle')
    @include('inc.select2-custom-style')
    @stack('css')

    @if (session()->get('locale') == 'bn')
        <link href="https://fonts.maateen.me/adorsho-lipi/font.css" rel="stylesheet">
        <style>
            body {
                font-family: 'AdorshoLipi', Arial, sans-serif !important;
            }

        </style>
    @endif


    <style>
        #sidebar-menu ul li .menu-arrow {
            font-family: 'Font Awesome 5 Free' !important;
            font-weight: 900 !important;
        }

    </style>

</head>


<body class="fixed-left">

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Top Bar Start -->
        @include('inc.topbar')
        <!-- Top Bar End -->


        <!-- ========== Left Sidebar Start ========== -->
        @include('inc.sidebar')
        <!-- Left Sidebar End -->
