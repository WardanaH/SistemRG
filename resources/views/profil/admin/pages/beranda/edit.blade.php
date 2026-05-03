{{-- resources/views/profil/admin/pages/beranda/edit.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Beranda')
@section('page_title', 'Edit Beranda')

@section('content')
@php
  /** @var \App\Models\PBeranda $home */

  $routes = $routes ?? [
      ['label' => '— Pilih Link —', 'value' => ''],
      ['label' => 'Beranda', 'value' => 'profil.beranda'],
      ['label' => 'Layanan', 'value' => 'profil.layanan'],
      ['label' => 'Tentang', 'value' => 'profil.tentang'],
      ['label' => 'Berita',  'value' => 'profil.berita'],
      ['label' => 'Kontak',  'value' => 'profil.kontak'],
  ];

  // pilihan warna sesuai request: brand + black + custom
  $colorModes = [
      ['label' => 'Brand Blue',   'value' => 'var(--rg-blue)'],
      ['label' => 'Brand Yellow', 'value' => 'var(--rg-yellow)'],
      ['label' => 'Brand Red',    'value' => 'var(--rg-red)'],
      ['label' => 'Black',        'value' => '#000000'],
      ['label' => 'Custom Hex',   'value' => 'custom'],
  ];

  $ov = fn($key, $default = '') => old($key, $default);

  $parts         = old('hero_title_parts', $home->hero_title_parts ?? []);
  $heroBranches  = old('hero_branches', $home->hero_branches ?? []);
  $aboutBranches = old('about_branches', $home->about_branches ?? []);
  $heroLabels    = old('hero_labels', $home->hero_labels ?? []);
  $heroCats      = old('hero_cats', $home->hero_cats ?? []);
  $mainCards     = old('main_cards', $home->main_cards ?? []);
  $whyCards      = old('why_cards', $home->why_cards ?? []);
  $colors        = old('colors', $home->colors ?? []);

  if (!is_array($parts)) $parts = [];
  if (!is_array($heroBranches)) $heroBranches = [];
  if (!is_array($aboutBranches)) $aboutBranches = [];
  if (!is_array($heroLabels)) $heroLabels = [];
  if (!is_array($heroCats)) $heroCats = [];
  if (!is_array($mainCards)) $mainCards = [];
  if (!is_array($whyCards)) $whyCards = [];
  if (!is_array($colors)) $colors = [];

  $ensure3 = function(array $arr, array $defaults) {
      for ($i=0; $i<3; $i++) {
          if (!isset($arr[$i]) || !is_array($arr[$i])) $arr[$i] = $defaults[$i] ?? [];
      }
      return $arr;
  };

  $heroLabels = $ensure3($heroLabels, [
      ['text'=>'Outdoor','color'=>'var(--rg-blue)'],
      ['text'=>'Indoor','color'=>'var(--rg-red)'],
      ['text'=>'Multi','color'=>'var(--rg-yellow)'],
  ]);

  $heroCats = $ensure3($heroCats, [
      ['text'=>'Outdoor','color'=>'var(--rg-blue)'],
      ['text'=>'Indoor','color'=>'var(--rg-red)'],
      ['text'=>'Multi','color'=>'var(--rg-yellow)'],
  ]);

  $mainCards = $ensure3($mainCards, [
      ['title'=>'Outdoor Advertising','desc'=>'Baliho, billboard, spanduk, neonbox, branding.','dot'=>'var(--rg-blue)','route'=>'profil.layanan','image'=>null],
      ['title'=>'Indoor Printing','desc'=>'Poster, banner, backdrop, display indoor.','dot'=>'var(--rg-red)','route'=>'profil.layanan','image'=>null],
      ['title'=>'Multi / Sticker','desc'=>'Sticker vinyl, cutting, label produk, dll.','dot'=>'var(--rg-yellow)','route'=>'profil.layanan','image'=>null],
  ]);

  $whyCards = $ensure3($whyCards, [
      ['title'=>'Kualitas Produksi','desc'=>'Material terkurasi, finishing rapi, QC sebelum kirim.','accent'=>'var(--rg-blue)'],
      ['title'=>'Cepat & Tepat','desc'=>'Timeline jelas, komunikasi cepat, pengerjaan efisien.','accent'=>'var(--rg-red)'],
      ['title'=>'Support Tim','desc'=>'Dibantu dari konsep sampai file siap produksi.','accent'=>'var(--rg-yellow)'],
  ]);

  if (count($parts) === 0) {
      $parts = [
          ['text'=>'Restu','color'=>'var(--rg-blue)'],
          ['text'=>'Guru','color'=>'var(--rg-yellow)'],
          ['text'=>'Promosindo','color'=>'var(--rg-red)'],
      ];
  }

  /**
   * Color field (rapih)
   * - dropdown: brand+black+custom
   * - swatch selalu tampil
   * - picker+hex tampil hanya saat custom
   * - value tersimpan di hidden input (string)
   */
  $colorField = function($name, $value, $id, $label = null) use ($colorModes) {
      $value = $value ?? '';
      $safeName = e($name);
      $safeId   = e($id);
      $safeVal  = e($value);
      $labelHtml = $label ? '<label class="form-label fw-bold mb-1">'.$label.'</label>' : '';

      $options = '';
      foreach ($colorModes as $m) {
          $options .= '<option value="'.e($m['value']).'">'.e($m['label']).'</option>';
      }

      return <<<HTML
      <div class="rg-color rg-color--compact" data-color-field id="cf-{$safeId}">
        {$labelHtml}
        <input type="hidden" name="{$safeName}" value="{$safeVal}" data-color-hidden>

        <div class="rg-color__row">
          <select class="form-select rg-color__select" data-color-mode>
            {$options}
          </select>

          <div class="rg-color__swatch-wrap">
            <input type="color" class="form-control form-control-color rg-color__picker" value="#000000" data-color-picker aria-label="Color picker">
            <span class="rg-color__swatch" data-color-swatch title="Preview"></span>
          </div>

          <input type="text" class="form-control rg-color__hex" placeholder="#RRGGBB" value="" data-color-hex>
        </div>
      </div>
      HTML;
  };
@endphp

<style>
  /* ====== RAPIN UI EDIT ====== */
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
    padding: 16px 18px;
    border-bottom:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.65);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .rg-card-shell .rg-card-body{ padding: 18px; }

  .rg-tabs.nav-pills .nav-link{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 999px;
    padding: .55rem .95rem;
    font-weight: 800;
    color:#0f172a;
    background: #fff;
    box-shadow: 0 8px 18px rgba(15,23,42,.04);
  }
  .rg-tabs.nav-pills .nav-link.active{
    background:#0f172a;
    color:#fff;
    border-color:#0f172a;
  }

  .rg-section-card{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 16px;
    padding: 16px;
    background:#fff;
  }
  .rg-section-title{ font-weight:900; margin-bottom:2px; }
  .rg-section-sub{ color:#64748b; font-size:.9rem; line-height:1.6; margin-bottom: 14px; }

  /* repeat row rapih */
  .rg-repeat-row{
    display:flex; gap:12px; align-items:flex-start;
    padding: 12px;
    border:1px solid rgba(226,232,240,.95);
    border-radius: 14px;
    background: rgba(248,250,252,.55);
    margin-bottom: 10px;
  }
  .rg-repeat-row .btn{ white-space:nowrap; border-radius: 12px; font-weight: 900; }
  @media (max-width: 991.98px){
    .rg-repeat-row{ flex-direction: column; }
    .rg-repeat-row .pt-4{ padding-top: 0 !important; }
  }

  /* ===== COLOR FIELD (RAPIH) ===== */
  .rg-color__row{
    display:flex;
    gap:10px;
    align-items:center;
    flex-wrap:wrap;
  }
  .rg-color__select{
    width: 170px;
    min-width: 170px;
    font-weight: 800;
  }
  .rg-color__swatch-wrap{
    position: relative;
    width: 54px;
    height: 40px;
    flex: 0 0 54px;
  }
  .rg-color__picker{
    width: 54px;
    height: 40px;
    padding: .2rem;
    opacity: 0;
    position:absolute;
    inset:0;
    cursor:pointer;
  }
  .rg-color__swatch{
    display:block;
    width: 54px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid rgba(226,232,240,.95);
    box-shadow: inset 0 0 0 1px rgba(15,23,42,.05);
    background: #000;
  }
  .rg-color__hex{
    width: 160px;
    min-width: 160px;
    font-weight: 800;
    letter-spacing: .02em;
  }
  .rg-color--compact .rg-color__hint{ display:none; }
  @media (max-width: 575.98px){
    .rg-color__select{ width: 100%; min-width: 0; }
    .rg-color__hex{ width: 100%; min-width: 0; }
  }

  .rg-actions .btn{ font-weight:900; border-radius: 12px; padding: .6rem 1rem; }
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

  <form action="{{ route('profil.admin.beranda.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Beranda</div>
        <div class="s">Warna: pilih <b>Brand/Black</b> atau <b>Custom Hex</b> (picker + hex muncul hanya saat Custom).</div>
      </div>
      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.admin.beranda.edit') }}" class="btn btn-light border">Reset</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Pengaturan Beranda</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">
        {{-- Tabs --}}
        <ul class="nav nav-pills rg-tabs gap-2" id="homeEditTabs" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-hero" type="button" role="tab">Hero</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-layanan" type="button" role="tab">Layanan Utama</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-why" type="button" role="tab">Kenapa Memilih Kami</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-about" type="button" role="tab">Tentang & Cabang</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-news" type="button" role="tab">Berita Terbaru</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-cta" type="button" role="tab">CTA Bottom</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-colors" type="button" role="tab">Warna & Aksen</button></li>
        </ul>

        <div class="tab-content pt-4">

          {{-- ================= HERO ================= --}}
          <div class="tab-pane fade show active" id="pane-hero" role="tabpanel" tabindex="0">
            <div class="row g-3">

              {{-- HERO LEFT --}}
              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">Hero (Left)</div>
                  <div class="rg-section-sub">Badge, judul berwarna (repeatable), deskripsi, tombol, dan cabang.</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Badge Label</label>
                      <input name="hero_badge_label" class="form-control" value="{{ $ov('hero_badge_label', $home->hero_badge_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      {!! $colorField('hero_badge_dot', $ov('hero_badge_dot', $home->hero_badge_dot), 'hero_badge_dot', 'Badge Dot Color') !!}
                    </div>
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Judul Berwarna (Repeatable)</div>
                  <div class="rg-section-sub">Maks 6 potongan. Tiap potongan: teks + warna.</div>

                  <div id="heroTitleParts" data-max="6">
                    @foreach($parts as $i => $p)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Teks</label>
                          <input type="text" class="form-control" name="hero_title_parts[{{ $i }}][text]" value="{{ $p['text'] ?? '' }}" placeholder="Mis: Restu">
                        </div>

                        <div style="min-width: 320px;">
                          {!! $colorField("hero_title_parts[$i][color]", ($p['color'] ?? 'var(--rg-blue)'), "hero_title_part_$i", 'Warna') !!}
                        </div>

                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="text-muted small">Contoh: tambah “CV” di depan.</div>
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddHeroTitlePart">+ Tambah Potongan</button>
                  </div>

                  <hr class="my-4">

                  <label class="form-label fw-bold">Deskripsi Hero</label>
                  <textarea name="hero_desc" class="form-control" rows="3">{{ $ov('hero_desc', $home->hero_desc) }}</textarea>

                  <hr class="my-4">

                  <div class="rg-section-title">Tombol Hero</div>

                  <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Label)</label>
                      <input name="hero_btn1_label" class="form-control" value="{{ $ov('hero_btn1_label', $home->hero_btn1_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Link)</label>
                      <select name="hero_btn1_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_btn1_route', $home->hero_btn1_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Label)</label>
                      <input name="hero_btn2_label" class="form-control" value="{{ $ov('hero_btn2_label', $home->hero_btn2_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Link)</label>
                      <select name="hero_btn2_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_btn2_route', $home->hero_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Cabang (Pill)</div>
                  <div class="rg-section-sub">Maks 8 cabang. 1 item = 1 pill.</div>

                  <div id="heroBranches" data-max="8">
                    @foreach($heroBranches as $i => $b)
                      <div class="rg-repeat-row">
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Cabang</label>
                          <input type="text" class="form-control" name="hero_branches[{{ $i }}]" value="{{ $b }}" placeholder="Nama cabang">
                        </div>
                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddBranch">+ Tambah Cabang</button>
                  </div>

                  {{-- HERO UPLOAD DIHAPUS TOTAL --}}
                </div>
              </div>

              {{-- HERO RIGHT --}}
              <div class="col-12 col-xl-5">
                <div class="rg-section-card">

                  <div class="rg-section-title">Label Kategori Bawah</div>
                  <div class="rg-section-sub">Maks 3 label. Teks + warna dot.</div>

                  <div class="row g-3">
                    @for($i=0; $i<3; $i++)
                      <div class="col-12">
                        <div class="rg-section-card" style="padding: 14px;">
                          <div class="row g-2">
                            <div class="col-12 col-md-6">
                              <label class="form-label fw-bold">Teks</label>
                              <input class="form-control" name="hero_labels[{{ $i }}][text]" value="{{ $heroLabels[$i]['text'] ?? '' }}">
                            </div>
                            <div class="col-12 col-md-6">
                              {!! $colorField("hero_labels[$i][color]", ($heroLabels[$i]['color'] ?? 'var(--rg-blue)'), "hero_label_$i", 'Dot Color') !!}
                            </div>
                          </div>
                        </div>
                      </div>
                    @endfor
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Hero (Right Panel)</div>
                  <div class="rg-section-sub">Judul, link “Detail →”, kategori grid, dan tombol tanya cepat.</div>

                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label fw-bold">Small Label</label>
                      <input name="hero_right_small_label" class="form-control" value="{{ $ov('hero_right_small_label', $home->hero_right_small_label) }}">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Judul Panel</label>
                      <input name="hero_right_title" class="form-control" value="{{ $ov('hero_right_title', $home->hero_right_title) }}">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Link “Detail →”</label>
                      <select name="hero_right_detail_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_right_detail_route', $home->hero_right_detail_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Kategori Grid (3 Box)</div>
                  <div class="rg-section-sub">Teks + warna box.</div>

                  <div class="row g-3">
                    @for($i=0; $i<3; $i++)
                      <div class="col-12">
                        <div class="rg-section-card" style="padding: 14px;">
                          <div class="row g-2">
                            <div class="col-12 col-md-6">
                              <label class="form-label fw-bold">Teks</label>
                              <input class="form-control" name="hero_cats[{{ $i }}][text]" value="{{ $heroCats[$i]['text'] ?? '' }}">
                            </div>
                            <div class="col-12 col-md-6">
                              {!! $colorField("hero_cats[$i][color]", ($heroCats[$i]['color'] ?? 'var(--rg-blue)'), "hero_cat_$i", 'Warna Box') !!}
                            </div>
                          </div>
                        </div>
                      </div>
                    @endfor
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Ask Box</div>

                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label fw-bold">Label Ask Box</label>
                      <input name="hero_ask_label" class="form-control" value="{{ $ov('hero_ask_label', $home->hero_ask_label) }}">
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol WA (Label)</label>
                      <input name="hero_ask_wa_label" class="form-control" value="{{ $ov('hero_ask_wa_label', $home->hero_ask_wa_label) }}">
                      <div class="form-text">Link WA pakai value dari tab CTA Bottom.</div>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol Kontak (Label)</label>
                      <input name="hero_ask_contact_label" class="form-control" value="{{ $ov('hero_ask_contact_label', $home->hero_ask_contact_label) }}">
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-bold">Tombol Kontak (Link)</label>
                      <select name="hero_ask_contact_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_ask_contact_route', $home->hero_ask_contact_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <hr class="my-4">
                  <div class="text-muted small" style="line-height:1.7;">
                    <b>Berita Terbaru</b> di beranda otomatis ambil 3 berita terbaru dari modul Berita.
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- ================= LAYANAN UTAMA ================= --}}
          <div class="tab-pane fade" id="pane-layanan" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Layanan Utama</div>
              <div class="rg-section-sub">3 kartu + gambar + dot color + link detail.</div>

              <div class="row g-3">
                @for($i=0; $i<3; $i++)
                  @php $it = $mainCards[$i] ?? []; @endphp
                  <div class="col-12 col-lg-4">
                    <div class="rg-section-card" style="padding: 14px;">
                      <div class="fw-bold mb-2">Kartu {{ $i+1 }}</div>

                      <label class="form-label fw-bold">Judul</label>
                      <input class="form-control" name="main_cards[{{ $i }}][title]" value="{{ $it['title'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Deskripsi</label>
                      <textarea class="form-control" name="main_cards[{{ $i }}][desc]" rows="3">{{ $it['desc'] ?? '' }}</textarea>

                      <div class="mt-3">
                        {!! $colorField("main_cards[$i][dot]", ($it['dot'] ?? 'var(--rg-blue)'), "main_dot_$i", 'Dot Color') !!}
                      </div>

                      <label class="form-label fw-bold mt-3">Link “Lihat detail →”</label>
                      <select class="form-select" name="main_cards[{{ $i }}][route]">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected(($it['route'] ?? '') === $r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>

                      <label class="form-label fw-bold mt-3">Gambar</label>
                      <input type="file" class="form-control" name="main_cards[{{ $i }}][image]" accept="image/*">

                      @if(!empty($it['image']))
                        <div class="small text-muted mt-2">Current: <code>{{ $it['image'] }}</code></div>
                        <img src="{{ asset('storage/'.$it['image']) }}" alt="main-{{ $i }}" style="width:100%;max-height:140px;object-fit:cover;border-radius:12px;border:1px solid rgba(0,0,0,.12);margin-top:8px;">
                      @endif
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          {{-- ================= WHY ================= --}}
          <div class="tab-pane fade" id="pane-why" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Kenapa Memilih Kami</div>
              <div class="rg-section-sub">Judul section + deskripsi + tombol, dan 3 kartu alasan.</div>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Judul Section</label>
                  <input name="why_title" class="form-control" value="{{ $ov('why_title', $home->why_title) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Deskripsi Section</label>
                  <input name="why_desc" class="form-control" value="{{ $ov('why_desc', $home->why_desc) }}">
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol 1 (Label)</label>
                  <input name="why_btn1_label" class="form-control" value="{{ $ov('why_btn1_label', $home->why_btn1_label) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol 1 (Link)</label>
                  <select name="why_btn1_route" class="form-select">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('why_btn1_route', $home->why_btn1_route)===$r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol 2 (Label)</label>
                  <input name="why_btn2_label" class="form-control" value="{{ $ov('why_btn2_label', $home->why_btn2_label) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol 2 (Link)</label>
                  <select name="why_btn2_route" class="form-select">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('why_btn2_route', $home->why_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <hr class="my-4">

              <div class="row g-3">
                @for($i=0; $i<3; $i++)
                  @php $it = $whyCards[$i] ?? []; @endphp
                  <div class="col-12 col-lg-4">
                    <div class="rg-section-card" style="padding: 14px;">
                      <div class="fw-bold mb-2">Kartu {{ $i+1 }}</div>

                      <label class="form-label fw-bold">Judul</label>
                      <input class="form-control" name="why_cards[{{ $i }}][title]" value="{{ $it['title'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Deskripsi</label>
                      <textarea class="form-control" name="why_cards[{{ $i }}][desc]" rows="3">{{ $it['desc'] ?? '' }}</textarea>

                      <div class="mt-3">
                        {!! $colorField("why_cards[$i][accent]", ($it['accent'] ?? 'var(--rg-blue)'), "why_acc_$i", 'Accent Color') !!}
                      </div>
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          {{-- ================= ABOUT & CABANG ================= --}}
          <div class="tab-pane fade" id="pane-about" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">Tentang Singkat</div>

                  <label class="form-label fw-bold mt-3">Judul</label>
                  <input name="about_title" class="form-control" value="{{ $ov('about_title', $home->about_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <textarea name="about_desc" class="form-control" rows="4">{{ $ov('about_desc', $home->about_desc) }}</textarea>

                  <div class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Label)</label>
                      <input name="about_btn1_label" class="form-control" value="{{ $ov('about_btn1_label', $home->about_btn1_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Link)</label>
                      <select name="about_btn1_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('about_btn1_route', $home->about_btn1_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Label)</label>
                      <input name="about_btn2_label" class="form-control" value="{{ $ov('about_btn2_label', $home->about_btn2_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Link)</label>
                      <select name="about_btn2_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('about_btn2_route', $home->about_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Cakupan Cabang</div>
                  <div class="rg-section-sub">Maks 8 item.</div>

                  <div id="aboutBranches" data-max="8">
                    @foreach($aboutBranches as $i => $b)
                      <div class="rg-repeat-row">
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Cabang</label>
                          <input type="text" class="form-control" name="about_branches[{{ $i }}]" value="{{ $b }}">
                        </div>
                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddAboutBranch">+ Tambah Cabang</button>
                  </div>

                  <hr class="my-4">

                  <label class="form-label fw-bold">Teks kecil bawah (opsional)</label>
                  <textarea name="about_small_text" class="form-control" rows="2">{{ $ov('about_small_text', $home->about_small_text) }}</textarea>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= NEWS ================= --}}
          <div class="tab-pane fade" id="pane-news" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Berita Terbaru</div>
              <div class="rg-section-sub">Konten berita otomatis (3 terbaru). Yang diatur: judul, deskripsi, tombol.</div>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Judul Section</label>
                  <input name="news_title" class="form-control" value="{{ $ov('news_title', $home->news_title) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Deskripsi Section</label>
                  <input name="news_desc" class="form-control" value="{{ $ov('news_desc', $home->news_desc) }}">
                </div>

                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol (Label)</label>
                  <input name="news_btn_label" class="form-control" value="{{ $ov('news_btn_label', $home->news_btn_label) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Tombol (Link)</label>
                  <select name="news_btn_route" class="form-select">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('news_btn_route', $home->news_btn_route)===$r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= CTA ================= --}}
          <div class="tab-pane fade" id="pane-cta" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">CTA Bottom</div>
                  <div class="rg-section-sub">Judul, deskripsi, tombol WA + tombol kontak.</div>

                  <label class="form-label fw-bold">Judul</label>
                  <input name="cta_title" class="form-control" value="{{ $ov('cta_title', $home->cta_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <textarea name="cta_desc" class="form-control" rows="3">{{ $ov('cta_desc', $home->cta_desc) }}</textarea>

                  <div class="row g-3 mt-2">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol WA (Label)</label>
                      <input name="cta_wa_label" class="form-control" value="{{ $ov('cta_wa_label', $home->cta_wa_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol Kontak (Label)</label>
                      <input name="cta_contact_label" class="form-control" value="{{ $ov('cta_contact_label', $home->cta_contact_label) }}">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Tombol Kontak (Link)</label>
                      <select name="cta_contact_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('cta_contact_route', $home->cta_contact_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Kontak Manual</div>
                  <div class="rg-section-sub">Sesuai request: yang ditulis manual hanya IG dan WA.</div>

                  <label class="form-label fw-bold">Instagram URL</label>
                  <input name="ig_url" class="form-control" placeholder="https://instagram.com/username" value="{{ $ov('ig_url', $home->ig_url) }}">

                  <label class="form-label fw-bold mt-3">WhatsApp (nomor / link wa.me)</label>
                  <input name="wa_value" class="form-control" placeholder="62812xxxx atau https://wa.me/62xxxx" value="{{ $ov('wa_value', $home->wa_value) }}">

                  <div class="alert alert-warning mt-3 mb-0">
                    <div class="fw-bold">Info</div>
                    Nilai WA di sini dipakai untuk tombol WA di hero dan CTA.
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= COLORS ================= --}}
          <div class="tab-pane fade" id="pane-colors" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Warna & Aksen</div>
              <div class="rg-section-sub">Semua pakai dropdown brand/black/custom.</div>

              <div class="row g-3">
                @php
                  $colorFields = [
                    ['label'=>'Hero Blob Blue',          'key'=>'blob_blue',   'default'=>'var(--rg-blue)'],
                    ['label'=>'Hero Blob Red',           'key'=>'blob_red',    'default'=>'var(--rg-red)'],
                    ['label'=>'Hero Blob Yellow',        'key'=>'blob_yellow', 'default'=>'var(--rg-yellow)'],
                    ['label'=>'Soft Section Background', 'key'=>'soft_bg',     'default'=>'#f6f8fb'],
                    ['label'=>'Link Accent Color',       'key'=>'link_accent', 'default'=>'var(--rg-blue)'],
                    ['label'=>'Button Primary Color',    'key'=>'btn_primary', 'default'=>'var(--rg-red)'],
                  ];
                @endphp

                @foreach($colorFields as $cf)
                  <div class="col-12 col-lg-6">
                    <div class="rg-section-card" style="padding:14px;">
                      {!! $colorField(
                        "colors[{$cf['key']}]",
                        ($colors[$cf['key']] ?? $cf['default']),
                        "colors_{$cf['key']}",
                        $cf['label']
                      ) !!}
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="alert alert-info mt-3 mb-0">
                Warna ini dipakai di beranda public (blob, section soft, link accent, tombol primary).
              </div>
            </div>
          </div>

        </div>{{-- tab-content --}}
      </div>{{-- card-body --}}
    </div>{{-- card-shell --}}
