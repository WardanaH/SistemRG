{{-- resources/views/profil/public/pages/layanan.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Layanan — Restu Guru Promosindo')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    // dari controller: $data
    $data = $data ?? [];

    $hero = $data;
    $summary = $data;
    $why = $data;
    $categories = $data['categories'] ?? [];
    $cta = $data;

    // helper warna: value sudah var/hex -> langsung pakai
    $color = function($v, $fallback = 'var(--rg-blue)'){
        $v = is_string($v) ? trim($v) : '';
        return $v !== '' ? $v : $fallback;
    };

    // WA link builder (global)
    $waLink = function($wa) {
        $wa = trim((string)$wa);
        if ($wa === '') return '#';
        if (str_starts_with($wa, 'http://') || str_starts_with($wa, 'https://')) return $wa;

        // nomor -> wa.me (hapus non-digit)
        $num = preg_replace('/\D+/', '', $wa);
        if ($num === '') return '#';
        return 'https://wa.me/' . $num;
    };

    $wa_value = $data['wa_value'] ?? null;

    // route fallback (kalau suatu saat ada sisa data lama)
    $routeMap = function (?string $name) {
        $name = $name ?: 'profil.kontak';
        $map = [
            'home' => 'profil.beranda',
            'about' => 'profil.tentang',
            'services' => 'profil.layanan',
            'news' => 'profil.berita',
            'contact' => 'profil.kontak',
        ];
        return $map[$name] ?? $name;
    };

    $heroTitleParts = $data['hero_title_parts'] ?? [
        ['color'=>'var(--rg-blue)', 'text'=>'Layanan'],
        ['color'=>'var(--rg-yellow)', 'text'=>'Restu Guru'],
        ['color'=>'var(--rg-red)', 'text'=>'Promosindo'],
    ];

    // WHY cards
    $whyCards = $data['why_cards'] ?? [];

    // categories fallback aman (kalau DB kosong banget)
    if (empty($categories)) {
        $categories = [
            [
                'title' => 'Outdoor',
                'desc' => 'Kebutuhan promosi luar ruang: kuat, tahan cuaca, dan terlihat jelas.',
                'items' => [
                    ['title'=>'Billboard / Baliho', 'desc'=>'Ukuran besar untuk jangkauan luas.', 'image'=>null],
                    ['title'=>'Spanduk / Banner', 'desc'=>'Promosi event dan toko.', 'image'=>null],
                    ['title'=>'Neonbox / Signage', 'desc'=>'Branding permanen lebih standout.', 'image'=>null],
                ],
            ],
            [
                'title' => 'Indoor',
                'desc' => 'Kebutuhan promosi dalam ruang: detail tajam dan warna konsisten.',
                'items' => [
                    ['title'=>'Backdrop', 'desc'=>'Event indoor lebih rapi & premium.', 'image'=>null],
                    ['title'=>'X-Banner / Roll Up', 'desc'=>'Praktis untuk promosi instan.', 'image'=>null],
                    ['title'=>'Poster', 'desc'=>'Tampilan informatif, hasil tajam.', 'image'=>null],
                ],
            ],
            [
                'title' => 'Multi Printing',
                'desc' => 'Kebutuhan cetak kecil-menengah: cepat, rapi, dan fleksibel.',
                'items' => [
                    ['title'=>'Stiker', 'desc'=>'Cutting & finishing rapih.', 'image'=>null],
                    ['title'=>'Label Produk', 'desc'=>'Kuat dan presisi sesuai kebutuhan.', 'image'=>null],
                    ['title'=>'Akrilik / Display', 'desc'=>'Untuk kebutuhan display & dekor.', 'image'=>null],
                ],
            ],
        ];
    }
@endphp


{{-- HERO --}}
<section class="section section-soft rg-services-hero position-relative">
    <div class="position-absolute top-0 start-0 w-100 h-100 pe-none">
        <div class="rg-blob rg-blob-blue"></div>
        <div class="rg-blob rg-blob-red"></div>
        <div class="rg-blob rg-blob-yellow"></div>
        <div class="rg-dot-grid"></div>
    </div>

    <div class="rg-wrap position-relative">
        <div class="row align-items-center g-4 g-lg-5">
            {{-- LEFT --}}
            <div class="col-12 col-lg-7 reveal">
                <div class="rg-hero-left">
                    <div class="rg-chip">
                        <span class="rounded-circle d-inline-block"
                            style="width:10px;height:10px;background: {{ $color($data['hero_chip_dot'] ?? 'var(--rg-blue)') }}"></span>
                        <span style="color:#334155;">{{ $data['hero_chip_text'] ?? 'Layanan' }}</span>
                    </div>

                    <h1 class="font-hero rg-services-title">
                        @foreach($heroTitleParts as $p)
                            <span style="color: {{ $color($p['color'] ?? 'var(--rg-blue)') }}">
                                {{ $p['text'] ?? '' }}
                            </span>@if(!$loop->last) <span> </span> @endif
                        @endforeach
                    </h1>

                    <p class="mt-3 text-muted rg-max-2xl rg-hero-desc">
                        {{ $data['hero_desc'] ?? '' }}
                    </p>

                    <div class="mt-4 d-flex flex-wrap gap-3 rg-hero-actions">
                        <a href="{{ $waLink($wa_value) }}" target="_blank" rel="noopener" class="btn-primary">
                            {{ $data['hero_btn1_text'] ?? 'WhatsApp' }}
                        </a>
                        <a href="{{ route($routeMap($data['hero_btn2_route'] ?? 'profil.kontak')) }}" class="btn-outline">
                            {{ $data['hero_btn2_text'] ?? 'Kontak' }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-12 col-lg-5 reveal delay-200">
                <div class="rg-summary-card">
                    <div class="rg-summary-accent"></div>

                    <div class="rg-summary-head">
                        <div class="t">{{ $data['summary_title'] ?? 'Ringkasan' }}</div>
                        <span class="text-muted" style="font-size:.9rem;">Pilih kategori</span>
                    </div>

                    <ul class="rg-summary-list">
                        @foreach(($data['summary_items'] ?? []) as $it)
                            <li class="rg-summary-pill">
                                <span class="rg-dot" style="background: {{ $color($it['dot'] ?? 'var(--rg-blue)') }}"></span>
                                <span style="font-weight:800;">{{ $it['text'] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- WHY --}}
<section class="section section-soft">
    <div class="rg-wrap">
        <div class="reveal">
            <h2 class="fw-bold rg-section-title">{{ $data['why_title'] ?? '' }}</h2>
            <p class="mt-2 text-muted rg-max-2xl">{{ $data['why_desc'] ?? '' }}</p>
        </div>

        <div class="row g-4 mt-4">
            @foreach(($whyCards ?? []) as $i => $c)
                @php
                    $img = $c['image'] ?? null;
                    $imgUrl = $img ? Storage::url($img) : null;
                    $accent = $color($c['accent'] ?? 'var(--rg-blue)');
                @endphp

                <div class="col-12 col-md-4">
                    <div class="rg-why-card rg-top-accent reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif"
                         style="--rg-accent: {{ $accent }};">
                        <div class="rg-card-body">
                            <div class="rg-imgbox mb-4">
                                <div class="rg-aspect-16x10 d-flex align-items-center justify-content-center text-muted"
                                     style="font-size:.75rem;">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="{{ $c['title'] ?? '' }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        Image (opsional)
                                    @endif
                                </div>
                            </div>

                            <h3 class="mb-0">{{ $c['title'] ?? '' }}</h3>
                            <p class="mt-2 mb-0 text-muted">{{ $c['desc'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- CATEGORIES (fixed 3) --}}
@foreach(($categories ?? []) as $ci => $cat)
    @php $soft = $ci % 2 === 1; @endphp
    <section class="section {{ $soft ? 'section-soft' : '' }}">
        <div class="rg-wrap">
            <div class="reveal">
                <h2 class="fw-bold rg-section-title">{{ $cat['title'] ?? '' }}</h2>
                <p class="mt-2 text-muted rg-max-2xl">{{ $cat['desc'] ?? '' }}</p>
            </div>

            <div class="row g-4 mt-4">
                @foreach(($cat['items'] ?? []) as $i => $it)
                    @php
                        $img = $it['image'] ?? null;
                        $imgUrl = $img ? Storage::url($img) : null;
                    @endphp

                    <div class="col-12 col-md-4">
                        <div class="card rg-p-6 reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif">
                            <div class="mb-4 rg-imgbox">
                                <div class="rg-aspect-16x10 d-flex align-items-center justify-content-center text-muted"
                                     style="font-size:.75rem;">
                                    @if($imgUrl)
                                        <img src="{{ $imgUrl }}" alt="{{ $it['title'] ?? '' }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        Image (opsional)
                                    @endif
                                </div>
                            </div>

                            <h3 class="fw-semibold mb-0" style="font-size:1rem;">{{ $it['title'] ?? '' }}</h3>
                            <p class="mt-2 mb-0 text-muted" style="font-size:.875rem;">{{ $it['desc'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endforeach


{{-- CTA --}}
<section class="section">
    <div class="rg-wrap">
        <div class="card rg-cta-pad reveal">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-4">
                <div>
                    <h3 class="fw-bold mb-0 rg-cta-title">{{ $data['cta_title'] ?? '' }}</h3>
                    <p class="mt-2 text-muted mb-0">{{ $data['cta_desc'] ?? '' }}</p>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ $waLink($wa_value) }}" target="_blank" rel="noopener" class="btn-primary">
                        {{ $data['cta_btn1_text'] ?? 'WhatsApp' }}
                    </a>
                    <a href="{{ route($routeMap($data['cta_btn2_route'] ?? 'profil.kontak')) }}" class="btn-outline">
                        {{ $data['cta_btn2_text'] ?? 'Kontak' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
