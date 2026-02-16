<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} - Sistem SPK RG</title>
    <link rel="shortcut icon" href="{{ asset('image-company/icon.webp') }}">

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
            $('.select2').each(function() {
                $(this).select2({
                    width: '100%',
                    // Mengambil placeholder dari atribut data-placeholder atau default ke 'Pilih data'
                    placeholder: $(this).data('placeholder') || 'Pilih data',
                    allowClear: true,
                    // Jika select2 berada di dalam Modal, tambahkan ini agar tidak error fokus
                    dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : null
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            encrypted: true
        });

        const isAdmin = {{ Auth::check() && Auth::user()->hasRole('admin') ? 'true' : 'false' }};
        const authUserId = "{{ auth()->id() }}";
        const cabangId = "{{ Auth::check() ? Auth::user()->cabang_id : 'null' }}";

        // Variabel untuk menyimpan interval suara agar bisa dihentikan
        let alertInterval = null;

        // 1. Fungsi Universal untuk Notifikasi
        function showNotif(title, data, redirectUrl = null) {
            const soundPath = data.operator_id ? "assets/sound/tugas_baru.mp3" : "assets/sound/notif_spk.mp3";
            const fullSoundPath = `{{ asset('') }}${soundPath}`;

            // Hentikan interval lama jika ada notif baru masuk beruntun
            if (alertInterval) clearInterval(alertInterval);

            // Fungsi untuk memutar suara
            const playAlert = () => {
                new Audio(fullSoundPath).play().catch(e => console.log("Audio play blocked by browser. User must interact first."));
            };

            // Putar pertama kali
            playAlert();

            // Set Interval untuk putar ulang suara setiap 10 detik jika notif belum ditutup
            alertInterval = setInterval(playAlert, 10000);

            updateBadgeNavbar();

            Swal.fire({
                title: title,
                html: `<p>${data.pesan || ''}</p><small>No: <b>${data.no_spk}</b></small><br><small>${data.nama_file || ''}</small>`,
                icon: 'info',
                position: 'top-end',
                toast: !isAdmin,
                showConfirmButton: true, // Pakai tombol agar user dipaksa klik (menghentikan suara)
                
                confirmButtonText: redirectUrl ? 'Lihat' : 'Oke, Mengerti',
                timer: 30000, // Durasi notif lebih lama (30 detik) agar tidak cepat hilang
                timerProgressBar: true,
                didClose: () => {
                    // Berhenti memutar suara saat notifikasi ditutup (baik klik oke/X/timer habis)
                    clearInterval(alertInterval);
                    alertInterval = null;
                }
            }).then((result) => {
                if (result.isConfirmed && redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        }

        // 2. Langganan Channel & Bind Event
        if (isAdmin) {
            pusher.subscribe('channel-admin-' + cabangId).bind('spk-dibuat', (data) => {
                let url = data.tipe === 'Reguler' ? "{{ url('/spk') }}" : "{{ url('/spk-bantuan') }}";
                showNotif(`SPK ${data.tipe} Baru!`, data, `${url}?search=${data.no_spk}`);
            });

            pusher.subscribe('channel-lembur').bind('spk-lembur-dibuat', (data) => {
                showNotif('SPK Lembur Baru!', data, `{{ url('/spk-lembur') }}?search=${data.no_spk}`);
            });
        }

        pusher.subscribe('operator.' + authUserId).bind('kerjaan-baru', (data) => {
            showNotif('Tugas Baru Masuk!', data);
            if(window.location.pathname.includes('tugas-operator')) {
                // Beri jeda sedikit sebelum reload agar user bisa melihat notif dulu
                setTimeout(() => { location.reload(); }, 2000);
            }
        });

        // 3. Fungsi Pendukung
        function updateBadgeNavbar() {
            let badge = document.getElementById('badge-notif');
            if (badge) {
                badge.innerText = (parseInt(badge.innerText) || 0) + 1;
                badge.style.display = 'inline-block';
            }
        }
    </script>

    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Mencegah link bekerja langsung

            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true // Tombol Batal di kiri, Ya di kanan (opsional)
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user klik "Ya", submit form logout
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    @stack('scripts')

</body>

</html>
