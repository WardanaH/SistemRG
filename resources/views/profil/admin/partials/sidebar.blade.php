@php
    use Illuminate\Support\Facades\Route;

    $isDashboard = request()->routeIs('profil.admin.dashboard');

    $isBeranda = request()->routeIs('profil.admin.beranda.edit');
    $isTentang = request()->routeIs('profil.admin.tentang.edit');
    $isLayanan = request()->routeIs('profil.admin.layanan.edit');

    // =========================
    // BERITA DROPDOWN ACTIVE
    // =========================
    $isBeritaList = request()->routeIs('profil.admin.berita.index')
        || request()->routeIs('profil.admin.berita.create')
        || request()->routeIs('profil.admin.berita.edit');

    $isBeritaHal  = request()->routeIs('profil.admin.berita.halaman.*');
    $isBeritaOpen = request()->routeIs('profil.admin.berita.*') || $isBeritaHal;

    // =========================
    // KONTAK
    // =========================
    $kontakRouteExists = Route::has('profil.admin.kontak.edit');
    $isKontak = request()->routeIs('profil.admin.kontak.*');

    // =========================
    // TAMPILAN (Navbar/Footer)
    // =========================
    $tampilanNavbarExists = Route::has('profil.admin.tampilan.navbar.edit');
    $tampilanFooterExists = Route::has('profil.admin.tampilan.footer.edit');

    $isTampilanNavbar = request()->routeIs('profil.admin.tampilan.navbar.*');
    $isTampilanFooter = request()->routeIs('profil.admin.tampilan.footer.*');
    $isTampilanOpen   = request()->routeIs('profil.admin.tampilan.*') || $isTampilanNavbar || $isTampilanFooter;
@endphp

<aside class="admin-sidebar">
    <div class="admin-brand">
        <div class="brand-mark">RGP<span class="dot">.</span></div>
        <div class="brand-sub">Restu Guru Promosindo</div>
    </div>

    <nav class="admin-nav">

        <a class="nav-item {{ $isDashboard ? 'active' : '' }}"
           href="{{ route('profil.admin.dashboard') }}">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-item {{ $isBeranda ? 'active' : '' }}"
           href="{{ route('profil.admin.beranda.edit') }}">
            <i class="bi bi-pencil-square"></i>
            <span>Beranda</span>
        </a>

        <a class="nav-item {{ $isTentang ? 'active' : '' }}"
           href="{{ route('profil.admin.tentang.edit') }}">
            <i class="bi bi-info-circle"></i>
            <span>Tentang</span>
        </a>

        <a class="nav-item {{ $isLayanan ? 'active' : '' }}"
           href="{{ route('profil.admin.layanan.edit') }}">
            <i class="bi bi-box-seam"></i>
            <span>Layanan</span>
        </a>

        {{-- =========================
             BERITA DROPDOWN
           ========================= --}}
        <a class="nav-item nav-parent {{ $isBeritaOpen ? 'active' : '' }}"
           data-bs-toggle="collapse"
           href="#navBerita"
           role="button"
           aria-expanded="{{ $isBeritaOpen ? 'true' : 'false' }}"
           aria-controls="navBerita">
            <i class="bi bi-newspaper"></i>
            <span>Berita</span>
            <span class="nav-caret">
                <i class="bi bi-chevron-down"></i>
            </span>
        </a>

        <div class="collapse nav-sub {{ $isBeritaOpen ? 'show' : '' }}" id="navBerita">
            <a class="nav-sub-item {{ $isBeritaList ? 'active' : '' }}"
               href="{{ route('profil.admin.berita.index') }}">
                <span class="bullet"></span>
                <span>Daftar Berita</span>
            </a>

            <a class="nav-sub-item {{ $isBeritaHal ? 'active' : '' }}"
               href="{{ route('profil.admin.berita.halaman.edit') }}">
                <span class="bullet"></span>
                <span>Edit Halaman Berita</span>
            </a>
        </div>

        {{-- =========================
             KONTAK
           ========================= --}}
        @if($kontakRouteExists)
            <a class="nav-item {{ $isKontak ? 'active' : '' }}"
               href="{{ route('profil.admin.kontak.edit') }}">
                <i class="bi bi-telephone-forward"></i>
                <span>Kontak</span>
            </a>
        @endif

        {{-- =========================
             TAMPILAN DROPDOWN (BENERIN)
             - dibuat sama persis seperti BERITA
           ========================= --}}
        <a class="nav-item nav-parent {{ $isTampilanOpen ? 'active' : '' }}"
           data-bs-toggle="collapse"
           href="#navTampilan"
           role="button"
           aria-expanded="{{ $isTampilanOpen ? 'true' : 'false' }}"
           aria-controls="navTampilan">
            <i class="bi bi-layout-text-window-reverse"></i>
            <span>Tampilan</span>
            <span class="nav-caret">
                <i class="bi bi-chevron-down"></i>
            </span>
        </a>

        <div class="collapse nav-sub {{ $isTampilanOpen ? 'show' : '' }}" id="navTampilan">
            @if($tampilanNavbarExists)
                <a class="nav-sub-item {{ $isTampilanNavbar ? 'active' : '' }}"
                   href="{{ route('profil.admin.tampilan.navbar.edit') }}">
                    <span class="bullet"></span>
                    <span>Navbar</span>
                </a>
            @endif

            @if($tampilanFooterExists)
                <a class="nav-sub-item {{ $isTampilanFooter ? 'active' : '' }}"
                   href="{{ route('profil.admin.tampilan.footer.edit') }}">
                    <span class="bullet"></span>
                    <span>Footer</span>
                </a>
            @endif
        </div>

        <div class="sidebar-divider"></div>
    </nav>

    <div class="sidebar-bottom">
        <a class="btn-open-site" href="{{ route('profil.beranda') }}" target="_blank" rel="noopener" title="Buka Website">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Buka Website</span>
        </a>
    </div>
</aside>