</div>

<script>
(function(){
  // ===== repeat remove =====
  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-remove]');
    if(!btn) return;
    const row = btn.closest('[data-repeat-row], .rg-repeat-row');
    if(row) row.remove();
  });

  // ===== add title part =====
  const heroTitleWrap = document.getElementById('heroTitleParts');
  const btnAddTitle   = document.getElementById('btnAddHeroTitlePart');

  function addHeroTitlePart(){
    if(!heroTitleWrap) return;
    const max = parseInt(heroTitleWrap.dataset.max || '6', 10);
    const rows = heroTitleWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = document.createElement('div');
    div.className = 'rg-repeat-row';
    div.setAttribute('data-repeat-row','1');

    div.innerHTML = `
      <div class="flex-grow-1">
        <label class="form-label fw-bold mb-1">Teks</label>
        <input type="text" class="form-control" name="hero_title_parts[${idx}][text]" value="" placeholder="Mis: CV">
      </div>

      <div style="min-width: 320px;">
        <div class="rg-color rg-color--compact" data-color-field id="cf-hero_title_part_${idx}">
          <label class="form-label fw-bold mb-1">Warna</label>
          <input type="hidden" name="hero_title_parts[${idx}][color]" value="var(--rg-blue)" data-color-hidden>

          <div class="rg-color__row">
            <select class="form-select rg-color__select" data-color-mode>
              <option value="var(--rg-blue)">Brand Blue</option>
              <option value="var(--rg-yellow)">Brand Yellow</option>
              <option value="var(--rg-red)">Brand Red</option>
              <option value="#000000">Black</option>
              <option value="custom">Custom Hex</option>
            </select>

            <div class="rg-color__swatch-wrap">
              <input type="color" class="form-control form-control-color rg-color__picker" value="#2caae1" data-color-picker aria-label="Color picker">
              <span class="rg-color__swatch" data-color-swatch title="Preview"></span>
            </div>

            <input type="text" class="form-control rg-color__hex" placeholder="#RRGGBB" value="#2caae1" data-color-hex>
          </div>
        </div>
      </div>

      <div>
        <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
        <button type="button" class="btn btn-light border" data-remove>Hapus</button>
      </div>
    `;

    heroTitleWrap.appendChild(div);
    initColorFields(div);
  }

  if(btnAddTitle) btnAddTitle.addEventListener('click', addHeroTitlePart);

  // ===== add branches =====
  function addBranch(targetId, inputName){
    const wrap = document.getElementById(targetId);
    if(!wrap) return;

    const max = parseInt(wrap.dataset.max || '8', 10);
    const rows = wrap.querySelectorAll('.rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = document.createElement('div');
    div.className = 'rg-repeat-row';
    div.innerHTML = `
      <div class="flex-grow-1">
        <label class="form-label fw-bold mb-1">Cabang</label>
        <input type="text" class="form-control" name="${inputName}[${idx}]" value="" placeholder="Nama cabang">
      </div>
      <div>
        <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
        <button type="button" class="btn btn-light border" data-remove>Hapus</button>
      </div>
    `;
    wrap.appendChild(div);
  }

  const btnAddBranch = document.getElementById('btnAddBranch');
  if(btnAddBranch) btnAddBranch.addEventListener('click', () => addBranch('heroBranches','hero_branches'));

  const btnAddAboutBranch = document.getElementById('btnAddAboutBranch');
  if(btnAddAboutBranch) btnAddAboutBranch.addEventListener('click', () => addBranch('aboutBranches','about_branches'));

  // ===== COLOR FIELD ENGINE =====
  function isHex(v){
    return /^#([0-9a-fA-F]{6})$/.test((v||'').trim());
  }

  function guessHexFromVar(v){
    if(!v) return '#000000';
    if(v.includes('--rg-blue')) return '#2caae1';
    if(v.includes('--rg-red')) return '#eb1f27';
    if(v.includes('--rg-yellow')) return '#fbed1c';
    if(v === '#000000') return '#000000';
    return '#000000';
  }

  function setSwatch(field, hex){
    const sw = field.querySelector('[data-color-swatch]');
    if(sw) sw.style.background = hex || '#000000';
  }

  function initColorFields(scope){
    const root = scope || document;
    const fields = root.querySelectorAll('[data-color-field]');

    fields.forEach(field => {
      const hidden = field.querySelector('[data-color-hidden]');
      const select = field.querySelector('[data-color-mode]');
      const picker = field.querySelector('[data-color-picker]');
      const hexInp = field.querySelector('[data-color-hex]');
      if(!hidden || !select || !picker || !hexInp) return;

      const allowed = Array.from(select.options).map(o => o.value);
      const current = (hidden.value || '').trim();

      function applyState(){
        const mode = select.value;

        if(mode === 'custom'){
          hexInp.style.display = '';
          picker.style.pointerEvents = 'auto';

          const hv = (hexInp.value || '').trim();
          if(isHex(hv)){
            hidden.value = hv;
            picker.value = hv;
            setSwatch(field, hv);
            return;
          }

          const pv = (picker.value || '').trim();
          const use = isHex(pv) ? pv : '#000000';
          hidden.value = use;
          hexInp.value = use;
          picker.value = use;
          setSwatch(field, use);
          return;
        }

        // preset / black
        hexInp.style.display = 'none';
        picker.style.pointerEvents = 'none';
        hidden.value = mode;

        const preview = guessHexFromVar(mode);
        picker.value = preview;
        hexInp.value = '';
        setSwatch(field, preview);
      }

      // init mode
      if(isHex(current)){
        select.value = 'custom';
        picker.value = current;
        hexInp.value = current;
      } else if(allowed.includes(current) && current !== 'custom'){
        select.value = current;
      } else {
        select.value = 'var(--rg-blue)';
        hidden.value = 'var(--rg-blue)';
      }

      select.addEventListener('change', applyState);

      picker.addEventListener('input', function(){
        if(select.value !== 'custom') return;
        const v = (picker.value || '').trim();
        if(isHex(v)){
          hidden.value = v;
          hexInp.value = v;
          setSwatch(field, v);
        }
      });

      hexInp.addEventListener('input', function(){
        if(select.value !== 'custom') return;
        const v = (hexInp.value || '').trim();
        if(isHex(v)){
          hidden.value = v;
          picker.value = v;
          setSwatch(field, v);
        }
      });

      applyState();
    });
  }

  // init all
  initColorFields(document);

})();
</script>
@endsection
