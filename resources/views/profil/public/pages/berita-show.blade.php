{{-- resources/views/profil/public/pages/berita-show.blade.php --}}
@extends('profil.public.layouts.app')

@section('title', 'Detail — Berita & Edukasi — Restu Guru Promosindo')

@section('content')
@php
    /** @var \App\Models\PBerita $post */
    /** @var \App\Models\PBeritaPage $page */
    $label = ($post->type ?? 'news') === 'education' ? ($page->tab_edu ?? 'Edukasi') : ($page->tab_news ?? 'Berita');
@endphp

<section class="section section-soft rg-article-hero position-relative">
    <div class="rg-news-hero-bg pe-none"></div>

    <div class="rg-wrap position-relative">
        <div class="rg-article-top reveal">
            <a href="{{ route('profil.berita', ['type' => ($post->type ?? 'news')]) }}" class="rg-back-link">
                ← Kembali
            </a>

            <div class="rg-article-chip">
                <span class="dot"></span>
                <span class="t">{{ $label }}@if(!empty($post->category_label)) • {{ $post->category_label }} @endif</span>
            </div>

            <h1 class="mt-3 rg-article-title">{{ $post->title }}</h1>

            <div class="rg-article-meta text-muted">
                <span>{{ $post->published_at?->translatedFormat('d M Y') ?? $post->created_at?->translatedFormat('d M Y') }}</span>
                <span class="sep">•</span>
                <span>Restu Guru Promosindo</span>
            </div>

            <p class="mt-3 rg-article-excerpt text-muted">{{ $post->excerpt }}</p>
        </div>

        <div class="rg-article-cover reveal delay-200">
            <div class="rg-article-cover-inner">
                @if($post->cover_url)
                    <img src="{{ $post->cover_url }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover;border-radius:18px;">
                @else
                    <span class="small fw-bold text-muted">{{ $label }}</span>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="rg-wrap">
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-lg-8">
                <article class="rg-article card p-4 p-md-5 reveal">
                    <div class="rg-article-content">
                        {!! $post->content !!}
                    </div>

                    <hr class="my-4">

                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                        <div class="text-muted small">
                            {{ $page->article_help_text ?? 'Punya pertanyaan? Konsultasi gratis.' }}
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ $waLink ?? 'https://wa.me/6281234567890' }}" target="_blank" class="btn-primary">{{ $page->cta_btn_wa ?? 'WhatsApp' }}</a>
                            <a href="{{ route('profil.kontak') }}" class="btn-outline">{{ $page->cta_btn_kontak ?? 'Kontak' }}</a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
