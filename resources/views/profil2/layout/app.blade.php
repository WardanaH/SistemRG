<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Beranda' }} - Restu Guru Promosindo</title>
    <link rel="shortcut icon" href="{{ asset('image-company/icon.webp') }}">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />



    <style>
        /* CSS BRUTALISM FULL COLOR */
        :root {
            --b-border: 4px solid #000;
            --b-shadow: 8px 8px 0px #000;
            --neon-y: #fffb00; /* Kuning Nyarak */
            --neon-c: #00ffff; /* Cyan Nyarak */
            --neon-g: #39ff14; /* Hijau Neon */
            --neon-m: #ff00ff; /* Magenta */
            --neon-r: #ff0000; /* Merah Neon */
        }
        body {
            background-color: #e0e0e0;
            font-family: 'Arial', sans-serif;
            color: #000;
            background-image: radial-gradient(#000 1px, transparent 0);
            background-size: 20px 20px;
            overflow-x: hidden;
        }
        h1, h2, h3, h4, h5, h6 {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1px;
            color: #000;
        }
        .brutal-card {
            border: var(--b-border);
            box-shadow: var(--b-shadow);
            border-radius: 0;
            transition: all 0.2s ease;
            color: #000;
        }
        .brutal-card:hover {
            transform: translate(-3px, -3px);
            box-shadow: 11px 11px 0px #000;
        }
        .brutal-btn {
            background: var(--neon-g);
            color: #000;
            border: var(--b-border);
            box-shadow: 5px 5px 0px #000;
            font-weight: 900;
            text-transform: uppercase;
            border-radius: 0;
            padding: 12px 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.1s;
            font-size: 1.1rem;
        }
        .brutal-btn:hover {
            background: var(--neon-m);
            color: #fff;
            transform: translate(2px, 2px);
            box-shadow: 3px 3px 0px #000;
        }
        .brutal-nav {
            background-color: var(--neon-c);
            border-bottom: var(--b-border);
        }
        /* Style text highlight */
        .hl-yellow { background-color: var(--neon-y); padding: 2px 8px; border: 2px solid #000; }
        .hl-cyan { background-color: var(--neon-c); padding: 2px 8px; border: 2px solid #000; }
        .brutal-client-box {
        border: 4px solid #000;
        background-color: #fff;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 4px 4px 0px #000;
        transition: all 0.2s;
    }
    .brutal-client-box:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0px #000;
        background-color: var(--neon-y); /* Bakuning pas disenggol */
    }
    </style>
</head>
<body>

    @include('profil2.layout.header')
    @include('profil2.layout.sidebar') {{-- Sidebar kaina jadi menu lipat (Offcanvas) --}}

    <!-- Konten lumbah (Full Width) -->
    <div class="container my-5 px-4">
        @yield('content')
    </div>

    {{-- Tampilkan komponen mesin cetak di semua halaman KECUALI beranda --}}
    @if(!request()->routeIs(['profil2.beranda', 'profil2.legal.public']))
        @include('profil2.components.mesin')
    @endif

    @include('profil2.layout.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>
</html>
