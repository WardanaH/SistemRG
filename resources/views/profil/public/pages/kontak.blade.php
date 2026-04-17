{{-- resources/views/profil/public/pages/kontak.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Kontak — Restu Guru Promosindo')

@section('content')
@php
    /** @var \App\Models\PKontakPage $page */
    /** @var \Illuminate\Support\Collection|\App\Models\PKontakBranch[] $branches */
    /** @var \App\Models\PBeranda $home */

    $waLink = $waLink ?? ($home->waLink() ?? 'https://wa.me/6281234567890');

    $pills = $page->hero_pills ?? [];
    if (!is_array($pills)) $pills = [];
    for ($i=0; $i<3; $i++) {
        if (!isset($pills[$i]) || !is_array($pills[$i])) $pills[$i] = [];
        $pills[$i]['text']  = $pills[$i]['text']  ?? '';
        $pills[$i]['color'] = $pills[$i]['color'] ?? 'var(--rg-blue)';
    }

    $stats = $page->stats ?? [];
    if (!is_array($stats)) $stats = [];
    $defaultAcc = ['var(--rg-blue)','var(--rg-red)','var(--rg-yellow)'];
    for ($i=0; $i<3; $i++) {
        if (!isset($stats[$i]) || !is_array($stats[$i])) $stats[$i] = [];
        $stats[$i]['k'] = $stats[$i]['k'] ?? '';
        $stats[$i]['v'] = $stats[$i]['v'] ?? '';
        $stats[$i]['accent'] = $stats[$i]['accent'] ?? ($defaultAcc[$i] ?? 'var(--rg-blue)');
    }

    // cabang utama untuk map (ambil cabang pertama yang aktif)
    $primary = null;
    if ($branches && count($branches)) {
        $primary = $branches->first();
    }

    // build URL embed Google Maps (tanpa API key)
    $embedSrc = null;

    if ($primary && is_numeric($primary->lat) && is_numeric($primary->lng)) {
        $lat = (float)$primary->lat;
        $lng = (float)$primary->lng;
        $embedSrc = "https://www.google.com/maps?q={$lat},{$lng}&z=14&output=embed";
    } elseif ($primary && !empty($primary->maps_url)) {
        // fallback: coba embed dari query teks (nama + alamat)
        $q = trim(($primary->name ?? '') . ' ' . ($primary->address ?? ''));
        if ($q !== '') {
            $embedSrc = 'https://www.google.com/maps?q=' . urlencode($q) . '&z=14&output=embed';
        }
    }

    // fallback final kalau kosong semua
    if (!$embedSrc) {
        $embedSrc = 'https://www.google.com/maps?q=' . urlencode('Restu Guru Promosindo Banjarmasin') . '&z=13&output=embed';
    }

    $primaryMapsUrl = ($primary && !empty($primary->maps_url)) ? $primary->maps_url : null;
@endphp

{{-- HERO --}}
<section class="section section-soft rg-contact-hero position-relative">
    <div class="rg-contact-hero-bg pe-none"></div>

    <div class="rg-wrap position-relative">
        <div class="row g-4 align-items-end">
            <div class="col-12 col-lg-8 reveal">
                <div class="rg-contact-chip">
                    <span class="dot"></span>
                    <span class="t">{{ $page->hero_chip }}</span>
                </div>

                <h1 class="mt-3 font-hero rg-contact-title">
                    {{ $page->hero_title }}
                </h1>

                <p class="mt-2 text-muted rg-contact-lead">
                    {{ $page->hero_lead }}
                </p>

                <div class="mt-4 d-flex flex-wrap gap-3">
                    <a href="{{ $waLink }}" target="_blank" class="btn-primary">{{ $page->hero_btn_wa_label }}</a>
                    <a href="{{ route($page->hero_btn2_route) }}" class="btn-outline">{{ $page->hero_btn2_label }}</a>
                </div>
            </div>

            <div class="col-12 col-lg-4 reveal delay-200">
                <div class="rg-contact-mini">
                    <div class="rg-contact-mini-accent"></div>
                    <div class="p-4">
                        <div class="fw-bold" style="letter-spacing:-0.01em;">{{ $page->panel_title }}</div>
                        <div class="text-muted mt-1" style="line-height:1.7; white-space:pre-line;">
                            {{ $page->panel_desc }}
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            @for($i=0; $i<3; $i++)
                                @php
                                    $tx = $pills[$i]['text'] ?? '';
                                    $cl = $pills[$i]['color'] ?? 'var(--rg-blue)';
                                @endphp
                                @if($tx !== '')
                                    <span class="rg-contact-pill">
                                        <span class="d" style="background:{{ $cl }}"></span>
                                        {{ $tx }}
                                    </span>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MINI STATS --}}
        <div class="row g-3 mt-4 reveal delay-200">
            @for($i=0; $i<3; $i++)
                <div class="col-12 col-md-4">
                    <div class="rg-contact-stat" style="--rg-accent: {{ $stats[$i]['accent'] ?? $defaultAcc[$i] }};">
                        <div class="k">{{ $stats[$i]['k'] }}</div>
                        <div class="v">{{ $stats[$i]['v'] }}</div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</section>

