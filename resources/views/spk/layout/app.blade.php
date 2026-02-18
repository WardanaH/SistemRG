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
        // 1. Inisialisasi Pusher
        const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
            encrypted: true
        });

        const isAdmin = {{ Auth::check() && Auth::user()->hasRole('admin') ? 'true' : 'false' }};
        const authUserId = "{{ auth()->id() }}";
        const cabangId = "{{ Auth::check() ? Auth::user()->cabang_id : 'null' }}";

        let alertInterval = null; // Untuk looping suara
        let reminderTimeout = null; // Untuk memunculkan kembali Swal yang ditutup paksa

        // --- FUNGSI LOGIKA DATA ---

        // Menangani notifikasi baru yang masuk
        function handleIncomingNotif(title, data, redirectUrl = null) {
            let notifications = JSON.parse(localStorage.getItem('notif_list')) || [];

            const newNotif = {
                id: Date.now(),
                title: title,
                pesan: data.pesan || '',
                no_spk: data.no_spk || '',
                nama_file: data.nama_file || '',
                url: redirectUrl,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                is_operator: !!data.operator_id
            };

            notifications.unshift(newNotif);
            if(notifications.length > 10) notifications.pop(); // Batasi 10 notif terakhir

            localStorage.setItem('notif_list', JSON.stringify(notifications));
            localStorage.setItem('pending_active_notif', JSON.stringify(newNotif)); // Notif yang sedang "berisik"

            renderNotifList();
            triggerNotifUI();
        }

        // Menjalankan UI (Suara + SweetAlert)
        function triggerNotifUI() {
            const activeNotif = JSON.parse(localStorage.getItem('pending_active_notif'));
            if (!activeNotif) return;

            // Bersihkan interval lama
            if (alertInterval) clearInterval(alertInterval);
            if (reminderTimeout) clearTimeout(reminderTimeout);

            const soundPath = activeNotif.is_operator ? "assets/sound/tugas_baru.mp3" : "assets/sound/notif_spk.mp3";
            const audio = new Audio(`{{ asset('') }}${soundPath}`);

            const playAlert = () => {
                audio.play().catch(e => console.log("Interaksi user diperlukan untuk suara."));
            };

            playAlert();
            alertInterval = setInterval(playAlert, 10000); // Ulangi suara setiap 10 detik

            Swal.fire({
                title: activeNotif.title,
                html: `<div class="text-start">
                        <p class="mb-1">${activeNotif.pesan}</p>
                        <small>No: <b>${activeNotif.no_spk}</b></small><br>
                        <small class="text-truncate d-block">${activeNotif.nama_file}</small>
                    </div>`,
                icon: 'info',
                position: 'top-end',
                toast: !isAdmin,
                showConfirmButton: true,
                confirmButtonText: activeNotif.url ? 'Buka / Lihat' : 'Tandai Dibaca',
                showCancelButton: true,
                cancelButtonText: 'Nanti Saja',
                timer: 20000,
                timerProgressBar: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Berhenti Total
                    stopNotificationLoop();
                    if (activeNotif.url) window.location.href = activeNotif.url;
                } else {
                    // User klik "Nanti" atau Timer habis: Diam sebentar, lalu muncul lagi
                    clearInterval(alertInterval);
                    reminderTimeout = setTimeout(triggerNotifUI, 60000); // Muncul lagi dalam 40 detik
                }
            });
        }

        function stopNotificationLoop() {
            localStorage.removeItem('pending_active_notif');
            clearInterval(alertInterval);
            clearTimeout(reminderTimeout);
            alertInterval = null;
            reminderTimeout = null;
        }

        // --- FUNGSI TAMPILAN DROPDOWN ---

        function renderNotifList() {
            const listContainer = document.getElementById('dropdown-notif-list');
            const badge = document.getElementById('badge-notif');
            if (!listContainer) return;

            let notifications = JSON.parse(localStorage.getItem('notif_list')) || [];

            if (notifications.length > 0) {
                badge.innerText = notifications.length;
                badge.style.display = 'inline-block';

                let html = notifications.map(n => `
                    <li class="mb-2 border-bottom pb-2">
                        <a class="dropdown-item border-radius-md" href="${n.url || 'javascript:;'}" onclick="clearSingleNotif(${n.id})">
                            <div class="d-flex py-1">
                                <div class="my-auto">
                                    <i class="material-icons text-primary me-3">assignment</i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">
                                        <span class="font-weight-bold">${n.title}</span>
                                    </h6>
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="fa fa-clock me-1"></i> ${n.time} | ${n.no_spk}
                                    </p>
                                </div>
                            </div>
                        </a>
                    </li>
                `).join('');

                html += `<li><a class="dropdown-item text-center text-primary text-xs font-weight-bold" href="javascript:;" onclick="clearAllNotif()">Hapus Semua</a></li>`;
                listContainer.innerHTML = html;
            } else {
                badge.style.display = 'none';
                listContainer.innerHTML = '<li class="p-2 text-center"><p class="text-xs text-secondary mb-0">Tidak ada notifikasi</p></li>';
            }
        }

        function clearSingleNotif(id) {
            let notifications = JSON.parse(localStorage.getItem('notif_list')) || [];
            localStorage.setItem('notif_list', JSON.stringify(notifications.filter(n => n.id !== id)));
            stopNotificationLoop();
            renderNotifList();
        }

        function clearAllNotif() {
            localStorage.removeItem('notif_list');
            stopNotificationLoop();
            renderNotifList();
        }

        // --- BROADCAST LISTENER ---

        if (isAdmin) {
            pusher.subscribe('channel-admin-' + cabangId).bind('spk-dibuat', (data) => {
                let url = data.tipe === 'Reguler' ? "{{ url('/spk') }}" : "{{ url('/spk-bantuan') }}";
                handleIncomingNotif(`SPK ${data.tipe} Baru!`, data, `${url}?search=${data.no_spk}`);
            });

            pusher.subscribe('channel-lembur').bind('spk-lembur-dibuat', (data) => {
                handleIncomingNotif('SPK Lembur Baru!', data, `{{ url('/spk-lembur') }}?search=${data.no_spk}`);
            });
        }

        pusher.subscribe('operator.' + authUserId).bind('kerjaan-baru', (data) => {
            // Log untuk ngecek apakah tipe sudah masuk
            console.log("Data tipe order masuk:", data.tipe);

            if (data.tipe === 'advertising') {
                // Sesuaikan nama route ini dengan yang ada di web.php Anda
                handleIncomingNotif('Tugas Advertising Masuk!', data, "{{ route('advertising.produksi-index') }}");
            }
            else if (data.tipe === 'lembur') {
                handleIncomingNotif('Tugas Lembur Masuk!', data, "{{ route('spk-lembur.produksi') }}");
            }
            else if (data.tipe === 'bantuan') {
                handleIncomingNotif('Tugas Bantuan Masuk!', data, "{{ route('spk-bantuan.produksi') }}");
                // Catatan: Biasanya SPK Bantuan numpuk di antrean reguler, kalau halamannya dipisah, sesuaikan routenya.
            }
            else {
                handleIncomingNotif('Tugas Reguler Masuk!', data, "{{ route('spk.produksi') }}");
            }
        });

        // Jalankan saat pertama kali buka halaman atau refresh
        window.addEventListener('load', () => {
            renderNotifList();
            if (localStorage.getItem('pending_active_notif')) {
                setTimeout(triggerNotifUI, 3000); // Munculkan kembali setelah 3 detik loading halaman
            }
        });
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
