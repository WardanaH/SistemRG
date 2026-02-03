@php
use Illuminate\Support\Facades\Auth;

if (request()->routeIs('gudangpusat.dashboard')) {
    $pageTitle = 'Dashboard';
} elseif (request()->routeIs('barang.pusat*')) {
    $pageTitle = 'Data Barang';
} else {
    $pageTitle = 'Gudang Pusat';
}

$user = Auth::user();

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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm text-dark">Pages</li>
                <li class="breadcrumb-item text-sm text-dark active">
                    {{ $pageTitle }}
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $pageTitle }}</h6>
        </nav>

        {{-- =====================
        RIGHT NAVBAR
        ===================== --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4">
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
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                        @endif
                    </a>

                    <div class="dropdown-menu notification-bubble p-0">

                        <div class="notification-header">
                            Notifikasi
                        </div>

                        <div class="notification-body">

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
                                <div class="notification-empty">
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

                {{-- LOGOUT --}}
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>

{{-- =====================
SCRIPT NOTIFIKASI
===================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.notification-item.unread').forEach(item => {
        item.addEventListener('click', function () {

            const notifId = this.dataset.id;
            let url = '';

            @if(auth()->user()->hasRole('inventory utama'))
                url = "{{ route('permintaan.pusat.read', ':id') }}".replace(':id', notifId);
            @elseif(auth()->user()->hasRole('inventory cabang'))
                url = "{{ route('gudangcabang.notif.read', ':id') }}".replace(':id', notifId);
            @endif

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(() => {
                this.classList.remove('unread');

                const badge = document.querySelector('.badge.bg-danger');
                if (badge) {
                    let count = parseInt(badge.innerText);
                    count--;

                    if (count <= 0) {
                        badge.remove();
                    } else {
                        badge.innerText = count;
                    }
                }

            });
        });
    });

});
</script>


<style>
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
    right: 0 !important;      /* 🔥 TEMPEL KE KANAN ICON */
    left: auto !important;   /* 🔥 MATIKAN LEFT */
    width: 340px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.18);
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
    right: 18px;             /* 🔥 PAS DI BAWAH LONCENG */
    width: 14px;
    height: 14px;
    background: #fff;
    transform: rotate(45deg);
    box-shadow: -3px -3px 6px rgba(0,0,0,0.05);
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
</style>
