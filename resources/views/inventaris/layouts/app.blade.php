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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <!-- CSS -->
    <link href="{{ asset('assets/css/material-dashboard.css?v=3.0.0') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- notifikasi pakai pusher -->
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

/* ============================
SIDEBAR ACTIVE → PINK
============================ */
.sidenav .nav-link.active {
    background: linear-gradient(195deg, #ec407a, #d81b60) !important;
    box-shadow: 0 4px 20px rgba(216,27,96,.4) !important;
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

    /* card warna warni */
    .card-header .bg-gradient-primary,
    .card-header .bg-gradient-success {
        border-radius: 1rem !important;
    }

    /* kelengkungan tabel */
    .card,
    .border-radius-lg {
        border-radius: 1rem !important; /* default ±0.75rem */
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
    border-radius: 1rem;
    background-color: #fff;
}

.table thead th,
.table tbody td {
    border-bottom: 1px solid #e0e0e0;
    border-right: 1px solid #e0e0e0!important;
}

.table-responsive {
    overflow-x: auto;
    overflow-y: visible;
    border-radius: 0.75rem;
}

    /* hapus border kanan terakhir */
    .table thead th:last-child,
    .table tbody td:last-child {
        border-right: none;
    }

    .table tbody tr:last-child td {
    border-bottom: 1px solid #e0e0e0 !important;
}


    /* hover */
    .table-hover tbody tr:hover {
        background-color: rgba(66, 165, 245, 0.06);
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
/* =========================================================
   PUSHER INIT
   ========================================================= */
Pusher.logToConsole = true;

const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
    forceTLS: true
});

const isInventoryUtama = {{ Auth::check() && Auth::user()->hasRole('inventory utama') ? 'true' : 'false' }};
const isInventoryCabang = {{ Auth::check() && Auth::user()->hasRole('inventory cabang') ? 'true' : 'false' }};

const channel = pusher.subscribe('inventaris-channel');

/* =========================================================
   EVENT PUSHER
   ========================================================= */
channel.bind('inventaris-notif', function (data) {

    if (data.role === 'inventory utama' && !isInventoryUtama) return;
    if (data.role === 'inventory cabang' && !isInventoryCabang) return;

    /* ===============================
       1. UPDATE BADGE
       =============================== */
    let badge = document.getElementById('badge-notif');

    if (!badge) {
        // HAPUS EMPTY STATE JIKA ADA
        const empty = document.getElementById('notif-empty');
        if (empty) empty.remove();

        const bell = document.querySelector('.notification-wrapper a');
        bell.insertAdjacentHTML('beforeend',
            `<span id="badge-notif"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                1
            </span>`
        );
    } else {
        let count = parseInt(badge?.innerText || 0);
        count++;
        badge.innerText = count;
        badge.style.display = 'inline-block';
    }

    /* ===============================
       2. TAMBAH ITEM KE DROPDOWN
       =============================== */
    const body = document.getElementById('notif-body');
    if (body) {

        let html = '';

        if (isInventoryUtama) {
            html = `
                <div class="notification-item unread" data-id="${data.id}">
                    <div class="notif-icon bg-warning">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">Permintaan Pengiriman</div>
                        <div class="notif-text">
                            Dari <strong>${data.cabang}</strong>
                        </div>
                        <div class="notif-time">Baru saja</div>
                    </div>
                </div>
            `;
        }

        if (isInventoryCabang) {
            html = `
                <div class="notification-item unread" data-id="${data.id}">
                    <div class="notif-icon bg-success">
                        <i class="fa fa-box"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">Barang Dikirim</div>
                        <div class="notif-time">Baru saja</div>
                    </div>
                </div>
            `;
        }

        body.insertAdjacentHTML('afterbegin', html);
    }

    /* ===============================
       3. AUDIO (TIDAK DIUBAH)
       =============================== */
    let audio = new Audio('{{ asset("assets/sound/notif_spk.mp3") }}');
    audio.play().catch(() => {});

    /* ===============================
       4. SWEETALERT 3 DETIK
       =============================== */
    Swal.fire({
        title: 'Notifikasi Baru',
        text: data.pesan,
        icon: 'info',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    }).then(() => {
        document.querySelector('.notification-wrapper > a')?.click();
    });

});

/* =========================================================
   CLICK NOTIF (READ AT) — EVENT DELEGATION
   ========================================================= */
document.addEventListener('click', function (e) {

    const item = e.target.closest('.notification-item.unread');
    if (!item) return;

    const notifId = item.dataset.id;
    let url = '';

    @if(auth()->user()->hasRole('inventory utama'))
        url = "{{ route('permintaan.pusat.read', ':id') }}".replace(':id', notifId);
    @elseif(auth()->user()->hasRole('inventory cabang'))
        url = "{{ route('gudangcabang.notif.read', ':id') }}".replace(':id', notifId);
    @endif

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(() => {

        item.classList.remove('unread');

        const badge = document.getElementById('badge-notif');
        if (badge) {
            let count = parseInt(badge.innerText) - 1;
            count <= 0 ? badge.remove() : badge.innerText = count;
        }

    });
});
</script>


@stack('scripts')

</body>
</html>
