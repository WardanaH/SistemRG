@php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();

/*
|--------------------------------------------------------------------------
| AREA TITLE (BERDASARKAN ROLE)
|--------------------------------------------------------------------------
*/
if ($user->hasRole('inventory utama')) {
$areaTitle = 'Gudang Pusat';
} elseif ($user->hasRole('inventory cabang')) {
$areaTitle = 'Gudang Cabang';
} else {
$areaTitle = 'Dashboard';
}

/*
|--------------------------------------------------------------------------
| PAGE TITLE (BERDASARKAN ROUTE)
|--------------------------------------------------------------------------
*/
$pageTitle = match (true) {

request()->routeIs('gudangpusat.dashboard') => 'Dashboard',
request()->routeIs('gudangcabang.dashboard') => 'Dashboard',

request()->routeIs('barang.pusat*') => 'Data Barang',
request()->routeIs('gudangcabang.barang*') => 'Data Barang Cabang',

request()->routeIs('pengiriman.*') => 'Pengiriman Barang',
request()->routeIs('gudangcabang.penerimaan*') => 'Penerimaan Barang',

request()->routeIs('permintaan.*') => 'Permintaan Pengiriman',
request()->routeIs('gudangcabang.permintaan.*') => 'Permintaan Pengiriman',

request()->routeIs('gudangcabang.inventaris.index') => 'Inventaris Barang',
request()->routeIs('gudangcabang.inventaris.create') => 'Tambah Inventaris',
request()->routeIs('gudangcabang.inventaris.edit') => 'Edit Inventaris',

default => null,
};
/*
|--------------------------------------------------------------------------
| NOTIFIKASI
|--------------------------------------------------------------------------
*/
if ($user->hasRole('inventory utama')) {

$notifications = \App\Models\MPermintaanPengiriman::with('cabang')
->where('status', 'Menunggu')
->where('created_at', '>=', now()->subDays(3))
->orderByDesc('created_at')
->get();

$unreadCount = $notifications->whereNull('read_at')->count();

} elseif ($user->hasRole('inventory cabang')) {

$notifications = \App\Models\MPengiriman::where('cabang_tujuan_id', $user->cabang_id)
->where('status_pengiriman', 'Dikirim')
->where('created_at', '>=', now()->subDays(3))
->orderByDesc('created_at')
->get();

$unreadCount = $notifications->whereNull('read_at')->count();

} else {
$notifications = collect();
$unreadCount = 0;
}
@endphp

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">

        {{-- =====================
        BREADCRUMB & TITLE
        ===================== --}}
<div class="mobile-header-left">

    {{-- HAMBURGER DI KIRI --}}
    <a href="javascript:;" class="nav-link text-body p-0 mobile-hamburger" id="iconNavbarSidenav">
        <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
        </div>
    </a>

    {{-- TITLE --}}
    <h6 class="font-weight-bolder mb-0 mobile-title">
        {{ $pageTitle ?? $areaTitle }}
    </h6>

