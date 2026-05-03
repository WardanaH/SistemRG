{{-- resources/views/profil/public/pages/beranda.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Beranda — Restu Guru Promosindo')

@section('content')
@php
    /** @var \App\Models\PBeranda $home */

    // helper resolve warna: bisa var(--rg-blue) atau #hex
    $c = fn($v, $fallback) => (is_string($v) && trim($v) !== '') ? $v : $fallback;

    $colors = $home->colors ?? [];
    $blobBlue   = $c($colors['blob_blue'] ?? null, 'var(--rg-blue)');
    $blobRed    = $c($colors['blob_red'] ?? null, 'var(--rg-red)');
    $blobYellow = $c($colors['blob_yellow'] ?? null, 'var(--rg-yellow)');
    $softBg     = $c($colors['soft_bg'] ?? null, '#f6f7fb');
    $linkAccent = $c($colors['link_accent'] ?? null, 'var(--rg-blue)');
    $btnPrimary = $c($colors['btn_primary'] ?? null, 'var(--rg-red)');

    $branches = $home->hero_branches ?? [];
    $mainCards = $home->main_cards ?? [];
    $whyCards = $home->why_cards ?? [];

    $heroTitleParts = $home->hero_title_parts ?? [];
    $heroLabels = $home->hero_labels ?? [];
    $heroCats = $home->hero_cats ?? [];

    // latestNews dari controller
@endphp

{{-- token override dari DB --}}
<style>
  :root{
    --rg-blob-blue: {{ $blobBlue }};
    --rg-blob-red: {{ $blobRed }};
    --rg-blob-yellow: {{ $blobYellow }};
    --rg-soft-bg: {{ $softBg }};
    --rg-link-accent: {{ $linkAccent }};
    --rg-btn-primary: {{ $btnPrimary }};
  }
</style>

{{-- HERO --}}
<section class="hero-wrap">
  <div class="hero-blob blob-blue"></div>
  <div class="hero-blob blob-red"></div>
  <div class="hero-blob blob-yellow"></div>
  <div class="hero-grid"></div>

  <div class="container py-5">
    <div class="row g-5 align-items-start">

      {{-- LEFT --}}
      <div class="col-12 col-lg-7 reveal">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill border bg-white bg-opacity-75">
          <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background: {{ $c($home->hero_badge_dot, 'var(--rg-yellow)') }}"></span>
          <span class="small fw-bold text-muted">{{ $home->hero_badge_label ?? 'Percetakan & Advertising' }}</span>
        </div>

        @php
          // Default aman kalau DB kosong
          if(!is_array($heroTitleParts) || count($heroTitleParts) === 0){
            $heroTitleParts = [
              ['text'=>'Restu','color'=>'var(--rg-blue)'],
              ['text'=>'Guru','color'=>'var(--rg-yellow)'],
              ['text'=>'Promosindo','color'=>'var(--rg-red)'],
            ];
          }

          // Baris 1 = item 0 & 1, baris 2 = sisanya
          $p0 = $heroTitleParts[0] ?? ['text'=>'Restu','color'=>'var(--rg-blue)'];
          $p1 = $heroTitleParts[1] ?? ['text'=>'Guru','color'=>'var(--rg-yellow)'];
          $rest = array_slice($heroTitleParts, 2);
          if(count($rest) === 0){
            $rest = [['text'=>'Promosindo','color'=>'var(--rg-red)']];
          }
        @endphp

        {{-- ✅ FIX JARAK: pakai flex gap, bukan &nbsp; --}}
        <h1 class="mt-4 font-hero hero-title">
          <span class="hero-title-row">
            <span class="hero-title-word" style="color: {{ $c($p0['color'] ?? null, 'var(--rg-blue)') }}">
              {{ $p0['text'] ?? '' }}
            </span>
            <span class="hero-title-word" style="color: {{ $c($p1['color'] ?? null, 'var(--rg-yellow)') }}">
              {{ $p1['text'] ?? '' }}
            </span>
          </span>

          <span class="hero-title-row">
            @foreach($rest as $p)
              <span class="hero-title-word" style="color: {{ $c($p['color'] ?? null, 'var(--rg-red)') }}">
                {{ $p['text'] ?? '' }}
              </span>
            @endforeach
          </span>
        </h1>

        <p class="mt-3 text-muted fs-5" style="max-width: 44rem; line-height: 1.75;">
          {{ $home->hero_desc ?? '' }}
        </p>

        <div class="mt-4 d-flex flex-wrap gap-3">
          <a href="{{ $home->hero_btn1_route ? route($home->hero_btn1_route) : route('profil.layanan') }}" class="btn-primary">
            {{ $home->hero_btn1_label ?? 'Lihat Layanan' }}
          </a>
          <a href="{{ $home->hero_btn2_route ? route($home->hero_btn2_route) : route('profil.kontak') }}" class="btn-outline">
            {{ $home->hero_btn2_label ?? 'Konsultasi' }}
          </a>
        </div>

        @if(is_array($branches) && count($branches))
          <div class="mt-4 d-flex flex-wrap align-items-center gap-2 small">
            <span class="text-muted fw-bold">Cabang:</span>
            @foreach($branches as $b)
              <span class="px-3 py-2 rounded-pill border bg-white bg-opacity-75 fw-bold">{{ $b }}</span>
            @endforeach
          </div>
        @endif

        @if(is_array($heroLabels) && count($heroLabels))
          <div class="mt-4 d-flex flex-wrap gap-4 small text-muted">
            @foreach($heroLabels as $it)
              <div class="d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background: {{ $c($it['color'] ?? null,'var(--rg-blue)') }}"></span>
                <span class="fw-bold">{{ $it['text'] ?? '' }}</span>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- RIGHT PANEL --}}
      <div class="col-12 col-lg-5 reveal delay-200">
        <div class="hero-panel rg-hero-right">
          <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
              <div class="small text-muted fw-bold">{{ $home->hero_right_small_label ?? 'Kebutuhan Promosi' }}</div>
              <div class="mt-1 fs-5 fw-bold" style="letter-spacing:-0.01em;">
                {{ $home->hero_right_title ?? 'Pilih kategori cepat' }}
              </div>
            </div>
            <a href="{{ $home->hero_right_detail_route ? route($home->hero_right_detail_route) : route('profil.layanan') }}" class="small fw-bold link">Detail →</a>
          </div>

          <div class="rg-cat-grid">
            @foreach($heroCats as $cat)
              @php $cc = $c($cat['color'] ?? null,'var(--rg-blue)'); @endphp
              <div class="rg-cat-item" style="--cat-accent: {{ $cc }};">
                {{ $cat['text'] ?? '' }}
              </div>
            @endforeach
          </div>

          <div class="rg-ask-box">
            <div class="rg-ask-label">{{ $home->hero_ask_label ?? 'Tanya cepat sekarang:' }}</div>
            <div class="rg-ask-actions">
              <a href="{{ $home->waLink() }}" target="_blank" class="btn-primary">
                {{ $home->hero_ask_wa_label ?? 'WhatsApp' }}
              </a>
              <a href="{{ $home->hero_ask_contact_route ? route($home->hero_ask_contact_route) : route('profil.kontak') }}" class="btn-outline">
                {{ $home->hero_ask_contact_label ?? 'Kontak' }}
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- LAYANAN UTAMA --}}
<section class="section">
  <div class="container">
    <div class="reveal">
      <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">Layanan Utama</h2>
      <p class="mt-2 text-muted" style="max-width: 44rem; line-height: 1.8;">
        Beberapa layanan utama yang sering dipesan.
      </p>
    </div>

    <div class="row g-4 mt-2">
      @foreach($mainCards as $i => $it)
        <div class="col-12 col-md-4">
          <div class="card p-4 reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif">
            <div class="mb-3 overflow-hidden rounded-4 border bg-white bg-opacity-75">
              <div class="ratio ratio-16x9 d-flex align-items-center justify-content-center small text-muted fw-bold">
                @php $img = $it['image'] ?? null; @endphp
                @if($img)
                  <img src="{{ asset('storage/'.$img) }}" alt="{{ $it['title'] ?? '' }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                  Image {{ $it['title'] ?? '' }} (upload dari admin)
                @endif
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <span class="d-inline-block rounded-circle" style="width:12px;height:12px;background: {{ $c($it['dot'] ?? null, 'var(--rg-blue)') }}"></span>
              <h3 class="h5 fw-bold mb-0">{{ $it['title'] ?? '' }}</h3>
            </div>

            <p class="mt-3 small text-muted mb-0" style="line-height:1.8;">
              {{ $it['desc'] ?? '' }}
            </p>

            <div class="mt-4">
              <a href="{{ !empty($it['route']) ? route($it['route']) : route('profil.layanan') }}" class="small fw-bold link">Lihat detail →</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- WHY --}}
