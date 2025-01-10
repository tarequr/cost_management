<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>Cost Management</title>
    <meta content="Responsive admin theme build on top of Bootstrap 4" name="description" />
    <meta content="Themesdesign" name="author" />
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">



    <!--Morris Chart CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('backend/plugins/morris/morris.css') }}"> --}}

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/metismenu.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/assets/css/style.css') }}" rel="stylesheet" type="text/css">

    <!-- DataTables -->
    <link href="{{ asset('backend/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('backend/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />

    @stack('css')

    {{-- <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet"> --}}
</head>

<body>
    <!-- Begin page -->
    <div id="wrapper">
        @include('backend.partials.header')
        @include('backend.partials.sidebar')

        <div class="content-page">
            @yield('content')
            @include('backend.partials.footer')
        </div>
    </div>
    <!-- END wrapper -->

    <!-- jQuery  -->
    <script src="{{ asset('backend/assets/js/jquery.min.js') }}"></script>

    <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/metismenu.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('backend/assets/js/waves.min.js') }}"></script>

    <!--Morris Chart-->
    {{-- <script src="{{ asset('backend/plugins/morris/morris.min.js') }}"></script> --}}
    <script src="{{ asset('backend/plugins/raphael/raphael.min.js') }}"></script>

    {{-- <script src="{{ asset('backend/assets/pages/dashboard.init.js') }}"></script> --}}

    <!-- Required datatable js -->
    <script src="{{ asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('backend/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('backend/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('backend/assets/pages/datatables.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script> --}}

    {{-- <script type="text/javascript" src="{{ asset('js/sweetalert2@11.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/customSweetalert2.js') }}"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    {{-- <script src="{{ asset('js/iziToast.js') }}"></script>

    @include('vendor.lara-izitoast.toast') --}}

    @stack('js')

    <script>
        $('.select2').select2({
            width: '100%'
        });
    </script>
</body>

</html>
