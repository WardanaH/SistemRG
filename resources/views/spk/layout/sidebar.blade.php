<div class="d-flex flex-column p-3 text-white h-100">
    <ul class="nav nav-pills flex-column mb-auto">

        <li class="nav-item mb-2">
            <a href="{{ route('home') }}"
                class="nav-link {{ request()->routeIs([
                    'manajemen.dashboard',
                    'operator.dashboard',
                    'admin.dashboard',
                    'designer.dashboard'
                ]) ? 'active' : 'text-white' }}"
                aria-current="page">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>

        <hr class="text-white my-2">
        <div class="text small fw-bold text-uppercase mb-1 ms-1">Manajemen {{ Auth::user()->roles->pluck('name')->first() }}</div>

        @hasrole('manajemen')
        <li class="nav-item">
            <a href="{{ route('manajemen.user') }}"
                class="nav-link {{ request()->routeIs('manajemen.user*') ? 'active' : 'text-white' }}">
                <i class="bi bi-people me-2"></i>
                Manajemen User
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('manajemen.cabang') }}"
                class="nav-link {{ request()->routeIs('manajemen.cabang*') ? 'active' : 'text-white' }}">
                <i class="bi bi-building me-2"></i>
                Manajemen Cabang
            </a>
        </li>
        @endhasrole

        @hasrole('manajemen|operator indoor|operator outdoor|operator multi')
        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->routeIs('operator.barang*') ? 'active' : 'text-white' }}">
                <i class="bi bi-box me-2"></i>
                Manajemen Barang
            </a>
        </li>
        @endhasrole

        @hasrole('manajemen|designer')
        <li class="nav-item">
            <a href="{{ route('designer.spk') }}"
                class="nav-link {{ request()->routeIs('designer.spk*') ? 'active' : 'text-white' }}">
                <i class="bi bi-box me-2"></i>
                Manajemen SPK
            </a>
        </li>
        @endhasrole
    </ul>
</div>
