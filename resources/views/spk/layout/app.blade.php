<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} - Sistem SPK RG</title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900">
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- CSS -->
    @stack('styles')
    <link href="{{ asset('assets/css/material-dashboard.css?v=3.0.0') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
            background-color: transparent;
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

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
</head>

<body class="g-sidenav-show bg-gray-200">

    @include('spk.layout.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('spk.layout.navbar')

        <div class="container-fluid py-4">
            @yield('content')
            @include('spk.layout.footer')
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

    <!-- select 2-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih data',
                allowClear: true
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. AKTIFKAN LOGGING (PENTING BUAT DEBUG)
        Pusher.logToConsole = true;

        // 2. Inisialisasi Pusher
        var pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            encrypted: true
        });

        // Cek di Console Browser nanti, harusnya config key & cluster tidak ada spasi
        console.log("Config Pusher:", {
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}'
        });

        const isAdmin = {{ Auth::check() && Auth::user() -> hasRole('admin') ? 'true' : 'false' }};
        console.log("Status Admin:", isAdmin);

        var channel = pusher.subscribe('channel-admin');

        // Binding Event
        channel.bind('spk-dibuat', function(data) {

            console.log("EVENT DITERIMA:", data); // Harus muncul jika koneksi sukses

            if (isAdmin) {
                playNotificationSound();
                Swal.fire({
                    title: 'SPK Baru Masuk!',
                    text: data.pesan + ' (No: ' + data.no_spk + ')',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Lihat SPK',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ url('/spk') }}?search=" + data.no_spk;
                    }
                });
            }
        });

        function playNotificationSound() {
            let audio = new Audio('{{ asset("assets/sound/notif_spk.mp3") }}');
            audio.play().catch(function(error) {
                console.log("Audio error: " + error);
            });
        }
    </script>

    @stack('scripts')

</body>

</html>
