{{-- resources/views/profil/public/partials/navbar.blade.php --}}
@php
    $routeName = request()->route()?->getName();

    // ============ LOAD FROM DB (p_site_layouts.navbar) ============
    $navLayout = $navLayout ?? [];

    // fallback brand parts (warna seperti sekarang)
    $brandParts = $navLayout['brand_parts'] ?? [
        ['text' => 'Restu',      'color' => 'var(--rg-blue)'],
        ['text' => ' Guru',      'color' => 'var(--rg-yellow)'],
        ['text' => ' Promosindo','color' => 'var(--rg-red)'],
    ];
    if (!is_array($brandParts)) $brandParts = [];

    $logoPath = $navLayout['logo_path'] ?? null;

    // menu items (route name)
    $items = $navLayout['menu'] ?? [
        ['label' => 'Beranda', 'route' => 'profil.beranda'],
        ['label' => 'Tentang', 'route' => 'profil.tentang'],
        ['label' => 'Layanan', 'route' => 'profil.layanan'],
        ['label' => 'Berita',  'route' => 'profil.berita'],
        ['label' => 'Kontak',  'route' => 'profil.kontak'],
    ];
    if (!is_array($items)) $items = [];

    // color brand map for pills
    $brandMap = [
        'profil.beranda' => 'blue',
        'profil.tentang' => 'yellow',
        'profil.layanan' => 'red',
        'profil.berita'  => 'blue',
        'profil.kontak'  => 'blue',
    ];

    $colorStyles = [
        'red' => ['bg' => 'rgba(235,31,39,.12)', 'bd' => 'rgba(235,31,39,.28)', 'tx' => 'var(--rg-red)'],
        'yellow' => ['bg' => 'rgba(251,237,28,.18)', 'bd' => 'rgba(251,237,28,.34)', 'tx' => 'var(--rg-yellow-text)'],
        'blue' => ['bg' => 'rgba(44,170,225,.14)', 'bd' => 'rgba(44,170,225,.30)', 'tx' => 'var(--rg-blue)'],
    ];

    $isActive = function (string $name) use ($routeName) {
        if ($name === 'profil.berita') return $routeName === 'profil.berita' || $routeName === 'profil.berita.show';
        return $routeName === $name;
    };
@endphp

<style>
    #rgHeader{
        position: sticky;
        top: 0;
        z-index: 1050;
        transition: background-color .25s ease, box-shadow .25s ease, border-color .25s ease, backdrop-filter .25s ease;
        will-change: background-color, box-shadow, border-color, backdrop-filter;
    }
    #rgHeader.rg-top{
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(226,232,240,.9);
        box-shadow: none;
    }
    #rgHeader.rg-scrolled{
        background: rgba(255,255,255,.58);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(226,232,240,.75);
        box-shadow: 0 10px 28px rgba(15,23,42,.08);
    }

    /* ✅ brand logo */
    .rg-brand-logo{
        width: 34px;
        height: 34px;
        object-fit: contain;
        border-radius: 8px;
    }
</style>

<header id="rgHeader" class="rg-top">
    <nav class="navbar navbar-expand-md py-2">
        <div class="container">
            <a href="{{ route('profil.beranda') }}" class="d-flex align-items-center gap-3 text-decoration-none">
                @if(!empty($logoPath))
                    <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" class="rg-brand-logo">
                @endif

                <div class="font-hero fs-3 lh-1" style="letter-spacing:-0.02em;">
                    @foreach($brandParts as $p)
                        @php
                            $tx = $p['text'] ?? '';
                            $cl = $p['color'] ?? 'var(--rg-blue)';
                        @endphp
                        @if($tx !== '')
                            <span style="color: {{ $cl }}">{{ $tx }}</span>
                        @endif
                    @endforeach
                </div>
            </a>

            <button class="navbar-toggler rg-mobile-btn" type="button"
                    data-bs-toggle="collapse" data-bs-target="#rgNavbar"
                    aria-controls="rgNavbar" aria-expanded="false" aria-label="Toggle navigation">
                Menu
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="rgNavbar">
                <ul class="navbar-nav align-items-md-center gap-2 mt-3 mt-md-0">
                    @foreach($items as $it)
                        @php
                            $route = $it['route'] ?? '';
                            $label = $it['label'] ?? '';
                            if ($route === '' || $label === '') continue;

                            $active = $isActive($route);
                            $c = $colorStyles[$brandMap[$route] ?? 'blue'];
                        @endphp

                        <li class="nav-item">
                            <a href="{{ route($route) }}"
                               class="rg-pill"
                               @if($active)
                                   style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['bd'] }}; color: {{ $c['tx'] }};"
                               @else
                                   style="border: 1px solid transparent; color: var(--rg-text);"
                               @endif
                            >
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                    @guest

                    <li class="nav-item">
                        <a href="{{ route('auth.login') }}"
                           class="rg-pill"
                           @if($active)
                               style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['bd'] }}; color: {{ $c['tx'] }};"
                           @else
                               style="border: 1px solid transparent; color: var(--rg-text);"
                           @endif
                        >
                            Login
                        </a>
                    </li>
                    @endguest

                </ul>

                {{-- Mobile card --}}
                <div class="d-md-none mt-3 rg-mobile-card p-2">
                    @foreach($items as $it)
                        @php
                            $route = $it['route'] ?? '';
                            $label = $it['label'] ?? '';
                            if ($route === '' || $label === '') continue;

                            $active = $isActive($route);
                            $c = $colorStyles[$brandMap[$route] ?? 'blue'];
                        @endphp

                        <a href="{{ route($route) }}"
                           class="d-block rg-pill w-100 justify-content-start mb-2"
                           @if($active)
                               style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['bd'] }}; color: {{ $c['tx'] }};"
                           @else
                               style="border: 1px solid transparent; color: var(--rg-text);"
                           @endif
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </nav>

    <script>
        (function () {
            var header = document.getElementById('rgHeader');
            if (!header) return;

            function onScroll() {
                var y = window.scrollY || document.documentElement.scrollTop || 0;
                if (y > 6) {
                    header.classList.remove('rg-top');
                    header.classList.add('rg-scrolled');
                } else {
                    header.classList.add('rg-top');
                    header.classList.remove('rg-scrolled');
                }
            }

            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        })();
    </script>
</header>
