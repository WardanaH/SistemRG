<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900">
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CSS Material Dashboard -->
    <link href="{{ asset('assets/css/material-dashboard.css?v=3.0.0') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Notifikasi pakai pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

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
            border: 1px solid #d2d6da;
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
            height: 40px;
        }

        .select2-container--open .select2-selection--single {
            border-color: #e91e63;
            box-shadow: 0 0 0 2px rgba(233, 30, 99, 0.2);
        }

        .select2-dropdown {
            border-radius: 0.5rem !important;
            border: 1px solid #d2d6da !important;
        }

        /* ============================
           OVERRIDE WARNA & SIDEBAR
           ============================ */
        .bg-gradient-primary {
            background: linear-gradient(195deg, #42a5f5 0%, #1e88e5 100%) !important;
        }

        .sidenav .nav-link.active {
            background: linear-gradient(195deg, #ec407a, #d81b60) !important;
            box-shadow: 0 4px 20px rgba(216, 27, 96, .4) !important;
        }

        .sidenav .nav-link.active i {
            color: #ffffff !important;
        }

        /* =================
           KONDISI WARNA HEADER CARD
           ================= */
        .card-header .bg-gradient-primary:has(h6:contains("Tambah")) {
            background: linear-gradient(195deg, #43a047 0%, #2e7d32 100%) !important;
        }

        .card-header .bg-gradient-primary:has(h6:contains("Data")) {
            background: linear-gradient(195deg, #42a5f5 0%, #1e88e5 100%) !important;
        }

        .card-header .bg-gradient-primary,
        .card-header .bg-gradient-success,
        .card,
        .border-radius-lg {
            border-radius: 1rem !important;
        }

        /* =================
           CUSTOM TABEL
           ================= */
        .table {
            border-collapse: separate !important;
            border-spacing: 0;
            border: 1px solid #e0e0e0;
            border-radius: 1rem;
            background-color: #fff;
        }

        .table thead th,
        .table tbody td {
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0 !important;
        }

        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
            border-radius: 0.75rem;
        }

        .table thead th:last-child,
        .table tbody td:last-child {
            border-right: none !important;
        }

        .table tbody tr:last-child td {
            border-bottom: none !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(66, 165, 245, 0.06);
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">

    @include('distribusi.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('distribusi.layouts.navbar')

        <div class="container-fluid py-4">
            @yield('content')
            @include('distribusi.layouts.footer')
        </div>
    </main>

    <!-- jQuery & Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Core JS Material Dashboard -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>

    <!-- Material Dashboard JS -->
    <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.0.0') }}"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scrollbar Fix -->
    <script>
        if (navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                damping: '0.5'
            });
        }
    </script>

    <!-- Inisialisasi Select2 -->
    <script>
        $(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih data',
                allowClear: true
            });
        });

        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    <!-- PUSHER, LOCAL STORAGE & NOTIFIKASI V2 -->
    <script>
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });

        const isInventoryUtama = {{ Auth::check() && Auth::user()->hasRole('inventory utama') ? 'true' : 'false' }};
        const isInventoryCabang = {{ Auth::check() && Auth::user()->hasRole('inventory cabang') ? 'true' : 'false' }};
        const myCabangId = {{ Auth::check() ? Auth::user()->cabang_id ?? 'null' : 'null' }};

        // Bikin key local storage unik per user biar kada tecampur amun login akun lain
        const storageKey = 'notif_v2_' + {{ Auth::id() ?? 0 }};

        // Subscribe ka channel V2
        const channel = pusher.subscribe('distribusi-v2-channel');

        // 1. FUNGSI LOAD NOTIF DARI LOCAL STORAGE PAS HALAMAN DIMUAT
        function loadNotifFromStorage() {
            let storedNotifs = JSON.parse(localStorage.getItem(storageKey)) || [];

            if (storedNotifs.length > 0) {
                const empty = document.getElementById('notif-empty');
                if (empty) empty.remove();
            }

            // Render semua notif yg ada di storage
            storedNotifs.forEach(notif => {
                renderNotifUI(notif, 'append');
            });

            updateBadgeCount(storedNotifs.length, true);
        }

        // 2. LISTENER PUSHER PUSAT & CABANG
        channel.bind('notif-pusat', function(data) {
            if (!isInventoryUtama) return;
            simpanDanRenderNotif(data, 'Pusat');
        });

        channel.bind('notif-cabang', function(data) {
            if (!isInventoryCabang) return;
            if (data.gudang_cabang_id != myCabangId) return;
            simpanDanRenderNotif(data, 'Cabang');
        });

        // 3. FUNGSI SIMPAN KE STORAGE LALU TAMPILKAN
        function simpanDanRenderNotif(data, tipe) {
            let storedNotifs = JSON.parse(localStorage.getItem(storageKey)) || [];

            // Cek biar kada double
            if (!storedNotifs.some(n => n.id === data.id)) {
                let notifData = {
                    id: data.id,
                    pesan: data.pesan,
                    cabang: data.cabang,
                    tipe: tipe
                };

                // Masukkan ka urutan paling atas
                storedNotifs.unshift(notifData);
                localStorage.setItem(storageKey, JSON.stringify(storedNotifs));

                const empty = document.getElementById('notif-empty');
                if (empty) empty.remove();

                renderNotifUI(notifData, 'prepend');
                updateBadgeCount(1, false);

                // Mainkan suara & alert
                let audio = new Audio('{{ asset('assets/sound/notif_spk_masuk.mp3') }}');
                audio.play().catch(() => {});

                Swal.fire({
                    title: 'Notifikasi Baru',
                    text: data.pesan,
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        }

        // 4. FUNGSI MENGGAMBAR ELEMEN HTML NOTIFIKASI
        function renderNotifUI(data, method) {
            const body = document.getElementById('notif-body');
            if (!body) return;

            // Atur target redirect otomatis berdasarkan role
            let targetUrl = isInventoryUtama ?
                "{{ route('gudang-pusat.permintaan.masuk') }}" :
                "{{ route('gudang-cabang.permintaan.penerimaan') }}";

            let html = '';
            if (data.tipe === 'Pusat') {
                html = `
                    <a href="${targetUrl}" class="dropdown-item notification-item unread py-2" data-id="${data.id}">
                        <div class="d-flex align-items-center">
                            <div class="notif-icon bg-warning text-white rounded-circle p-2 me-3 shadow-sm">
                                <i class="fa fa-truck"></i>
                            </div>
                            <div class="notif-content">
                                <h6 class="text-sm font-weight-bold mb-0">Permintaan Masuk</h6>
                                <p class="text-xs text-secondary mb-0">Dari <strong>${data.cabang}</strong></p>
                            </div>
                        </div>
                    </a>`;
            } else if (data.tipe === 'Cabang') {
                html = `
                    <a href="${targetUrl}" class="dropdown-item notification-item unread py-2" data-id="${data.id}">
                        <div class="d-flex align-items-center">
                            <div class="notif-icon bg-success text-white rounded-circle p-2 me-3 shadow-sm">
                                <i class="fa fa-box"></i>
                            </div>
                            <div class="notif-content">
                                <h6 class="text-sm font-weight-bold mb-0">Update Status</h6>
                                <p class="text-xs text-secondary mb-0">${data.pesan}</p>
                            </div>
                        </div>
                    </a>`;
            }

            if (method === 'prepend') {
                body.insertAdjacentHTML('afterbegin', html);
            } else {
                body.insertAdjacentHTML('beforeend', html);
            }
        }

        // 5. FUNGSI UPDATE ANGKA BADGE MERAH
        function updateBadgeCount(count, isExact = false) {
            let badge = document.getElementById('badge-notif');
            let bell = document.querySelector('.notification-wrapper a');

            if (isExact) {
                if (count > 0) {
                    if (!badge && bell) {
                        bell.insertAdjacentHTML('beforeend',
                            `<span id="badge-notif" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${count}</span>`
                        );
                    } else if (badge) {
                        badge.innerText = count;
                    }
                } else if (badge) {
                    badge.remove();
                }
            } else {
                if (!badge && bell) {
                    bell.insertAdjacentHTML('beforeend',
                        `<span id="badge-notif" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">1</span>`
                    );
                } else if (badge) {
                    badge.innerText = parseInt(badge.innerText) + count;
                }
            }
        }

        // 6. EVENT LISTENER KLIK NOTIFIKASI
        document.addEventListener('click', function(e) {
            const item = e.target.closest('.notification-item.unread');
            if (!item) return;

            // Hapus notif ini dari local storage biar pas pindah halaman kada muncul lagi
            const notifId = item.dataset.id;
            let storedNotifs = JSON.parse(localStorage.getItem(storageKey)) || [];

            storedNotifs = storedNotifs.filter(n => n.id != notifId);
            localStorage.setItem(storageKey, JSON.stringify(storedNotifs));

            // Note: e.preventDefault() sengaja kada dipakai disini
            // supaya tag <a href="..."> bekerja natural dan me-redirect halaman
        });

        // Eksekusi load data pas pertama kali web direfresh
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifFromStorage();
        });
    </script>

    @stack('scripts')
</body>

</html>
