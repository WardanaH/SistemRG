<style>
    .sidebar-section {
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .6);
        margin: 12px 0 6px 16px;
        font-weight: 600;
    }
</style>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>

        @php
            $user = Auth::user();
            $cabangNama = $user->cabang ? $user->cabang->nama : 'Gudang Pusat';
        @endphp

        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('image-company/icon.webp') }}" class="navbar-brand-img h-100 me-2" style="height:40px; width:40px; object-fit:contain;" alt="Logo Restu Guru">
            <span class="ms-1 font-weight-bold text-white">
                {{ $cabangNama }}
            </span>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- =====================
                 DASHBOARD
            ===================== --}}
            @hasrole('inventory cabang')
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('gudang-cabang.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-cabang.dashboard') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard Cabang</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('gudang-pusat.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-pusat.dashboard') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">dashboard</i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
            @endhasrole

            {{-- =====================
                 DISTRIBUSI BARANG
            ===================== --}}
            <li class="nav-item mt-3">
                <h6 class="sidebar-section">Distribusi Barang</h6>
            </li>

            @hasrole('inventory cabang')
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-cabang.permintaan') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-cabang.permintaan') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">category</i>
                        </div>
                        <span class="nav-link-text ms-1">Buat Permintaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-cabang.permintaan.riwayat') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-cabang.permintaan.riwayat') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">history</i>
                        </div>
                        <span class="nav-link-text ms-1">Riwayat Permintaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-cabang.permintaan.penerimaan') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-cabang.permintaan.penerimaan') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">inventory_2</i>
                        </div>
                        <span class="nav-link-text ms-1">Penerimaan Barang</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-pusat.permintaan.masuk') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-pusat.permintaan.masuk') }} ">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Permintaan Masuk</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-pusat.permintaan.riwayat') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-pusat.permintaan.riwayat') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">history</i>
                        </div>
                        <span class="nav-link-text ms-1">Riwayat Permintaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-pusat.laporan') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-pusat.laporan') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">analytics</i>
                        </div>
                        <span class="nav-link-text ms-1">Laporan Permintaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- Ganti route-nya kena mun ikam sudah meulah controller-nya --}}
                    <a class="nav-link text-white {{ request()->routeIs('gudang-pusat.laporan.barang') ? 'active bg-gradient-primary' : '' }}" href="{{ route('gudang-pusat.laporan.barang') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons-round opacity-10">inventory</i>
                        </div>
                        <span class="nav-link-text ms-1">Laporan Per Barang</span>
                    </a>
                </li>
            @endhasrole

        </ul>
    </div>
</aside>