<section class="section section-soft">
  <div class="container">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-md-between gap-3 reveal">
      <div>
        <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">{{ $home->why_title ?? 'Kenapa Memilih Kami' }}</h2>
        <p class="mt-2 text-muted" style="max-width: 44rem; line-height: 1.8;">
          {{ $home->why_desc ?? '' }}
        </p>
      </div>
      <div class="d-flex flex-wrap gap-3">
        <a href="{{ $home->why_btn1_route ? route($home->why_btn1_route) : route('profil.layanan') }}" class="btn-primary">{{ $home->why_btn1_label ?? 'Lihat Layanan' }}</a>
        <a href="{{ $home->why_btn2_route ? route($home->why_btn2_route) : route('profil.kontak') }}" class="btn-outline">{{ $home->why_btn2_label ?? 'Kontak' }}</a>
      </div>
    </div>

    <div class="row g-4 mt-2">
      @foreach($whyCards as $i => $w)
        @php $accent = $c($w['accent'] ?? null, 'var(--rg-blue)'); @endphp
        <div class="col-12 col-md-4">
          <div class="card why-card reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif" style="--why-accent: {{ $accent }};">
            <div class="d-flex align-items-start gap-3">
              <div class="why-icon">
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background: var(--why-accent);"></span>
              </div>
              <div>
                <div class="why-title">{{ $w['title'] ?? '' }}</div>
                <div class="why-text mt-2">{{ $w['desc'] ?? '' }}</div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ABOUT --}}
