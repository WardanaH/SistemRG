<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="#">
            <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100">
            <span class="ms-1 font-weight-bold text-white">Material Dashboard</span>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto">
        <ul class="navbar-nav">

            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs(['manajemen.dashboard', 'operator.dashboard', 'admin.dashboard', 'designer.dashboard']) ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('home') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <hr>

            @hasrole('manajemen')
            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('manajemen.user') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('manajemen.user') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemen User</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('manajemen.cabang') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('manajemen.cabang') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">location_on</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemne Cabang</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('manajemen.bahanbaku') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('manajemen.bahanbaku') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemne Bahan Baku</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('manajemen.finishing') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('manajemen.finishing') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">texture</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemen Finishing</span>
                </a>
            </li>
            @endhasrole

            @hasrole('manajemen|designer|admin')
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk.index') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">book</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemen SPK</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk-bantuan.index') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk-bantuan.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">book</i>
                    </div>
                    <span class="nav-link-text ms-1">Manajemen SPK Bantuan</span>
                </a>
            </li>
            @endhasrole

            @hasrole('manajemen|designer')
            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('spk') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('spk') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">work</i>
                    </div>
                    <span class="nav-link-text ms-1">Buat SPK</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{
                    request()->routeIs('spk-bantuan') ? 'active bg-gradient-primary' : ''
                    }}" href="{{ route('spk-bantuan') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">work</i>
                    </div>
                    <span class="nav-link-text ms-1">Buat SPK Bantuan</span>
                </a>
            </li>
            @endhasrole

            @hasrole('manajemen|operator indoor|operator outdoor|operator multi')
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk.produksi') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk.produksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Produksi SPK</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk-bantuan.produksi') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk-bantuan.produksi') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">precision_manufacturing</i>
                    </div>
                    <span class="nav-link-text ms-1">Produksi SPK Bantuan</span>
                </a>
            </li>
            @endhasrole

            @hasrole('manajemen|admin|operator indoor|operator outdoor|operator multi')
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk.riwayat') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk.riwayat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">manage_history</i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat Produksi SPK</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{
                                request()->routeIs('spk-bantuan.riwayat') ? 'active bg-gradient-primary' : ''
                                }}" href="{{ route('spk-bantuan.riwayat') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons-round opacity-10">manage_history</i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat Produksi SPKB</span>
                </a>
            </li>
            @endhasrole
        </ul>
    </div>
    <hr>
</aside>
