@php
    /*
    |--------------------------------------------------------------------------
    | PAGE TITLE (UNTUK NAVBAR)
    |--------------------------------------------------------------------------
    */
    if (request()->routeIs('gudangpusat.dashboard')) {
        $pageTitle = 'Dashboard';
    } elseif (request()->routeIs('barang.pusat*')) {
        $pageTitle = 'Barang Gudang Pusat';
    } elseif (request()->routeIs('pengiriman.pusat*')) {
        $pageTitle = 'Pengiriman Barang';
    } elseif (request()->routeIs('laporan.pengiriman*')) {
        $pageTitle = 'Laporan Pengiriman';
    } else {
        $pageTitle = 'Gudang Pusat';
    }
@endphp

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            id="iconSidenav"></i>

        <a class="navbar-brand m-0" href="{{ route('gudangpusat.dashboard') }}">
            <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100">
            <span class="ms-1 font-weight-bold text-white">Gudang Pusat</span>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto">
        <ul class="navbar-nav">

            {{-- =====================
            DASHBOARD
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('gudangpusat.dashboard') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('gudangpusat.dashboard') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- =====================
            DATA BARANG
            ===================== --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('barang.pusat*') ? 'active bg-gradient-primary' : '' }}"
                   href="{{ route('barang.pusat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Barang</span>
                </a>
            </li>

            {{-- =====================
            PENGIRIMAN BARANG
            ===================== --}}
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
            LAPORAN PENGIRIMAN
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

        </ul>
    </div>
</aside>
