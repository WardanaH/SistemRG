{{-- resources/views/profil/admin/pages/berita/halaman.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Halaman Berita')
@section('page_title', 'Edit Halaman Berita')

@section('content')
@php
  /** @var \App\Models\PBeritaPage $page */
  $ov = fn($key, $default = '') => old($key, data_get($page, $key, $default));
@endphp

<style>
  .rg-page-wrap{ max-width: 1100px; margin: 0 auto; }
  .rg-topbar{
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
    margin-bottom: 14px;
  }
  .rg-topbar .t{ font-weight:900; font-size: 1.35rem; letter-spacing:-.01em; }
  .rg-topbar .s{ color:#64748b; font-size:.92rem; line-height:1.6; max-width: 56rem; }

  .rg-card-shell{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 18px;
    background:#fff;
    box-shadow: 0 10px 26px rgba(15,23,42,.06);
    overflow:hidden;
  }
  .rg-card-shell .rg-card-head{
    padding: 16px 18px;
    border-bottom:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.65);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .rg-card-shell .rg-card-body{ padding: 18px; }

  .rg-actions .btn{ font-weight:900; border-radius: 12px; padding: .6rem 1rem; }

  .rg-section-card{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 16px;
    padding: 16px;
    background:#fff;
  }
  .rg-section-title{ font-weight:900; margin-bottom:2px; }
  .rg-section-sub{ color:#64748b; font-size:.9rem; line-height:1.6; margin-bottom: 14px; }

  .form-control, .form-select, textarea{
    border-radius: 12px;
    font-weight: 800;
  }

  .rg-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 14px;
  }
  @media(min-width: 992px){
    .rg-grid{ grid-template-columns: 1fr 1fr; }
  }
</style>

<div class="rg-page-wrap">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-2">Ada error validasi:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('profil.admin.berita.halaman.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Halaman Berita</div>
        <div class="s">
          Ini untuk mengubah <b>Hero, Search, Tab, Stat, Heading, CTA</b> halaman Berita/Edukasi.
          CRUD tambah/edit Berita & Edukasi tetap ada di menu <b>Berita</b>.
        </div>
      </div>

      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.berita') }}" target="_blank" class="btn btn-light border">Preview Public</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Setting Halaman</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">
        <div class="rg-grid">

          {{-- HERO --}}
          <div class="rg-section-card">
            <div class="rg-section-title">Hero</div>
            <div class="rg-section-sub">Teks bagian atas (chip, judul warna 3 bagian, deskripsi).</div>

            <label class="form-label fw-bold">Chip</label>
            <input class="form-control" name="hero_chip" value="{{ $ov('hero_chip') }}">

            <div class="row g-3 mt-1">
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Title 1</label>
                <input class="form-control" name="hero_title_1" value="{{ $ov('hero_title_1') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Title 2</label>
                <input class="form-control" name="hero_title_2" value="{{ $ov('hero_title_2') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Title 3</label>
                <input class="form-control" name="hero_title_3" value="{{ $ov('hero_title_3') }}">
              </div>
            </div>

            <label class="form-label fw-bold mt-3">Lead</label>
            <textarea class="form-control" name="hero_lead" rows="3">{{ $ov('hero_lead') }}</textarea>
          </div>

          {{-- SEARCH + TABS --}}
          <div class="rg-section-card">
            <div class="rg-section-title">Search & Tab</div>
            <div class="rg-section-sub">Placeholder, tombol cari, label tab.</div>

            <label class="form-label fw-bold">Placeholder</label>
            <input class="form-control" name="search_placeholder" value="{{ $ov('search_placeholder') }}">

            <div class="row g-3 mt-1">
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Tombol Cari</label>
                <input class="form-control" name="search_button" value="{{ $ov('search_button') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Tab Berita</label>
                <input class="form-control" name="tab_news" value="{{ $ov('tab_news') }}">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label fw-bold">Tab Edukasi</label>
                <input class="form-control" name="tab_edu" value="{{ $ov('tab_edu') }}">
              </div>
            </div>
          </div>

          {{-- STAT CARDS --}}
          <div class="rg-section-card">
            <div class="rg-section-title">3 Stat Card</div>
            <div class="rg-section-sub">Teks untuk 3 kotak kecil (Konten/Topik/Tujuan).</div>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 1 - Key</label>
                <input class="form-control" name="stat1_k" value="{{ $ov('stat1_k') }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 1 - Value</label>
                <input class="form-control" name="stat1_v" value="{{ $ov('stat1_v') }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 2 - Key</label>
                <input class="form-control" name="stat2_k" value="{{ $ov('stat2_k') }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 2 - Value</label>
                <input class="form-control" name="stat2_v" value="{{ $ov('stat2_v') }}">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 3 - Key</label>
                <input class="form-control" name="stat3_k" value="{{ $ov('stat3_k') }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Stat 3 - Value</label>
                <input class="form-control" name="stat3_v" value="{{ $ov('stat3_v') }}">
              </div>
            </div>
          </div>

          {{-- SECTION + BUTTONS --}}
          <div class="rg-section-card">
            <div class="rg-section-title">Heading List & Tombol</div>
            <div class="rg-section-sub">Judul/deskripsi untuk tab Berita & Edukasi + label tombol kanan.</div>

            <label class="form-label fw-bold">Berita - Heading</label>
            <input class="form-control" name="news_heading" value="{{ $ov('news_heading') }}">

            <label class="form-label fw-bold mt-2">Berita - Deskripsi</label>
            <textarea class="form-control" name="news_desc" rows="3">{{ $ov('news_desc') }}</textarea>

            <label class="form-label fw-bold mt-3">Edukasi - Heading</label>
            <input class="form-control" name="edu_heading" value="{{ $ov('edu_heading') }}">

            <label class="form-label fw-bold mt-2">Edukasi - Deskripsi</label>
            <textarea class="form-control" name="edu_desc" rows="3">{{ $ov('edu_desc') }}</textarea>

            <div class="row g-3 mt-2">
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Label Tombol Kontak</label>
                <input class="form-control" name="btn_kontak" value="{{ $ov('btn_kontak') }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Label Tombol Layanan</label>
                <input class="form-control" name="btn_layanan" value="{{ $ov('btn_layanan') }}">
              </div>
            </div>
          </div>

          {{-- CTA --}}
          <div class="rg-section-card">
            <div class="rg-section-title">CTA Bawah</div>
            <div class="rg-section-sub">Kartu CTA bawah + label tombol.</div>

            <label class="form-label fw-bold">Judul CTA</label>
            <input class="form-control" name="cta_title" value="{{ $ov('cta_title') }}">

            <label class="form-label fw-bold mt-2">Deskripsi CTA</label>
            <textarea class="form-control" name="cta_desc" rows="3">{{ $ov('cta_desc') }}</textarea>

            <div class="row g-3 mt-1">
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Label Tombol WhatsApp</label>
                <input class="form-control" name="cta_btn_wa" value="{{ $ov('cta_btn_wa') }}">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label fw-bold">Label Tombol Kontak</label>
                <input class="form-control" name="cta_btn_kontak" value="{{ $ov('cta_btn_kontak') }}">
              </div>
            </div>

            <label class="form-label fw-bold mt-3">Teks Bantuan di Detail Artikel</label>
            <input class="form-control" name="article_help_text" value="{{ $ov('article_help_text') }}">
          </div>

        </div>
      </div>
    </div>
  </form>
</div>
@endsection
