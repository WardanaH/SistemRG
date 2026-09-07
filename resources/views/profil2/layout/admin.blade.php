<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Profil' }} - Restu Guru Promosindo</title>
    <link rel="shortcut icon" href="{{ asset('image-company/icon.webp') }}">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* CSS BRUTALISM ADMIN */
        :root {
            --b-border: 4px solid #000;
            --b-shadow: 6px 6px 0px #000;
            --neon-y: #fffb00;
            --neon-c: #00ffff;
            --neon-g: #39ff14;
            --neon-m: #ff00ff;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            color: #000;
            background-image: radial-gradient(#000 1px, transparent 0);
            background-size: 25px 25px;
        }

        .admin-sidebar {
            background: #fff;
            border-right: var(--b-border);
            min-height: 100vh;
        }

        .brutal-card {
            background: #fff;
            border: var(--b-border);
            box-shadow: var(--b-shadow);
            border-radius: 0;
            margin-bottom: 20px;
        }

        .brutal-input {
            border: 3px solid #000;
            border-radius: 0;
            padding: 10px 15px;
            font-weight: bold;
            box-shadow: 3px 3px 0px #000;
            transition: all 0.2s;
        }

        .brutal-input:focus {
            outline: none;
            background-color: var(--neon-y);
            box-shadow: 5px 5px 0px #000;
            transform: translate(-2px, -2px);
        }

        .brutal-btn {
            background: #000;
            color: #fff;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px var(--neon-g);
            font-weight: 900;
            text-transform: uppercase;
            border-radius: 0;
            padding: 10px 25px;
            transition: all 0.1s;
        }

        .brutal-btn:hover {
            background: var(--neon-g);
            color: #000;
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px #000;
        }

        .admin-nav-link {
            display: block;
            padding: 12px 15px;
            color: #000;
            font-weight: 900;
            text-decoration: none;
            border-bottom: 3px solid #000;
            background: var(--neon-y);
            transition: all 0.1s;
        }

        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: var(--neon-c);
            padding-left: 20px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR ADMIN -->
            <div class="col-md-3 col-lg-2 p-0 admin-sidebar d-none d-md-block position-fixed">
                <div class="p-3 border-bottom border-dark border-4" style="background: var(--neon-m);">
                    <h4 class="fw-bold mb-0 text-white" style="text-shadow: 2px 2px 0px #000;">PANEL ADMIN</h4>
                    <span class="fw-bold text-dark">Restu Guru</span>
                </div>

                <!-- navbar -->
                <div class="p-0">
                    <a href="{{ route('profil2.dashboard') }}" class="admin-nav-link {{ request()->routeIs('profil2.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge me-2"></i> Dashboard Utama</a>

                    <a href="{{ route('profil2.hero.edit') }}" class="admin-nav-link {{ request()->routeIs('profil2.hero.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-heading me-2"></i> Atur Teks Hero
                    </a>
                    <a href="{{ route('profil2.perusahaan.edit') }}" class="admin-nav-link {{ request()->routeIs('profil2.perusahaan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-building me-2"></i> Info Perusahaan & Kontak
                    </a>
                    <a href="{{ route('profil2.produk.index') }}" class="admin-nav-link {{ request()->routeIs('profil2.produk.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box-open me-2"></i> Kelola Produk
                    </a>
                    <a href="{{ route('profil2.legal.edit') }}" class="admin-nav-link {{ request()->routeIs('profil2.legal.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-scale-balanced me-2"></i> Legal & Privasi
                    </a>
                    <a href="{{ route('profil2.event.index') }}" class="admin-nav-link {{ request()->routeIs('profil2.event.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-images me-2"></i> Kelola Event
                    </a>
                    <a href="{{ route('profil2.mesin.index') }}" class="admin-nav-link {{ request()->routeIs('profil2.mesin.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-wrench me-2"></i> Kelola Mesin
                    </a>
                    <a href="{{ route('profil2.klien.index') }}" class="admin-nav-link {{ request()->routeIs('profil2.klien.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users me-2"></i> Kelola Klien
                    </a>

                    <form action="{{ route('auth.logout') }}" method="POST" class="mt-5 border-top border-dark border-3">
                        @csrf
                        <button type="submit" class="admin-nav-link text-start w-100 border-0" style="background: #ff4444; color: white;"><i class="fa-solid fa-right-from-bracket me-2"></i> LOGOUT</button>
                    </form>
                </div>
            </div>

            <!-- KONTEN UTAMA -->
            <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2 p-4 p-md-5">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