<section class="section">
  <div class="container">
    <div class="row g-4 align-items-start">
      <div class="col-12 col-lg-6 reveal">
        <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">{{ $home->about_title ?? 'Tentang Singkat' }}</h2>
        <p class="mt-3 text-muted" style="max-width: 40rem; line-height: 1.8;">
          {{ $home->about_desc ?? '' }}
        </p>
        <div class="mt-4 d-flex flex-wrap gap-3">
          <a href="{{ $home->about_btn1_route ? route($home->about_btn1_route) : route('profil.tentang') }}" class="btn-primary">{{ $home->about_btn1_label ?? 'Baca Selengkapnya' }}</a>
          <a href="{{ $home->about_btn2_route ? route($home->about_btn2_route) : route('profil.kontak') }}" class="btn-outline">{{ $home->about_btn2_label ?? 'Kontak' }}</a>
        </div>
      </div>

      <div class="col-12 col-lg-6 reveal delay-200">
        <div class="card p-4">
          <div class="small text-muted fw-bold">Cakupan Cabang</div>

          <div class="row g-3 mt-2">
            @foreach(($home->about_branches ?? []) as $b)
              <div class="col-12 col-sm-6">
                <div class="card p-3 hover-lift">
                  <div class="fw-bold">{{ $b }}</div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-3 small text-muted" style="line-height:1.8;">
            {{ $home->about_small_text ?? '' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- NEWS --}}
