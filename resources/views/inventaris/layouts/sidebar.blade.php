@php
    /*
    |--------------------------------------------------------------------------
    | PAGE TITLE (UNTUK NAVBAR)
    |--------------------------------------------------------------------------
    */
    if (request()->routeIs('gudangpusat.dashboard')) {
        $pageTitle = 'Dashboard';
    } elseif (request()->routeIs('barang.pusat') || request()->routeIs('barang.pusat.index')) {
        $pageTitle = 'Barang Gudang Pusat';
    } elseif (request()->routeIs('pengiriman.pusat*')) {
        $pageTitle = 'Pengiriman Barang';
    } elseif (request()->routeIs('laporan.pengiriman*')) {
        $pageTitle = 'Laporan Pengiriman';
    } elseif (request()->routeIs('gudangcabang.dashboard')) {
        $pageTitle = 'Dashboard Cabang';
    } elseif (request()->routeIs('gudangcabang.barang*')) {
        $pageTitle = 'Data Barang Cabang';
    } elseif (request()->routeIs('barang.pusat.updatestok*')) {
        $pageTitle = 'Update Stok';
    } elseif (request()->routeIs('gudangcabang.penerimaan*')) {
        $pageTitle = 'Penerimaan Barang';
    } elseif (request()->routeIs('gudangcabang.laporan*')) {
        $pageTitle = 'Laporan Penerimaan';
    } elseif (request()->routeIs('gudangcabang.inventaris*')) {
        $pageTitle = 'Inventaris Kantor';
    } elseif (request()->routeIs('gudangcabang.permintaan*')) {
        $pageTitle = 'Permintaan Pengiriman';
    } elseif (request()->routeIs('gudangcabang.pengambilan*')) {
        $pageTitle = 'Pengambilan Antar';
    // } elseif (request()->routeIs('gudangcabang.ambil*')) {
    //     $pageTitle = 'Ambil Barang';
    // } elseif (request()->routeIs('gudangcabang.ambil*')) {
    //     $pageTitle = 'Antar Barang';
    } else {
        $pageTitle = 'Gudang Pusat';
    }
@endphp

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            id="iconSidenav"></i>

            @php
                $user = Auth::user();
                $cabangNama = $user->cabang ? $user->cabang->nama : 'Gudang Pusat';
            @endphp

            <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('gudangpusat.dashboard') }}">
                <img src="{{ asset('image-company/icon.webp') }}" class="navbar-brand-img h-100 me-2" style="height:40px; width:40px; object-fit:contain;">
                <span class="ms-1 font-weight-bold text-white">
                    {{ $cabangNama }}
                </span>
            </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto">
        <ul class="navbar-nav">
<style>
    .sidebar-section {
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: rgba(255,255,255,.6);
        margin: 12px 0 6px 16px;
        font-weight: 600;
    }
</style>
            {{-- =====================
            DASHBOARD
            ===================== --}}
            @hasrole('inventory cabang')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.dashboard') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('gudangcabang.dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard Cabang</span>
                </a>
            </li>
            @else
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangpusat.dashboard') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('gudangpusat.dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            @endhasrole

            {{-- =====================
            DATA BARANG
            ===================== --}}
            @hasrole('inventory cabang')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.barang*') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('gudangcabang.barang') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Barang Cabang</span>
                </a>
            </li>
            @else
            <li class="nav-item">
                <a class="nav-link text-white {{  request()->routeIs('barang.pusat') || request()->routeIs('barang.pusat.index')? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('barang.pusat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Barang</span>
                </a>
            </li>
            @endhasrole

            @hasrole('inventory utama')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('barang.pusat.updatestok*') ? 'active bg-gradient-success' : '' }}"
                href="{{ route('barang.pusat.updatestok') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">sync_alt</i>
                    </div>
                    <span class="nav-link-text ms-1">Update Stok</span>
                </a>
            </li>
            @endhasrole

            @hasrole('inventory cabang')
            {{-- =====================
            INVENTARIS KANTOR (CABANG)
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.inventaris*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.inventaris.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">qr_code</i>
                    </div>
                    <span class="nav-link-text ms-1">Inventaris Kantor</span>
                </a>
            </li>

            {{-- =====================
            PERMINTAAN PENGIRIMAN (HANYA UNTUK CABANG)
            ===================== --}}
            <li class="nav-item mt-2">
                <span class="sidebar-section">Pengiriman</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.permintaan*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.permintaan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">send</i>
                    </div>
                    <span class="nav-link-text ms-1">Permintaan Pengiriman</span>
                </a>
            </li>

            {{-- =====================
            PENERIMAAN BARANG (HANYA UNTUK CABANG)
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.penerimaan*') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('gudangcabang.penerimaan') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">assignment_turned_in</i>
                    </div>
                    <span class="nav-link-text ms-1">Penerimaan Barang</span>
                </a>
            </li>

            {{-- =====================
            AMBIL & ANTAR CABANG
            ===================== --}}
            {{-- <li class="nav-item mt-2">
                <span class="sidebar-section">Ambil & Antar</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.ambil*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.ambil.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">download</i>
                    </div>
                    <span class="nav-link-text ms-1">Ambil</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.antar*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.antar.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">local_shipping</i>
                    </div>
                    <span class="nav-link-text ms-1">Antar</span>
                </a>
            </li> --}}

            <li class="nav-item mt-2">
                <span class="sidebar-section">Ambil & Antar</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.pengambilan*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.pengambilan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">swap_horiz</i>
                    </div>
                    <span class="nav-link-text ms-1">Pengambilan Antar</span>
                </a>
            </li>
            {{-- =====================
            LAPORAN PENERIMAAN (HANYA UNTUK CABANG)
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangcabang.laporan*') ? 'active bg-gradient-primary' : '' }}"
                href="{{ route('gudangcabang.laporan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Laporan Penerimaan</span>
                </a>
            </li>
            @endhasrole

            {{-- =====================
            PENGIRIMAN BARANG (HANYA UNTUK GUDANG PUSAT)
            ===================== --}}
            @unlessrole('inventory cabang')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('pengiriman.pusat*') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('pengiriman.pusat.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">local_shipping</i>
                    </div>
                    <span class="nav-link-text ms-1">Pengiriman</span>
                </a>
            </li>


            {{-- =====================
            LAPORAN PENGIRIMAN (HANYA UNTUK GUDANG PUSAT)
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('laporan.pengiriman*') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('laporan.pengiriman.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Laporan Pengiriman</span>
                </a>
            </li>
            @endunlessrole

        </ul>
    </div>
</aside>