</div>


        {{-- =====================
        RIGHT NAVBAR
        ===================== --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 mobile-right-menu">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- SEARCH --}}
                <li class="nav-item me-3">
                    <div class="search_bar dropdown">
                        <span class="search_icon p-3 c-pointer" data-bs-toggle="dropdown">
                            <i class="mdi mdi-magnify"></i>
                        </span>
                        <div class="dropdown-menu p-0 m-0">
                            <form>
                                <input class="form-control" type="search" placeholder="Search">
                            </form>
                        </div>
                    </div>
                </li>

                {{-- =====================
                NOTIFIKASI
                ===================== --}}
                <li class="nav-item pe-2 notification-wrapper">
                    <a class="nav-link position-relative"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <i class="fa fa-bell fa-lg"></i>

                        @if($unreadCount > 0)
                        <span id="badge-notif" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>

                    <div class="dropdown-menu notification-bubble p-0">

                        <div class="notification-header">
                            Notifikasi
                        </div>

                        <div class="notification-body" id="notif-body" data-source="server">

                            @forelse($notifications as $note)

                            {{-- INVENTORY UTAMA --}}
                            @if($user->hasRole('inventory utama'))
                            <div class="notification-item {{ is_null($note->read_at) ? 'unread' : '' }}"
                                data-id="{{ $note->id }}">

                                <div class="notif-icon bg-warning">
                                    <i class="fa fa-truck"></i>
                                </div>

                                <div class="notif-content">
                                    <div class="notif-title">
                                        Permintaan Pengiriman
                                    </div>
                                    <div class="notif-text">
                                        Dari <strong>{{ $note->cabang->nama }}</strong>
                                    </div>
                                    <div class="notif-time">
                                        {{ $note->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- INVENTORY CABANG --}}
                            @elseif($user->hasRole('inventory cabang'))
                            <div class="notification-item {{ is_null($note->read_at) ? 'unread' : '' }}"
                                data-id="{{ $note->id }}">

                                <div class="notif-icon bg-success">
                                    <i class="fa fa-box"></i>
                                </div>

                                <div class="notif-content">
                                    <div class="notif-title">
                                        Barang Dikirim
                                    </div>
                                    <div class="notif-time">
                                        {{ $note->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            @endif
                            @empty
                            <div class="notification-empty" id="notif-empty">
                                Tidak ada notifikasi
                            </div>
                            @endforelse

                        </div>

                        @if($user->hasRole('inventory utama') && $notifications->count())
                        <div class="notification-footer text-center">
                            <small class="text-muted">
                                Klik notifikasi untuk menandai sebagai dibaca
                            </small>
                        </div>
                        @endif

                    </div>
                </li>

                {{-- B. LOGOUT --}}
                <li class="nav-item d-flex align-items-center">
                    <a href="javascript:;" onclick="confirmLogout(event)" class="nav-link text-body font-weight-bold px-0">
                        <i class="material-icons-round opacity-10">logout</i>
                    </a>
                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

                {{-- C. HAMBURGER MENU (Toggler Sidenav) --}}
                {{-- <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li> --}}

            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const hamburger = document.querySelector(".mobile-hamburger");
    const btn = document.getElementById("iconNavbarSidenav");
    const body = document.body;
    const rightMenu = document.querySelector(".mobile-right-menu");

    btn.addEventListener("click", function () {

        setTimeout(() => {

            if (body.classList.contains("g-sidenav-pinned") ||
                body.classList.contains("sidebar-open") ||
                body.classList.contains("sidenav-open")) {

                hamburger.classList.add("hamburger-shift");
                rightMenu.classList.add("hide-right-menu");

            } else {

                hamburger.classList.remove("hamburger-shift");
                rightMenu.classList.remove("hide-right-menu");

            }

        }, 200);
    });

});
</script>


<style>
    .mobile-hamburger {
        display: none;
    }
    /* =======================================================
   MOBILE RESPONSIVE + COMPACT (FINAL FIX)
   ======================================================= */
@media (max-width: 768px) {

    .notification-wrapper {
        position: static !important;
    }

    .notification-bubble {

        position: fixed !important;

        top: 58px !important;

        left: 8px !important;
        right: 8px !important;

        width: auto !important;
        max-width: none !important;

        border-radius: 10px;
        padding-top: 4px;

        font-size: 13px;

        z-index: 99999;
    }

    /* arrow tetap */
    .notification-bubble::before {
        right: 18px !important;
        width: 10px;
        height: 10px;
        top: -5px;
    }

    /* header */
    .notification-header {
        padding: 8px 12px;
        font-size: 13px;
    }

    /* body */
    .notification-body {
        max-height: 260px;
    }

    /* item */
    .notification-item {
        padding: 8px 10px;
        gap: 8px;
    }

    /* icon */
    .notif-icon {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }

    .notif-icon i {
        font-size: 11px;
    }

    /* title */
    .notif-title {
        font-size: 12.5px;
        line-height: 1.2;
    }

    /* text */
    .notif-text {
        font-size: 11.5px;
    }

    /* time */
    .notif-time {
        font-size: 10.5px;
    }

    /* empty */
    .notification-empty {
        padding: 14px;
        font-size: 12px;
    }

    /* badge */
    #badge-notif {
        font-size: 9px;
        padding: 3px 5px;
    }

    .mobile-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-hamburger {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-title {
        font-size: 15px;
        margin: 0;
        position: relative;
        top: 14px;
    }

    .mobile-hamburger {
        position: relative;
        left: 0px;
        top: 14px;
        z-index: 1300;
        transition: all 0.3s ease;
        gap: 10px;
    }

    .mobile-hamburger.hamburger-shift {
        position: fixed !important;
        right: 16px;
        left: auto !important;
        top: 14px;
        z-index: 1300;
    }

    .mobile-right-menu.hide-right-menu {
        opacity: 0;
        pointer-events: none;
        transition: all .2s ease;
    }

    .navbar .container-fluid {
        position: relative;
    }

    .mobile-right-menu {
        margin-left: auto !important;
        white-space: nowrap;
    }

    .mobile-header-left {
        flex: 1;
        min-width: 0;
    }

    .mobile-title {
        white-space: nowrap;
        /* overflow: hidden; */
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .mobile-right-menu {
        position: fixed;
        right: 20px;
        top: 19px;
        z-index: 1200;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* biar icon tidak melebar */
    .mobile-right-menu a,
    .mobile-right-menu button {
        padding: 6px 6px !important;
    }
}

    /* =======================================================
   WRAPPER — jadi anchor dropdown
   ======================================================= */
    .notification-wrapper {
        position: relative;
    }

    /* =======================================================
   DROPDOWN BUBBLE
   ======================================================= */
    .notification-bubble {
        position: absolute !important;
        top: 45px !important;
        right: 0 !important;
        /* 🔥 TEMPEL KE KANAN ICON */
        left: auto !important;
        /* 🔥 MATIKAN LEFT */
        width: 340px;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18);
        border: none;
        padding-top: 8px;
        z-index: 99999;
    }


    /* =======================================================
   ARROW SEGITIGA
   ======================================================= */
    .notification-bubble::before {
        content: "";
        position: absolute;
        top: -7px;
        right: 18px;
        /* 🔥 PAS DI BAWAH LONCENG */
        width: 14px;
        height: 14px;
        background: #fff;
        transform: rotate(45deg);
        box-shadow: -3px -3px 6px rgba(0, 0, 0, 0.05);
    }


    /* =======================================================
   BADGE
   ======================================================= */
    .notification-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #dc3545;
        color: #fff;
        font-size: 10px;
        font-weight: bold;
        padding: 3px 6px;
        border-radius: 50%;
    }

    /* =======================================================
   HEADER
   ======================================================= */
    .notification-header {
        padding: 12px 16px;
        font-weight: 600;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    /* =======================================================
   BODY
   ======================================================= */
    .notification-body {
        max-height: 320px;
        overflow-y: auto;
    }

    /* =======================================================
   ITEM
   ======================================================= */
    .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #f1f1f1;
    }

    .notification-item:hover {
        background: #f9f9f9;
    }

    /* =======================================================
   ICON
   ======================================================= */
    .notif-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    /* =======================================================
   EMPTY
   ======================================================= */
    .notification-empty {
        padding: 20px;
        text-align: center;
        font-size: 13px;
        color: #888;
    }

    .notification-item.unread {
        border-left: 3px solid #dc3545;
        background: #fff9f9;
    }

    /* Optional: hover lebih jelas */
    .notification-item.unread:hover {
        background: #fff1f1;
    }

    /* DEFAULT (pink) */
    .logout-btn {
        display: flex;
        align-items: center;
        gap: 8px;

        padding: 7px 16px;
        border-radius: 10px;
        border: none;

        background: linear-gradient(195deg, #ec407a, #d81b60);
        color: #fff;
        font-weight: 600;
        font-size: 13px;

        box-shadow: 0 4px 14px rgba(216, 27, 96, .35);
        transition: all .25s ease;
    }

    /* 🔥 HOVER JADI MERAH */
    .logout-btn:hover {
        background: linear-gradient(195deg, #ef5350, #c62828);
        box-shadow: 0 8px 22px rgba(198, 40, 40, .45);
        transform: translateY(-2px);
    }

    /* klik */
    .logout-btn:active {
        transform: scale(.92);
    }

    /* icon geser dikit */
    .logout-btn i {
        transition: transform .25s;
    }

    .logout-btn:hover i {
        transform: translateX(4px);
    }
</style>
