{{-- resources/views/profil/public/layouts/app.blade.php --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Restu Guru Promosindo')</title>

    {{-- Font (Salsa + Comfortaa) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Salsa&family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    {{-- Public Profil CSS --}}
    <link rel="stylesheet" href="{{ asset('profil/css/style.css') }}">

    @stack('head')
</head>
<body>
@php
    /**
     * Site Layout (Navbar + Footer) dari p_site_layouts (1 row).
     * - Aman kalau model / row belum ada -> fallback array kosong
     *
     * NOTE:
     * Pastikan model & table sudah ada:
     * App\Models\PSiteLayout dengan kolom json: navbar, footer (misal).
     */
    $siteLayout = null;
    $navLayout = [];
    $footLayout = [];

    try {
        // kalau model belum dibuat, ini tetap aman karena try/catch
        $siteLayout = \App\Models\PSiteLayout::query()->find(1);

        $navLayout = $siteLayout->navbar ?? [];
        $footLayout = $siteLayout->footer ?? [];

        if (!is_array($navLayout)) $navLayout = [];
        if (!is_array($footLayout)) $footLayout = [];
    } catch (\Throwable $e) {
        $siteLayout = null;
        $navLayout = [];
        $footLayout = [];
    }

    /**
     * Optional: set CSS variables dari layout (kalau kamu simpan warna brand di layout)
     * Contoh struktur:
     * $navLayout['brand_colors'] = ['restu'=>'var(--rg-blue)', 'guru'=>'var(--rg-yellow)', 'promosindo'=>'var(--rg-red)'];
     *
     * Ini tidak wajib, tapi siap kalau kamu mau admin bisa ubah warna teks.
     */
    $brandColors = $navLayout['brand_colors'] ?? [];
    if (!is_array($brandColors)) $brandColors = [];

    $cRestu = $brandColors['restu'] ?? 'var(--rg-blue)';
    $cGuru  = $brandColors['guru'] ?? 'var(--rg-yellow)';
    $cPro   = $brandColors['promosindo'] ?? 'var(--rg-red)';

    /**
     * Optional: logo navbar
     * $navLayout['brand_logo'] = 'path/di/storage/xxx.png'
     */
    $brandLogo = $navLayout['brand_logo'] ?? null;
@endphp

{{-- Optional: CSS var injection (kalau kamu mau navbar/footer baca dari var ini) --}}
<style>
    :root{
        --rg-brand-restu: {{ $cRestu }};
        --rg-brand-guru: {{ $cGuru }};
        --rg-brand-pro: {{ $cPro }};
    }
</style>

{{-- Navbar (kirim config ke partial) --}}
@include('profil.public.partials.navbar', [
    'siteLayout' => $siteLayout,
    'navLayout' => $navLayout,
    'brandLogo' => $brandLogo,
])

<main>
    @yield('content')
</main>

{{-- Footer (kirim config ke partial) --}}
@include('profil.public.partials.footer', [
    'siteLayout' => $siteLayout,
    'footLayout' => $footLayout,
])

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

{{-- main.js: reveal + helper UI --}}
<script src="{{ asset('profil/js/main.js') }}" defer></script>

@stack('scripts')
</body>
</html>
