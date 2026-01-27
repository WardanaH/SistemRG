@php
    /*
    |--------------------------------------------------------------------------
    | PAGE TITLE DINAMIS (BERDASARKAN ROUTE)
    |--------------------------------------------------------------------------
    */
    if (request()->routeIs('gudangpusat.dashboard')) {
        $pageTitle = 'Dashboard';
    } elseif (request()->routeIs('barang.pusat*')) {
        $pageTitle = 'Data Barang';
    } else {
        $pageTitle = 'Gudang Pusat';
    }
@endphp

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">

        {{-- =====================
        BREADCRUMB & TITLE
        ===================== --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm text-dark">
                    Pages
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                    {{ $pageTitle }}
                </li>
            </ol>

            <h6 class="font-weight-bolder mb-0">
                {{ $pageTitle }}
            </h6>
        </nav>

        {{-- =====================
        RIGHT NAVBAR
        ===================== --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4">
            <ul class="navbar-nav ms-auto align-items-center">

                {{-- USER --}}
                <li class="nav-item me-3">
                    <span class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-1"></i>
                        {{ Auth::user()->name ?? 'User' }}
                    </span>
                </li>

                {{-- LOGOUT --}}
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm btn-outline-danger mb-0">
                            <i class="fa fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</nav>
