@php
    use App\Helpers\Helper;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="X3-TECH" />
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon" />
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name') }}</title>

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
        rel="stylesheet" />
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fontawesome.css') }}" />
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}" />
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}" />
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}" />
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}" />
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick-theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <!-- Plugins css Ends-->
    @yield('styles')
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="/assets/css/vendors/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/main.css') }}">
    <!-- App css-->
    @vite(['public/assets/scss/style.scss'])
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css" />
    <script defer src="/assets/css/color-1.js"></script>
    <script defer src="/assets/css/responsive.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/toastr.min.css') }}">
    <script defer src="/assets/css/style.js"></script>
</head>

<body>
    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="loader-index"><span></span></div>
        <svg>
            <defs></defs>
            <filter id="goo">
                <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
                <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo">
                </fecolormatrix>
            </filter>
        </svg>
    </div>
    <!-- loader ends-->

    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.user.header')
        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            @include('layouts.user.sidebar')

            <div class="page-body">
                @yield('main_content')
            </div>

            @include('layouts.user.footer')
        </div>
        @include('components.alerts')
        <!-- latest jquery-->
        <script src="/assets/js/jquery.min.js"></script>
        <!-- Bootstrap js-->
        <script src="/assets/js/bootstrap.bundle.min.js"></script>
        <!-- feather icon js-->
        <script src="/assets/js/icons/feather.min.js"></script>
        <script src="/assets/js/icons/feather-icon.js"></script>
        <!-- scrollbar js-->
        <script src="{{ asset('assets/js/scrollbar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
        <!-- Sidebar jquery-->
        <script src="/assets/js/config.js"></script>
        <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
        <script src="{{ asset('assets/js/sidebar-pin.js') }}"></script>
        <script src="{{ asset('assets/js/slick/slick.min.js') }}"></script>
        <script src="{{ asset('assets/js/slick/slick.js') }}"></script>
        <script src="{{ asset('assets/js/header-slick.js') }}"></script>
        <!-- Status Update-->
        @yield('scripts')
        <!-- Plugins JS start--><!-- Plugins JS Ends--><!-- Theme js-->
        <script src="/assets/js/script.js"></script>
        <script src="/assets/js/script1.js"></script>
        <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
        <script>
            // Setup AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() {
                $(document).on('change', '.toggle-status', function() {

                    let status = $(this).prop('checked') ? 1 : 0;
                    let url = $(this).data('route');
                    console.log(url);
                    let clickedToggle = $(this);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            status: status,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            clickedToggle.prop('checked', status);
                            toastr.success("Status Berhasil Diperbarui");
                        },
                        error: function(xhr, status, error) {
                            console.log(error)
                        }
                    });
                });
            });
        </script>
</body>

</html>
