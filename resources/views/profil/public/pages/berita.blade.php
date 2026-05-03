{{-- resources/views/profil/public/pages/berita.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Berita & Edukasi — Restu Guru Promosindo')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
    /** @var \App\Models\PBeritaPage $page */
    use Illuminate\Support\Str;

    $type = $type ?? request('type', 'news');
    $q = $q ?? trim(request('q', ''));

    $accentFor = function($row){
        if(($row->type ?? 'news') === 'news') return 'rg-accent-red';
        $cat = $row->category_label ?? '';
        if($cat === 'Desain') return 'rg-accent-yellow';
        return 'rg-accent-blue';
    };
@endphp

<section class="section section-soft rg-news-hero position-relative">
    <div class="rg-news-hero-bg pe-none"></div>

    <div class="rg-wrap position-relative">
        <div class="row g-4 align-items-end">
            <div class="col-12 col-lg-8 reveal">
                <div class="rg-news-chip">
                    <span class="dot"></span>
                    <span class="t">{{ $page->hero_chip }}</span>
                </div>

                <h1 class="mt-3 font-hero rg-news-title">
                    <span style="color:var(--rg-blue)">{{ $page->hero_title_1 }}</span>
                    <span style="color:var(--rg-yellow)"> {{ $page->hero_title_2 }}</span>
                    <span style="color:var(--rg-red)"> {{ $page->hero_title_3 }}</span>
                </h1>

                <p class="mt-2 text-muted rg-news-lead">
                    {{ $page->hero_lead }}
                </p>
            </div>

            <div class="col-12 col-lg-4 reveal delay-200">
                <div class="rg-news-search">
                    <form method="GET" action="{{ route('profil.berita') }}" class="rg-news-search-form">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $page->search_placeholder }}" class="rg-news-input">
                        <button class="rg-news-btn" type="submit">{{ $page->search_button }}</button>
                    </form>

                    <div class="rg-news-tabs">
                        <a href="{{ route('profil.berita', ['type'=>'news', 'q'=>$q]) }}"
                           class="rg-news-tab {{ $type==='news' ? 'is-active' : '' }}">
                            {{ $page->tab_news }}
                        </a>
                        <a href="{{ route('profil.berita', ['type'=>'education', 'q'=>$q]) }}"
                           class="rg-news-tab {{ $type==='education' ? 'is-active' : '' }}">
                            {{ $page->tab_edu }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-4 reveal delay-200">
            <div class="col-12 col-md-4">
                <div class="rg-news-stat rg-accent-blue">
                    <div class="k">{{ $page->stat1_k }}</div>
                    <div class="v">{{ $page->stat1_v }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="rg-news-stat rg-accent-red">
                    <div class="k">{{ $page->stat2_k }}</div>
                    <div class="v">{{ $page->stat2_v }}</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="rg-news-stat rg-accent-yellow">
                    <div class="k">{{ $page->stat3_k }}</div>
                    <div class="v">{{ $page->stat3_v }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="rg-wrap">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-md-between gap-3 reveal">
            <div>
                <h2 class="fw-bold rg-section-title mb-0">
                    {{ $type === 'education' ? $page->edu_heading : $page->news_heading }}
                </h2>
                <p class="mt-2 text-muted mb-0 rg-max-2xl">
                    {{ $type === 'education' ? $page->edu_desc : $page->news_desc }}
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('profil.kontak') }}" class="btn-outline">{{ $page->btn_kontak }}</a>
                <a href="{{ route('profil.layanan') }}" class="btn-primary">{{ $page->btn_layanan }}</a>
            </div>
        </div>

        <div class="row g-4 mt-3">
            @forelse($rows as $i => $row)
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="rg-post-card reveal @if($i===1) delay-100 @elseif($i===2) delay-200 @elseif($i===3) delay-300 @endif {{ $accentFor($row) }}">
                        <div class="rg-post-media">
                            <div class="rg-post-thumb">
                                @if($row->cover_url)
                                    <img src="{{ $row->cover_url }}" alt="{{ $row->title }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div class="rg-post-thumb-inner">
                                        <span class="small fw-bold text-muted">
                                            {{ $row->type==='education' ? $page->tab_edu : $page->tab_news }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rg-post-body">
                            <div class="rg-post-meta">
                                <span class="rg-post-badge">
                                    {{ $row->type==='education' ? $page->tab_edu : $page->tab_news }}
                                    @if(!empty($row->category_label)) • {{ $row->category_label }} @endif
                                </span>
                                <span class="rg-post-date">{{ $row->card_date }}</span>
                            </div>

                            <h3 class="rg-post-title">{{ $row->title }}</h3>
                            <p class="rg-post-excerpt text-muted">{{ $row->excerpt }}</p>

                            <div class="mt-3">
                                <a href="{{ route('profil.berita.show', ['slug'=>$row->slug]) }}" class="rg-post-link">
                                    Baca selengkapnya →
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-4 p-md-5 text-center reveal">
                        <div class="fw-bold">Tidak ada konten ditemukan.</div>
                        <div class="text-muted mt-2">Coba ganti kata kunci atau pindah tab.</div>
                    </div>
                </div>
            @endforelse
        </div>

        @if(method_exists($rows, 'links'))
            <div class="mt-4">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</section>

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
                    <a href="{{ $waLink ?? 'https://wa.me/6281234567890' }}" target="_blank" class="btn-primary">{{ $page->cta_btn_wa }}</a>
                    <a href="{{ route('profil.kontak') }}" class="btn-outline">{{ $page->cta_btn_kontak }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
