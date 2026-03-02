{{-- resources/views/profil/public/pages/tentang.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Tentang — Restu Guru Promosindo')

@section('content')
@php
  /** @var \App\Models\PTentang|null $about */
  $about = $about ?? null;

  // fallback aman
  $d = \App\Models\PTentang::defaults();
  $get = fn($key, $fallback = null) => data_get($about, $key, $fallback ?? data_get($d, $key));

  $hero = [
      'chip' => $get('hero_chip'),
      'title_parts' => $get('hero_title_parts', []),
      'desc' => $get('hero_desc'),
      'btn1' => ['label' => $get('hero_btn1_label'), 'route' => $get('hero_btn1_route')],
      'btn2' => ['label' => $get('hero_btn2_label'), 'route' => $get('hero_btn2_route')],
  ];

  $focus = $get('focus_items', []);
  $highlights = $get('highlights', []);
  $faq = $get('faq', []);

  $owner = [
      'small' => $get('owner_small'),
      'title' => $get('owner_title'),
      'message' => $get('owner_message'),
      'name' => $get('owner_name'),
      'role' => $get('owner_role'),
      'photo' => $get('owner_photo') ? asset('storage/'.$get('owner_photo')) : null,
  ];

  $history = [
      'title' => $get('history_title'),
      'desc' => $get('history_desc'),
      'stats' => $get('history_stats', []),
  ];

  $vision = [
      'title' => $get('vision_title'),
      'desc' => $get('vision_desc'),
  ];

  $mission = [
      'title' => $get('mission_title'),
      'items' => $get('mission_items', []),
  ];

  $leaders = $get('leaders', []);
  // normalize leaders photo -> url
  $leaders = array_map(function($p){
      if (!is_array($p)) $p = [];
      if (!empty($p['photo'])) $p['photo_url'] = asset('storage/'.$p['photo']);
      return $p;
  }, is_array($leaders) ? $leaders : []);

  $clients = $get('clients', []);
  $clients = is_array($clients) ? $clients : [];
  $clientsUrls = array_map(fn($p) => asset('storage/'.$p), $clients);
  $clientsLoop = array_merge($clientsUrls, $clientsUrls);

  $colors = $get('colors', []);
  $blobBlue = $colors['blob_blue'] ?? 'var(--rg-blue)';
  $blobRed  = $colors['blob_red'] ?? 'var(--rg-red)';
  $blobYellow = $colors['blob_yellow'] ?? 'var(--rg-yellow)';
@endphp

<section class="hero-wrap about-hero" style="--rg-blob-blue: {{ $blobBlue }}; --rg-blob-red: {{ $blobRed }}; --rg-blob-yellow: {{ $blobYellow }};">
    <div class="hero-blob blob-blue"></div>
    <div class="hero-blob blob-red"></div>
    <div class="hero-blob blob-yellow"></div>
    <div class="hero-grid"></div>

    <div class="container py-5">
        <div class="row g-5 align-items-start">
            <div class="col-12 col-lg-7 reveal">
                <div class="about-chip">
                    <span class="dot"></span>
                    <span class="small fw-bold text-muted">{{ $hero['chip'] }}</span>
                </div>

                <h1 class="mt-4 font-hero display-4 lh-1" style="letter-spacing:-0.02em;">
                    @foreach($hero['title_parts'] as $p)
                        @php $c = $p['color'] ?? 'var(--rg-blue)'; @endphp
                        <span style="color: {{ $c }};">{{ $p['text'] ?? '' }}</span>
                        @if(!$loop->last) <span> </span> @endif
                    @endforeach
                </h1>

                <p class="mt-3 text-muted fs-5 about-lead">
                    {{ $hero['desc'] }}
                </p>

                <div class="mt-4 d-flex flex-wrap gap-3">
                    <a href="{{ route($hero['btn1']['route']) }}" class="btn-primary">{{ $hero['btn1']['label'] }}</a>
                    <a href="{{ route($hero['btn2']['route']) }}" class="btn-outline">{{ $hero['btn2']['label'] }}</a>
                </div>
            </div>

            <div class="col-12 col-lg-5 reveal delay-200">
                <div class="hero-panel p-4 p-md-5">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="small text-muted fw-bold">Fokus Layanan</div>
                            <div class="mt-1 fs-5 fw-bold" style="letter-spacing:-0.01em;">Pilih kategori</div>
                        </div>
                        <a href="{{ route('profil.layanan') }}" class="small fw-bold link">Detail →</a>
                    </div>

                    <div class="mt-4 d-grid gap-3">
                        @foreach($focus as $f)
                            @php
                              $accent = $f['accent'] ?? 'var(--rg-blue)';
                            @endphp
                            <div class="about-focus-item" style="--about-accent: {{ $accent }};">
                                <span class="bar"></span>
                                <span>{{ $f['label'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 card p-4">
                        <div class="small text-muted fw-bold">Butuh konsultasi?</div>
                        <div class="mt-3 d-flex flex-wrap gap-3">
                            <a href="{{ route('profil.kontak') }}" class="btn-primary">Kontak</a>
                            <a href="{{ route('profil.layanan') }}" class="btn-outline">Layanan</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-12 col-lg-6 reveal">
                <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">{{ $get('why_title') }}</h2>
                <p class="mt-2 text-muted" style="line-height:1.8;">{{ $get('why_desc') }}</p>

                <div class="card p-4 mt-4">
                    <div class="small text-muted fw-bold">Highlight</div>
                    <div class="mt-3 d-grid gap-2">
                        @foreach($highlights as $h)
                            @php $dot = $h['color'] ?? 'var(--rg-blue)'; @endphp
                            <div class="d-flex align-items-start gap-2">
                                <span class="d-inline-block rounded-circle mt-1" style="width:8px;height:8px;background: {{ $dot }};"></span>
                                <div class="fw-bold" style="line-height:1.7;">{{ $h['text'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 reveal delay-100">
                <div class="rg-faq">
                    @foreach($faq as $item)
                        <details>
                            <summary>{{ $item['q'] ?? '' }}</summary>
                            <div class="ans">{{ $item['a'] ?? '' }}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="card owner-card p-4 p-md-5 reveal">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-md-4 text-center">
                    <div class="owner-photo mx-auto">
                        @if($owner['photo'])
                            <img src="{{ $owner['photo'] }}" alt="Owner">
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    <div class="small text-muted fw-bold">{{ $owner['small'] }}</div>
                    <h3 class="mt-2 fw-bold" style="letter-spacing:-0.02em;">{{ $owner['title'] }}</h3>
                    <p class="mt-3 text-muted" style="line-height:1.8;">{{ $owner['message'] }}</p>

                    <div class="mt-3 fw-bold">{{ $owner['name'] }}</div>
                    <div class="text-muted small">{{ $owner['role'] }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-12 col-lg-6 reveal">
                <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">{{ $history['title'] }}</h2>
                <p class="mt-2 text-muted" style="line-height:1.8;">{{ $history['desc'] }}</p>

                <div class="row g-3 mt-2">
                    @foreach($history['stats'] as $s)
                        <div class="col-12 col-md-6">
                            <div class="mini-stat">
                                <div class="k">{{ $s['k'] ?? '' }}</div>
                                <div class="v mt-2">{{ $s['v'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-lg-6 reveal delay-100">
                <div class="card p-4 p-md-5">
                    <div class="small text-muted fw-bold">{{ $vision['title'] }}</div>
                    <div class="mt-2 fw-bold fs-4" style="letter-spacing:-0.02em;">{{ $vision['desc'] }}</div>

                    <hr class="my-4">

                    <div class="small text-muted fw-bold">{{ $mission['title'] }}</div>
                    <ul class="mt-3 mb-0" style="line-height:1.9;">
                        @foreach($mission['items'] as $m)
                            <li class="text-muted fw-bold" style="color: var(--rg-muted) !important;">{{ $m }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="text-center reveal">
            <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">Tim Kami</h2>
            <p class="mt-2 text-muted" style="line-height:1.8;">Beberapa peran utama dalam proses produksi dan layanan.</p>
        </div>

        <div class="row g-4 mt-2">
            @foreach($leaders as $i => $p)
                <div class="col-12 col-md-4">
                    <div class="card leader-card reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif">
                        <div class="d-flex align-items-center gap-3">
                            <div class="leader-photo">
                                @if(!empty($p['photo_url']))
                                    <img src="{{ $p['photo_url'] }}" alt="{{ $p['name'] ?? 'Leader' }}">
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold">{{ $p['name'] ?? '' }}</div>
                                <div class="text-muted small">{{ $p['role'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="text-center reveal">
            <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">Client Kami</h2>
            <p class="mt-2 text-muted" style="line-height:1.8;">Beberapa brand yang pernah bekerjasama.</p>
        </div>

        <div class="rg-marquee mt-4 reveal">
            <div class="rg-marquee__viewport">
                <div class="rg-marquee__track" aria-label="Clients marquee">
                    @foreach($clientsLoop as $logo)
                        <img class="rg-logo" src="{{ $logo }}" alt="Client Logo">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
