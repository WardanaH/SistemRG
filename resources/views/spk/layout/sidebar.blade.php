<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">

    {{-- HEADER LOGO --}}
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}">
            <img src="{{ asset('image-company/icon.webp') }}" class="navbar-brand-img" alt="main_logo" style="max-height: 50px; width: auto;">
            <span class="ms-1 font-weight-bold text-white">SISTEM SPK RG</span>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- 1. DASHBOARD (Semua User) --}}
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs(['manajemen.dashboard', 'operator.dashboard', 'admin.dashboard', 'designer.dashboard']) ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('home') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- 2. MASTER DATA (Khusus Manajemen) --}}
            @hasrole('manajemen')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Master Data</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('manajemen.user') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('manajemen.user') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">group</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemen User</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('manajemen.cabang') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('manajemen.cabang') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">store</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Cabang</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('manajemen.bahanbaku') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('manajemen.bahanbaku') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Bahan Baku</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('manajemen.finishing') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('manajemen.finishing') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">brush</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Finishing</span>
                </a>
            </li>
            @endhasrole

            {{-- 3. INPUT ORDER (Designer & Manajemen) --}}
            @hasrole('manajemen|designer')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Input Order</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">post_add</i>
                    </div>
                    <span class="nav-link-text ms-1">Buat SPK Baru</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-bantuan') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-bantuan') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">post_add</i>
                    </div>
                    <span class="nav-link-text ms-1">Buat SPK Bantuan</span>
                </a>
            </li>
            @endhasrole

            {{-- 4. MONITORING & DATA (Admin, Designer, Manajemen) --}}
            @hasrole('manajemen|designer|admin')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Data SPK</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk.index') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Data SPK Reguler</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-bantuan.index') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-bantuan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Data SPK Bantuan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-charge.index') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-charge.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Data Charge Desain</span>
                </a>
            </li>
            @if (now())
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-lembur.index') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-lembur.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <span class="nav-link-text ms-1">Data SPK Lembur</span>
                </a>
            </li>
            @endif
            @endhasrole

            {{-- 5. OPERASIONAL / PRODUKSI (Operator & Manajemen) --}}
            @hasrole('manajemen|operator indoor|operator outdoor|operator multi|operator dtf')
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Produksi</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk.produksi') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk.produksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Produksi Reguler</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-bantuan.produksi') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-bantuan.produksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Produksi Bantuan</span>
                </a>
            </li>
            @if (now())
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-lembur.produksi') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-lembur.produksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Produksi Lembur</span>
                </a>
            </li>
            @endif
            @endhasrole

            {{-- 6. HISTORY & REPORT (Semua User) --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Laporan & Riwayat</h6>
            </li>

            @hasrole('manajemen|admin|operator indoor|operator outdoor|operator multi|operator dtf')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk.riwayat') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk.riwayat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">history</i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat Reguler</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-bantuan.riwayat') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-bantuan.riwayat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">history</i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat Bantuan</span>
                </a>
            </li>
            @if (now())
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('spk-lembur.riwayat') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('spk-lembur.riwayat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">history</i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat Lembur</span>
                </a>
            </li>
            @endif
            @endhasrole

            @hasrole('manajemen|admin|operator indoor|operator outdoor|operator multi|operator dtf|designer')
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->routeIs('laporan.index') ? 'active bg-gradient-primary' : '' }}"
                    href="{{ route('laporan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">analytics</i>
                    </div>
                    <span class="nav-link-text ms-1">Laporan Kinerja</span>
                </a>
            </li>
            @endhasrole

        </ul>
    </div>
</aside>
