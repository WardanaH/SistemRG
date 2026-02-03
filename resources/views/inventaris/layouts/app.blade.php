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

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
    /* ===============================
    SELECT2 GLOBAL - MATERIAL LOOK
    ================================ */

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 40px;
        border-radius: 0.4rem;
        /* border: 1px solid #1a73e8; */
        padding: 8px 12px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        color: #344767;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 45px;
    }

    /* focus */
    .select2-container--open .select2-selection--single {
        border-color: #1a73e8;
        box-shadow: 0 0 0 0.15rem rgba(26, 115, 232, 0.25);
    }

    .select2-dropdown {
        border-radius: 0.75rem !important;
        border: 1px solid #1a73e8 !important;
    }

    </style>


    <style>
    /* ============================
    OVERRIDE WARNA PRIMARY (PINK → BIRU)
    ============================ */

    /* tombol / menu aktif */
    .bg-gradient-primary {
        background: linear-gradient(195deg, #42a5f5 0%, #1e88e5 100%) !important;
    }

    /* sidebar active link */
    .sidenav .nav-link.active {
        background: linear-gradient(195deg, #42a5f5 0%, #1e88e5 100%) !important;
        box-shadow: 0 4px 20px rgba(30, 136, 229, 0.4);
    }

    /* icon di menu aktif */
    .sidenav .nav-link.active i {
        color: #ffffff !important;
    }

    /* =================
   TAMBAH BARANG (HIJAU)
   ================= */
    .card-header .bg-gradient-primary:has(h6:contains("Tambah")) {
        background: linear-gradient(195deg, #43a047 0%, #2e7d32 100%) !important;
    }

    /* =================
    DATA BARANG (BIRU)
    ================= */
    .card-header .bg-gradient-primary:has(h6:contains("Data")) {
        background: linear-gradient(195deg, #42a5f5 0%, #1e88e5 100%) !important;
    }
    
    /* border luar tabel */
    .table {
        border-collapse: separate !important;
        border-spacing: 0;
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        overflow: hidden;
        background-color: #fff;
    }

    /* header */
    .table thead th {
        background-color: #f8f9fa;
        color: #344767;
        font-weight: 600;
        border-bottom: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        white-space: nowrap;
    }

    /* isi tabel */
    .table tbody td {
        border-bottom: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        color: #495057;
        vertical-align: middle;
    }

    /* hapus border kanan terakhir */
    .table thead th:last-child,
    .table tbody td:last-child {
        border-right: none;
    }

    /* hapus border bawah baris terakhir */
    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* hover */
    .table-hover tbody tr:hover {
        background-color: rgba(66, 165, 245, 0.06);
    }

    /* responsive fix */
    .table-responsive {
        border-radius: 0.75rem;
    }
    </style>

</head>

<body class="g-sidenav-show bg-gray-200">

@include('inventaris.layouts.sidebar')

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @include('inventaris.layouts.navbar')

    <div class="container-fluid py-4">
        @yield('content')
        @include('inventaris.layouts.footer')
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

<!-- js sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Scrollbar Fix -->
<script>
  if (navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
      damping: '0.5'
    });
  }
</script>

<!-- select 2-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Pilih data',
        allowClear: true
    });
});
</script>


@stack('scripts')

</body>
</html>
