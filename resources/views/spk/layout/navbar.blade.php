<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">

        {{-- 1. BAGIAN KIRI: BREADCRUMB & JUDUL (Tetap) --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="javascript:;">{{ Auth::user()->cabang->nama }}</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                    {{ $title ?? 'Dashboard' }}
                </li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $title ?? 'Dashboard' }}</h6>
        </nav>

        {{-- 2. BAGIAN TENGAH: COLLAPSE (Untuk menu lain jika ada) --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                {{-- Kosong atau Search Bar --}}
            </div>
        </div>

        {{-- 3. BAGIAN KANAN: ICON USER & TOGGLER (Pindahkan kesini agar selalu muncul) --}}
        <ul class="navbar-nav justify-content-end d-flex flex-row align-items-center gap-3">

            {{-- A. NOTIFIKASI --}}
            @hasrole('admin|operator indoor|operator outdoor|operator mutli|operator dtf')
            <li class="nav-item dropdown pe-2 d-flex align-items-center position-relative">
                <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="material-icons-round opacity-10" style="font-size: 1.2rem;">notifications</i>

                    {{-- Badge Notifikasi --}}
                    <span id="badge-notif"
                        class="position-absolute badge rounded-circle bg-danger border border-white"
                        style="display: none; top: -5px; right: -10px; padding: 4px 6px; font-size: 0.65rem; min-width: 18px; height: 18px; line-height: 10px;">
                        0
                    </span>
                </a>
                @hasrole('admin')
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md" href="{{ route('spk.index') }}">
                            <div class="d-flex py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">
                                        <span class="font-weight-bold">Cek Halaman SPK</span>
                                    </h6>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                @endhasrole
                @hasrole('operator indoor|operator outdoor|operator mutli|operator dtf')
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md" href="{{ route('spk.produksi') }}">
                            <div class="d-flex py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="text-sm font-weight-normal mb-1">
                                        <span class="font-weight-bold">Lihat Tugas Baru</span>
                                    </h6>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                @endhasrole
            </li>
            @endhasrole

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
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </a>
            </li>

        </ul>

    </div>
</nav>