{{-- CABANG + MAP --}}
<section class="section">
    <div class="rg-wrap">
        <div class="row g-4 align-items-start">
            {{-- LIST CABANG --}}
            <div class="col-12 col-lg-5 reveal">
                <h2 class="fw-bold rg-section-title mb-0">{{ $page->branches_heading }}</h2>
                <p class="mt-2 text-muted mb-0">{{ $page->branches_desc }}</p>

                <div class="mt-4 d-grid gap-3">
                    @forelse($branches as $i => $b)
                        <a href="{{ $b->maps_url ?: '#' }}" target="_blank" rel="noopener"
                           class="card p-4 hover-lift rg-branch-card reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @elseif($i===3) delay-300 @endif">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="fw-bold">{{ $b->name }}</div>
                                    <div class="text-muted mt-1" style="font-size:.95rem; line-height:1.6;">
                                        {{ $b->address }}
                                    </div>
                                </div>
                                <div class="fw-bold" style="color: var(--rg-blue); white-space:nowrap;">
                                    {{ $page->branch_open_label }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="card p-4 text-center">
                            <div class="fw-bold">Belum ada cabang.</div>
                            <div class="text-muted mt-1">Admin bisa menambah cabang di halaman admin kontak.</div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 rg-contact-help">
                    <div class="small text-muted fw-bold">{{ $page->help_title }}</div>
                    <div class="mt-3 d-flex flex-wrap gap-3">
                        <a href="{{ $waLink }}" target="_blank" class="btn-primary">{{ $page->help_btn_wa }}</a>
                        <a href="{{ route($page->help_btn2_route) }}" class="btn-outline">{{ $page->help_btn2_label }}</a>
                    </div>
                </div>
            </div>

            {{-- MAP (STATIS: GOOGLE MAPS EMBED) --}}
            <div class="col-12 col-lg-7 reveal delay-200">
                <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap">
                    <div>
                        <h2 class="fw-bold rg-section-title mb-0">{{ $page->map_heading }}</h2>
                        <p class="mt-2 text-muted mb-0">{{ $page->map_desc }}</p>
                    </div>

                    @if($primaryMapsUrl)
                        <a href="{{ $primaryMapsUrl }}" target="_blank" class="btn-outline" style="white-space:nowrap;">
                            Buka Cabang Utama →
                        </a>
                    @endif
                </div>

                <div class="mt-4 card overflow-hidden rg-map-card">
                    <div class="ratio ratio-16x9">
                        <iframe
                            src="{{ $embedSrc }}"
                            style="border:0;"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Peta Cabang">
                        </iframe>
                    </div>
                </div>

                <div class="mt-3 text-muted small">
                    Map statis pakai embed Google Maps (tanpa Leaflet / tanpa API key). Lokasi mengikuti cabang pertama.
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section section-soft">
    <div class="rg-wrap">
        <div class="card rg-cta-pad reveal">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-4">
                <div>
                    <h3 class="fw-bold mb-0 rg-cta-title">{{ $page->cta_title }}</h3>
                    <p class="mt-2 text-muted mb-0 rg-max-2xl">
                        {{ $page->cta_desc }}
                    </p>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ $waLink }}" target="_blank" class="btn-primary">{{ $page->cta_btn_wa }}</a>
                    <a href="{{ route($page->cta_btn_back_route) }}" class="btn-outline">{{ $page->cta_btn_back }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
