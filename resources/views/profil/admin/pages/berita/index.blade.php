{{-- resources/views/profil/admin/pages/berita/index.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Berita')
@section('page_title', 'Berita')

@section('content')
@php
  /** @var \Illuminate\Pagination\LengthAwarePaginator $rows */
  $type = $type ?? request('type');
  $q    = $q ?? trim(request('q',''));
@endphp

<style>
  .rg-page-wrap{ max-width: 1200px; margin: 0 auto; }
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
    padding: 14px 18px;
    border-bottom:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.65);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .rg-card-shell .rg-card-body{ padding: 18px; }

  .rg-actions .btn{ font-weight:900; border-radius: 12px; padding: .6rem 1rem; }

  .rg-filters{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    margin-bottom: 14px;
  }
  .rg-filters .form-select,
  .rg-filters .form-control{
    border-radius: 12px;
    font-weight: 800;
  }
  .rg-filters .btn{ border-radius: 12px; font-weight: 900; }

  .rg-table{
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    border:1px solid rgba(226,232,240,.95);
    border-radius: 16px;
  }
  .rg-table th, .rg-table td{
    padding: 12px 12px;
    vertical-align: top;
    border-bottom:1px solid rgba(226,232,240,.95);
  }
  .rg-table thead th{
    background: rgba(248,250,252,.9);
    font-size: .86rem;
    color:#334155;
    font-weight: 900;
    letter-spacing:.01em;
  }
  .rg-table tbody tr:last-child td{ border-bottom: 0; }

  .rg-badge{
    display:inline-flex; align-items:center; gap:8px;
    padding: 6px 10px;
    border-radius: 999px;
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    font-weight: 900;
    font-size:.82rem;
    color:#0f172a;
    white-space:nowrap;
  }
  .rg-dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }
  .dot-news{ background: var(--rg-red); }
  .dot-edu{ background: var(--rg-blue); }

  .rg-sub{
    color:#64748b; font-size:.86rem; line-height:1.5;
  }

  .rg-row-actions{
    display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;
  }
  .rg-row-actions .btn{
    border-radius: 12px;
    font-weight: 900;
    padding: .45rem .8rem;
    white-space:nowrap;
  }

  .rg-cover{
    width: 92px;
    height: 56px;
    border-radius: 12px;
    border:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.6);
    overflow:hidden;
    display:flex; align-items:center; justify-content:center;
    font-size:.75rem;
    color:#64748b;
    font-weight: 900;
  }
  .rg-cover img{ width:100%; height:100%; object-fit:cover; display:block; }

  @media (max-width: 991.98px){
    .rg-row-actions{ justify-content:flex-start; }
  }
</style>

<div class="rg-page-wrap">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold mb-2">Ada error:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rg-topbar">
    <div>
      <div class="t">Berita</div>
      <div class="s">
        Kelola konten <b>Berita</b> dan <b>Edukasi</b>. Data ini juga dipakai untuk menampilkan <b>3 berita terbaru</b> di Beranda.
      </div>
    </div>

    <div class="rg-actions d-flex gap-2">
      <a href="{{ route('profil.admin.berita.create') }}" class="btn btn-dark">+ Tambah</a>
    </div>
  </div>

  <div class="rg-card-shell">
    <div class="rg-card-head">
      <div class="fw-bold">Daftar Konten</div>
      <div class="text-muted small">Total: <b>{{ $rows->total() }}</b></div>
    </div>

    <div class="rg-card-body">
      <form class="rg-filters" method="GET" action="{{ route('profil.admin.berita.index') }}">
        <select name="type" class="form-select" style="max-width:220px;">
          <option value="">Semua tipe</option>
          <option value="news" @selected(($type ?? '')==='news')>Berita</option>
          <option value="education" @selected(($type ?? '')==='education')>Edukasi</option>
        </select>

        <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Cari judul / slug / ringkasan..." style="max-width:360px;">

        <button class="btn btn-dark" type="submit">Filter</button>
        <a href="{{ route('profil.admin.berita.index') }}" class="btn btn-light border">Reset</a>
      </form>

      <div class="table-responsive">
        <table class="rg-table">
          <thead>
            <tr>
              <th style="width:120px;">Cover</th>
              <th>Judul</th>
              <th style="width:160px;">Tipe</th>
              <th style="width:170px;">Publish</th>
              <th style="width:210px; text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $r)
              <tr>
                <td>
                  <div class="rg-cover">
                    @if(!empty($r->cover_url))
                      <img src="{{ $r->cover_url }}" alt="{{ $r->title }}">
                    @else
                      No Cover
                    @endif
                  </div>
                </td>

                <td>
                  <div style="font-weight:900; color:#0f172a; line-height:1.35;">
                    {{ $r->title }}
                  </div>
                  <div class="rg-sub mt-1">
                    <span class="text-muted">Slug:</span> <code>{{ $r->slug }}</code>
                  </div>
                  @if(!empty($r->excerpt))
                    <div class="rg-sub mt-1">{{ $r->excerpt }}</div>
                  @endif
                </td>

                <td>
                  <span class="rg-badge">
                    <span class="rg-dot {{ ($r->type==='education') ? 'dot-edu' : 'dot-news' }}"></span>
                    {{ ($r->type==='education') ? 'Edukasi' : 'Berita' }}
                    @if(!empty($r->category_label))
                      • {{ $r->category_label }}
                    @endif
                  </span>

                  <div class="rg-sub mt-2">
                    Status:
                    @if($r->is_published)
                      <span class="fw-bold" style="color:#16a34a;">Published</span>
                    @else
                      <span class="fw-bold" style="color:#f59e0b;">Draft</span>
                    @endif
                  </div>
                </td>

                <td>
                  <div style="font-weight:900; color:#0f172a;">
                    {{ $r->published_at?->translatedFormat('d M Y H:i') ?? '-' }}
                  </div>
                  <div class="rg-sub mt-1">
                    Dibuat: {{ $r->created_at?->translatedFormat('d M Y') }}
                  </div>
                </td>

                <td style="text-align:right;">
                  <div class="rg-row-actions">
                    <a href="{{ route('profil.berita.show', ['slug'=>$r->slug]) }}" target="_blank" class="btn btn-light border">Preview</a>
                    <a href="{{ route('profil.admin.berita.edit', ['beritum'=>$r->id]) }}" class="btn btn-dark">Edit</a>
                    <form method="POST" action="{{ route('profil.admin.berita.destroy', ['beritum'=>$r->id]) }}" onsubmit="return confirm('Hapus konten ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger" type="submit">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 22px;">
                  Belum ada konten. Klik <b>+ Tambah</b> untuk membuat berita.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $rows->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
