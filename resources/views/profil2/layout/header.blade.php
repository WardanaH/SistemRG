<nav class="navbar navbar-expand-lg brutal-nav py-3 border-bottom border-dark border-4 sticky-top" style="box-shadow: 0px 8px 0px #000;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-2" href="/beranda" style="background: var(--neon-y); color: #000; padding: 5px 15px; border: var(--b-border); box-shadow: 4px 4px 0px #000;">
            RESTU GURU PROMOSINDO
        </a>

        <div class="d-flex gap-2">
            <!-- Tombol Sidebar gasan HP -->
            <button class="brutal-btn py-2 px-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" style="background: var(--neon-m); color: #fff;">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>
            <!-- Tombol Navigasi Bootstrap -->
            <button class="navbar-toggler brutal-card" style="background: var(--neon-y);" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto fw-bold fs-5 align-items-lg-center mt-3 mt-lg-0">
                <li class="nav-item mx-2 mb-2 mb-lg-0"><a class="nav-link text-dark brutal-card px-3 text-center" style="background: var(--neon-g);" href="/beranda">BERANDA</a></li>
                <li class="nav-item mx-2 mb-2 mb-lg-0">
                    <a class="nav-link text-dark brutal-card px-3 text-center" style="background: #fff;" href="{{ route('profil2.layanan') }}">LAYANAN</a>
                </li>
                <li class="nav-item mx-2 mb-2 mb-lg-0">
                    <a class="nav-link text-dark brutal-card px-3 text-center" style="background: var(--neon-y);" href="{{ route('profil2.produk') }}">PRODUK</a>
                </li>

                <!-- Dropdown Event/Promo -->
                <li class="nav-item dropdown mx-2 mb-2 mb-lg-0">
                    <a class="nav-link dropdown-toggle text-dark brutal-card px-3 text-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: var(--neon-c);">
                        EVENT SPESIAL
                    </a>
                    <ul class="dropdown-menu brutal-card p-2" style="background: var(--neon-c); border-radius: 0;">
                        <li><a class="dropdown-item fw-bold" href="{{ route('profil2.event', 'ramadhan') }}">🕌 Tema Ramadhan</a></li>
                        <li><a class="dropdown-item fw-bold" href="{{ route('profil2.event', 'imlek') }}">🏮 Tema Imlek</a></li>
                        <li><a class="dropdown-item fw-bold" href="{{ route('profil2.event', 'natal') }}">🎄 Tema Natal</a></li>
                        <li><a class="dropdown-item fw-bold" href="{{ route('profil2.event', 'kemerdekaan') }}">🇮🇩 Tema 17 Agustus</a></li>
                    </ul>
                </li>

                <!-- Tombol Sidebar gasan Desktop -->
                <li class="nav-item ms-lg-3 d-none d-lg-block">
                    <button class="brutal-btn py-2 px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" style="background: var(--neon-m); color: #fff;" title="Buka Profil & Menu Lainnya">
                        <i class="fa-solid fa-bars fs-4"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
