<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900">
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('assets/css/material-dashboard.css?v=3.0.0') }}" rel="stylesheet">
</head>

<body class="g-sidenav-show bg-gray-200">

@include('layouts.sidebar')

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @include('layouts.navbar')

    <div class="container-fluid py-4">
        @yield('content')
        @include('layouts.footer')
    </div>
</main>

<!-- Core JS -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>

<!-- Material Dashboard -->
<script src="{{ asset('assets/js/material-dashboard.min.js?v=3.0.0') }}"></script>

<!-- Scrollbar Fix -->
<script>
  if (navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
      damping: '0.5'
    });
  }
</script>

@stack('scripts')

</body>
</html>