<section class="section section-soft">
  <div class="container">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-md-between gap-3 reveal">
      <div>
        <h2 class="fw-bold display-6" style="letter-spacing:-0.02em;">{{ $home->news_title ?? 'Berita Terbaru' }}</h2>
        <p class="mt-2 text-muted" style="max-width: 44rem; line-height: 1.8;">
          {{ $home->news_desc ?? '' }}
        </p>
      </div>
      <div class="d-flex flex-wrap gap-3">
        <a href="{{ $home->news_btn_route ? route($home->news_btn_route) : route('profil.berita') }}" class="btn-outline">
          {{ $home->news_btn_label ?? 'Lihat semua' }}
        </a>
      </div>
    </div>

    <div class="row g-4 mt-2">
      @forelse($latestNews as $i => $n)
        <div class="col-12 col-md-4">
          <article class="card p-4 reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @endif">
            <div class="mb-3 overflow-hidden rounded-4 border bg-white bg-opacity-75">
              <div class="ratio ratio-16x9 d-flex align-items-center justify-content-center small text-muted fw-bold">
                @if(!empty($n->cover_url))
                  <img src="{{ $n->cover_url }}" alt="{{ $n->title }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                  Cover (opsional)
                @endif
              </div>
            </div>

            <div class="small text-muted fw-bold">
              {{ $n->published_at?->translatedFormat('d M Y') ?? $n->created_at?->translatedFormat('d M Y') ?? '' }}
            </div>

            <h3 class="mt-2 h5 fw-bold" style="letter-spacing:-0.01em;">
              {{ $n->title ?? '' }}
            </h3>

            <p class="mt-2 small text-muted mb-0" style="line-height:1.8;">
              {{ $n->excerpt ?? '' }}
            </p>

            <div class="mt-4">
              <a href="{{ route('profil.berita.show', ['slug'=>$n->slug]) }}" class="small fw-bold link">Lihat detail →</a>
            </div>
          </article>
        </div>
      @empty
        @foreach([1,2,3] as $i)
          <div class="col-12 col-md-4">
            <article class="card p-4 reveal @if($i===2) delay-100 @elseif($i===3) delay-200 @endif">
              <div class="mb-3 overflow-hidden rounded-4 border bg-white bg-opacity-75">
                <div class="ratio ratio-16x9 d-flex align-items-center justify-content-center small text-muted fw-bold">
                  Belum ada berita
                </div>
              </div>
              <div class="small text-muted fw-bold">{{ now()->translatedFormat('d M Y') }}</div>
              <h3 class="mt-2 h5 fw-bold">Contoh Berita</h3>
              <p class="mt-2 small text-muted mb-0" style="line-height:1.8;">
                Modul berita belum ada data. Aman—tidak error.
              </p>
            </article>
          </div>
        @endforeach
      @endforelse
    </div>
</section>

{{-- CTA --}}
<section class="section">
  <div class="container">
    <div class="card p-4 p-md-5 reveal">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-4">
        <div>
          <h3 class="h2 fw-bold mb-0" style="letter-spacing:-0.02em;">
            {{ $home->cta_title ?? 'Siap mulai promosi sekarang?' }}
          </h3>
          <p class="mt-2 text-muted mb-0" style="line-height:1.8;">
            {{ $home->cta_desc ?? '' }}
          </p>
        </div>

        <div class="d-flex flex-wrap gap-3">
          <a href="{{ $home->waLink() }}" target="_blank" class="btn-primary">
            {{ $home->cta_wa_label ?? 'WhatsApp' }}
          </a>
          <a href="{{ $home->cta_contact_route ? route($home->cta_contact_route) : route('profil.kontak') }}" class="btn-outline">
            {{ $home->cta_contact_label ?? 'Kontak' }}
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
