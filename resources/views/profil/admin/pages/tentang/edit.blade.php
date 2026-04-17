{{-- resources/views/profil/admin/pages/tentang/edit.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Tentang')
@section('page_title', 'Edit Tentang')

@section('content')
@php
  /** @var \App\Models\PTentang $about */

  $routes = $routes ?? [
      ['label' => '— Pilih Link —', 'value' => ''],
      ['label' => 'Beranda', 'value' => 'profil.beranda'],
      ['label' => 'Layanan', 'value' => 'profil.layanan'],
      ['label' => 'Tentang', 'value' => 'profil.tentang'],
      ['label' => 'Berita',  'value' => 'profil.berita'],
      ['label' => 'Kontak',  'value' => 'profil.kontak'],
  ];

  // pilihan warna: brand + black + custom
  $colorModes = [
      ['label' => 'Brand Blue',   'value' => 'var(--rg-blue)'],
      ['label' => 'Brand Yellow', 'value' => 'var(--rg-yellow)'],
      ['label' => 'Brand Red',    'value' => 'var(--rg-red)'],
      ['label' => 'Black',        'value' => '#000000'],
      ['label' => 'Custom Hex',   'value' => 'custom'],
  ];

  $ov = fn($key, $default = '') => old($key, $default);

  // arrays (old > db)
  $titleParts   = old('hero_title_parts', $about->hero_title_parts ?? []);
  $focusItems   = old('focus_items', $about->focus_items ?? []);
  $highlights   = old('highlights', $about->highlights ?? []);
  $faq          = old('faq', $about->faq ?? []);
  $historyStats = old('history_stats', $about->history_stats ?? []);
  $missionItems = old('mission_items', $about->mission_items ?? []);
  $leaders      = old('leaders', $about->leaders ?? []);
  $colors       = old('colors', $about->colors ?? []);
  $clients      = $about->clients ?? [];

  if (!is_array($titleParts)) $titleParts = [];
  if (!is_array($focusItems)) $focusItems = [];
  if (!is_array($highlights)) $highlights = [];
  if (!is_array($faq)) $faq = [];
  if (!is_array($historyStats)) $historyStats = [];
  if (!is_array($missionItems)) $missionItems = [];
  if (!is_array($leaders)) $leaders = [];
  if (!is_array($colors)) $colors = [];
  if (!is_array($clients)) $clients = [];

  $ensureN = function(array $arr, int $n, array $defaults) {
      for ($i=0; $i<$n; $i++) {
          if (!isset($arr[$i]) || !is_array($arr[$i])) $arr[$i] = $defaults[$i] ?? [];
      }
      return $arr;
  };

  // default-ish agar form gak kosong
  if (count($titleParts) === 0) {
    $titleParts = [
      ['text'=>'Tentang', 'color'=>'var(--rg-blue)'],
      ['text'=>'Restu', 'color'=>'var(--rg-yellow)'],
      ['text'=>'Guru', 'color'=>'var(--rg-yellow)'],
      ['text'=>'Promosindo', 'color'=>'var(--rg-red)'],
    ];
  }

  // Focus fixed 3
  $focusItems = $ensureN($focusItems, 3, [
    ['label'=>'Outdoor Advertising', 'accent'=>'var(--rg-blue)'],
    ['label'=>'Indoor Printing', 'accent'=>'var(--rg-red)'],
    ['label'=>'Multi Printing', 'accent'=>'var(--rg-yellow)'],
  ]);

  if (count($highlights) === 0) {
    $highlights = [
      ['text'=>'Material terkurasi & finishing rapi', 'color'=>'var(--rg-blue)'],
      ['text'=>'Timeline jelas & komunikasi cepat', 'color'=>'var(--rg-red)'],
      ['text'=>'Support dari konsep hingga produksi', 'color'=>'var(--rg-yellow)'],
    ];
  }

  if (count($faq) === 0) {
    $faq = [
      ['q'=>'Bagaimana sistem pemesanan?', 'a'=>'Hubungi tim kami untuk konsultasi, kirim file/desain, dan kami bantu proses sampai produksi.'],
      ['q'=>'Apakah bisa custom desain?', 'a'=>'Bisa. Tim kami dapat membantu dari konsep sampai file siap cetak/produksi.'],
      ['q'=>'Berapa lama pengerjaan?', 'a'=>'Tergantung jenis pekerjaan dan antrian produksi. Kami selalu infokan estimasi sejak awal.'],
    ];
  }

  if (count($historyStats) === 0) {
    $historyStats = [
      ['k'=>'Outdoor', 'v'=>'Baliho, billboard, spanduk, neonbox.'],
      ['k'=>'Indoor', 'v'=>'Poster, banner, backdrop, display.'],
      ['k'=>'Multi', 'v'=>'Sticker vinyl, cutting, label, dll.'],
      ['k'=>'Support', 'v'=>'Bantu konsep sampai produksi.'],
    ];
  }

  if (count($missionItems) === 0) {
    $missionItems = [
      'Memberikan hasil produksi berkualitas dan rapi.',
      'Menyediakan layanan cepat dengan komunikasi yang jelas.',
      'Mendukung kebutuhan promosi klien dari awal hingga akhir.',
    ];
  }

  $leaders = $ensureN($leaders, 3, [
    ['name'=>'Nama Leader 1', 'role'=>'Head Production', 'photo'=>null],
    ['name'=>'Nama Leader 2', 'role'=>'Project Supervisor', 'photo'=>null],
    ['name'=>'Nama Leader 3', 'role'=>'Customer Support', 'photo'=>null],
  ]);

  /**
   * Color field (rapih, konsisten dengan Beranda)
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
  .rg-page-wrap{ max-width: 1200px; margin: 0 auto; }
  .rg-topbar{ display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom: 14px; }
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

  .rg-color__row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .rg-color__select{ width: 170px; min-width: 170px; font-weight: 800; }
  .rg-color__swatch-wrap{ position: relative; width: 54px; height: 40px; flex: 0 0 54px; }
  .rg-color__picker{ width: 54px; height: 40px; padding: .2rem; opacity: 0; position:absolute; inset:0; cursor:pointer; }
  .rg-color__swatch{
    display:block; width: 54px; height: 40px; border-radius: 10px;
    border: 1px solid rgba(226,232,240,.95);
    box-shadow: inset 0 0 0 1px rgba(15,23,42,.05);
    background: #000;
  }
  .rg-color__hex{ width: 160px; min-width: 160px; font-weight: 800; letter-spacing: .02em; }
  @media (max-width: 575.98px){
    .rg-color__select{ width: 100%; min-width: 0; }
    .rg-color__hex{ width: 100%; min-width: 0; }
  }

  .rg-actions .btn{ font-weight:900; border-radius: 12px; padding: .6rem 1rem; }

  .rg-preview-img{
    width: 100%;
    max-height: 140px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,.12);
    margin-top: 8px;
  }

  /* ===== Focus layout fix (2 baris) ===== */
  .rg-focus-wrap{ display:flex; flex-direction:column; gap:12px; }
  .rg-focus-row{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 14px;
    background: rgba(248,250,252,.55);
    padding: 12px;
  }
  .rg-focus-actions{ margin-top: 10px; display:flex; justify-content:flex-end; }
  .rg-focus-row .rg-color{ width:100%; }
  .rg-focus-row .rg-color__select{ flex: 1 1 220px; min-width: 220px; width: auto; font-weight: 800; }
  .rg-focus-row .rg-color__hex{ flex: 1 1 180px; min-width: 180px; width: auto; font-weight: 800; }
  @media (max-width: 575.98px){
    .rg-focus-actions{ justify-content:flex-start; }
    .rg-focus-row .rg-color__select,
    .rg-focus-row .rg-color__hex{ min-width: 0; flex-basis: 100%; }
  }

  /* ===== Clients UI (rapi sesuai style sekarang) ===== */
  .rg-client-card{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 14px;
    background:#fff;
    padding: 10px;
    display:flex;
    flex-direction:column;
    gap:10px;
    height:100%;
    box-shadow: 0 8px 18px rgba(15,23,42,.04);
  }
  .rg-client-thumb{
    width:100%;
    height:64px;
    border-radius: 12px;
    border:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.65);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
  }
  .rg-client-thumb img{
    width:100%;
    height:64px;
    object-fit:contain;
    padding: 6px;
  }
  .rg-client-path{
    font-size: 12px;
    color:#64748b;
    word-break: break-all;
    line-height: 1.35;
  }
  .rg-client-actions{
    display:flex;
    gap:10px;
    align-items:center;
    justify-content: space-between;
    margin-top:auto;
    padding-top: 6px;
  }
  .rg-btn-danger{
    border:1px solid rgba(239,68,68,.35);
    background: rgba(239,68,68,.08);
    color:#b91c1c;
    font-weight: 900;
    border-radius: 12px;
    padding: .45rem .75rem;
  }
  .rg-btn-danger:hover{ background: rgba(239,68,68,.12); }
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

  {{-- ✅ FORM UTAMA PUT --}}
  <form id="aboutMainForm" action="{{ route('profil.admin.tentang.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Tentang</div>
        <div class="s">
          Semua warna pakai dropdown <b>Brand/Black</b> atau <b>Custom Hex</b> (picker + hex muncul hanya saat Custom).
          Upload foto: <b>Owner</b>, <b>Leader 1-3</b>, dan <b>Clients</b> (bisa multiple).
        </div>
      </div>

      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.admin.tentang.edit') }}" class="btn btn-light border">Reset</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Pengaturan Halaman Tentang</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">
        <ul class="nav nav-pills rg-tabs gap-2" id="aboutEditTabs" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-hero" type="button" role="tab">Hero</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-why" type="button" role="tab">Why + FAQ</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-owner" type="button" role="tab">Owner</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-history" type="button" role="tab">Sejarah + Visi Misi</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-team" type="button" role="tab">Tim</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-clients" type="button" role="tab">Clients</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-colors" type="button" role="tab">Warna</button></li>
        </ul>

        <div class="tab-content pt-4">

          {{-- ================= HERO ================= --}}
          <div class="tab-pane fade show active" id="pane-hero" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">Hero (Left)</div>
                  <div class="rg-section-sub">Chip/badge, judul berwarna (repeatable), deskripsi, dan tombol.</div>

                  <label class="form-label fw-bold">Chip Label</label>
                  <input name="hero_chip" class="form-control" value="{{ $ov('hero_chip', $about->hero_chip) }}">

                  <hr class="my-4">

                  <div class="rg-section-title">Judul Berwarna (Repeatable)</div>
                  <div class="rg-section-sub">Maks 6 potongan. Tiap potongan: teks + warna.</div>

                  <div id="heroTitleParts" data-max="6">
                    @foreach($titleParts as $i => $p)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Teks</label>
                          <input type="text" class="form-control" name="hero_title_parts[{{ $i }}][text]" value="{{ $p['text'] ?? '' }}" placeholder="Mis: Tentang">
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
                  <textarea name="hero_desc" class="form-control" rows="3">{{ $ov('hero_desc', $about->hero_desc) }}</textarea>

                  <hr class="my-4">

                  <div class="rg-section-title">Tombol Hero</div>
                  <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Label)</label>
                      <input name="hero_btn1_label" class="form-control" value="{{ $ov('hero_btn1_label', $about->hero_btn1_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 1 (Link)</label>
                      <select name="hero_btn1_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_btn1_route', $about->hero_btn1_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Label)</label>
                      <input name="hero_btn2_label" class="form-control" value="{{ $ov('hero_btn2_label', $about->hero_btn2_label) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Link)</label>
                      <select name="hero_btn2_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_btn2_route', $about->hero_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Focus Layanan (3 Item)</div>
                  <div class="rg-section-sub">Teks + warna accent bar (dipakai di panel kanan). Fixed 3 agar layout stabil.</div>

                  <div class="rg-focus-wrap" id="focusItems" data-max="3">
                    @for($i=0; $i<3; $i++)
                      @php $it = $focusItems[$i] ?? []; @endphp

                      <div class="rg-focus-row">
                        <div class="rg-focus-block">
                          <label class="form-label fw-bold mb-1">Label</label>
                          <input type="text"
                                 class="form-control"
                                 name="focus_items[{{ $i }}][label]"
                                 value="{{ $it['label'] ?? '' }}"
                                 placeholder="Mis: Outdoor Advertising">
                        </div>

                        <div class="rg-focus-block mt-3">
                          {!! $colorField("focus_items[$i][accent]", ($it['accent'] ?? 'var(--rg-blue)'), "focus_item_$i", 'Accent Color') !!}
                        </div>

                        <div class="rg-focus-actions">
                          <button type="button" class="btn btn-light border" disabled title="Focus item fixed 3">Fixed</button>
                        </div>
                      </div>
                    @endfor
                  </div>

                  <div class="alert alert-info mb-0 mt-3">
                    Focus di halaman Tentang memang 3 item (fixed) supaya layout panel kanan stabil.
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= WHY + FAQ ================= --}}
          <div class="tab-pane fade" id="pane-why" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">Why Section</div>
                  <div class="rg-section-sub">Judul + deskripsi (kiri).</div>

                  <label class="form-label fw-bold">Judul</label>
                  <input name="why_title" class="form-control" value="{{ $ov('why_title', $about->why_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <input name="why_desc" class="form-control" value="{{ $ov('why_desc', $about->why_desc) }}">
                </div>

                <div class="rg-section-card mt-3">
                  <div class="rg-section-title">Highlight (Repeatable)</div>
                  <div class="rg-section-sub">Maks 6 item. Tiap item: teks + warna dot.</div>

                  <div id="highlightsWrap" data-max="6">
                    @foreach($highlights as $i => $h)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Teks</label>
                          <input type="text" class="form-control" name="highlights[{{ $i }}][text]" value="{{ $h['text'] ?? '' }}" placeholder="Mis: Material terkurasi">
                        </div>

                        <div style="min-width: 320px;">
                          {!! $colorField("highlights[$i][color]", ($h['color'] ?? 'var(--rg-blue)'), "highlight_$i", 'Dot Color') !!}
                        </div>

                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddHighlight">+ Tambah Highlight</button>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">FAQ (Repeatable)</div>
                  <div class="rg-section-sub">Maks 12 item. Tiap item: pertanyaan + jawaban.</div>

                  <div id="faqWrap" data-max="12">
                    @foreach($faq as $i => $f)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Pertanyaan</label>
                          <input type="text" class="form-control" name="faq[{{ $i }}][q]" value="{{ $f['q'] ?? '' }}" placeholder="Pertanyaan">
                          <label class="form-label fw-bold mt-3 mb-1">Jawaban</label>
                          <textarea class="form-control" name="faq[{{ $i }}][a]" rows="3" placeholder="Jawaban">{{ $f['a'] ?? '' }}</textarea>
                        </div>

                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddFaq">+ Tambah FAQ</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= OWNER ================= --}}
          <div class="tab-pane fade" id="pane-owner" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">Owner Message</div>
                  <div class="rg-section-sub">Small label, judul, pesan, nama & role.</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Small</label>
                      <input name="owner_small" class="form-control" value="{{ $ov('owner_small', $about->owner_small) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Nama Owner</label>
                      <input name="owner_name" class="form-control" value="{{ $ov('owner_name', $about->owner_name) }}">
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-bold">Judul</label>
                      <input name="owner_title" class="form-control" value="{{ $ov('owner_title', $about->owner_title) }}">
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-bold">Pesan</label>
                      <textarea name="owner_message" class="form-control" rows="4">{{ $ov('owner_message', $about->owner_message) }}</textarea>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Role</label>
                      <input name="owner_role" class="form-control" value="{{ $ov('owner_role', $about->owner_role) }}">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Foto Owner</div>
                  <div class="rg-section-sub">Upload foto owner (opsional).</div>

                  <label class="form-label fw-bold">Upload</label>
                  <input type="file" class="form-control" name="owner_photo" accept="image/*">

                  @if(!empty($about->owner_photo))
                    <div class="small text-muted mt-2">Current: <code>{{ $about->owner_photo }}</code></div>
                    <img src="{{ asset('storage/'.$about->owner_photo) }}" class="rg-preview-img" alt="owner">
                  @endif

                  <div class="alert alert-warning mt-3 mb-0">
                    Jika upload baru, foto lama otomatis diganti.
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= HISTORY + VISION MISSION ================= --}}
          <div class="tab-pane fade" id="pane-history" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">Sejarah Singkat</div>
                  <div class="rg-section-sub">Judul + deskripsi + stats (repeatable, maks 8).</div>

                  <label class="form-label fw-bold">Judul</label>
                  <input name="history_title" class="form-control" value="{{ $ov('history_title', $about->history_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <textarea name="history_desc" class="form-control" rows="4">{{ $ov('history_desc', $about->history_desc) }}</textarea>

                  <hr class="my-4">

                  <div class="rg-section-title">Stats (Repeatable)</div>
                  <div class="rg-section-sub">Maks 8 item. Tiap item: Key (k) + Value (v).</div>

                  <div id="historyStatsWrap" data-max="8">
                    @foreach($historyStats as $i => $s)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Key</label>
                          <input type="text" class="form-control" name="history_stats[{{ $i }}][k]" value="{{ $s['k'] ?? '' }}" placeholder="Mis: Outdoor">
                        </div>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Value</label>
                          <input type="text" class="form-control" name="history_stats[{{ $i }}][v]" value="{{ $s['v'] ?? '' }}" placeholder="Mis: Baliho, billboard...">
                        </div>
                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddHistoryStat">+ Tambah Stat</button>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">Visi</div>
                  <div class="rg-section-sub">Title + deskripsi.</div>

                  <label class="form-label fw-bold">Title</label>
                  <input name="vision_title" class="form-control" value="{{ $ov('vision_title', $about->vision_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <textarea name="vision_desc" class="form-control" rows="4">{{ $ov('vision_desc', $about->vision_desc) }}</textarea>
                </div>

                <div class="rg-section-card mt-3">
                  <div class="rg-section-title">Misi</div>
                  <div class="rg-section-sub">Title + items (repeatable). Maks 12.</div>

                  <label class="form-label fw-bold">Title</label>
                  <input name="mission_title" class="form-control" value="{{ $ov('mission_title', $about->mission_title) }}">

                  <hr class="my-4">

                  <div id="missionItemsWrap" data-max="12">
                    @foreach($missionItems as $i => $m)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Item</label>
                          <input type="text" class="form-control" name="mission_items[{{ $i }}]" value="{{ $m }}" placeholder="Isi misi">
                        </div>
                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddMissionItem">+ Tambah Misi</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ================= TEAM ================= --}}
          <div class="tab-pane fade" id="pane-team" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Leaders (3 orang)</div>
              <div class="rg-section-sub">Nama + role + foto. Fixed 3 agar layout stabil.</div>

              <div class="row g-3">
                @for($i=0; $i<3; $i++)
                  @php
                    $p = $leaders[$i] ?? [];
                    $oldPhoto = $about->leaders[$i]['photo'] ?? null;
                  @endphp
                  <div class="col-12 col-lg-4">
                    <div class="rg-section-card" style="padding:14px;">
                      <div class="fw-bold mb-2">Leader {{ $i+1 }}</div>

                      <label class="form-label fw-bold">Nama</label>
                      <input class="form-control" name="leaders[{{ $i }}][name]" value="{{ $p['name'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Role</label>
                      <input class="form-control" name="leaders[{{ $i }}][role]" value="{{ $p['role'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Foto</label>
                      <input type="file" class="form-control" name="leaders[{{ $i }}][photo]" accept="image/*">

                      @if(!empty($oldPhoto))
                        <div class="small text-muted mt-2">Current: <code>{{ $oldPhoto }}</code></div>
                        <img src="{{ asset('storage/'.$oldPhoto) }}" class="rg-preview-img" alt="leader-{{ $i }}">
                      @endif
                    </div>
                  </div>
                @endfor
              </div>

              <div class="alert alert-warning mt-3 mb-0">
                Jika upload foto leader baru, foto lama otomatis diganti.
              </div>
            </div>
          </div>

          {{-- ================= CLIENTS (TAMPIL HANYA DI TAB CLIENTS) ================= --}}
          <div class="tab-pane fade" id="pane-clients" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Upload Logo Client</div>
                  <div class="rg-section-sub">Bisa upload banyak sekaligus (multiple). Logo baru akan <b>ditambahkan</b>.</div>

                  <label class="form-label fw-bold">Upload (multiple)</label>
                  <input type="file" class="form-control" name="clients[]" accept="image/*" multiple>

                  <div class="alert alert-info mt-3 mb-0">
                    Existing clients aman. Upload baru menambah list (append).
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                      <div class="rg-section-title">Existing Clients</div>
                      <div class="rg-section-sub mb-0">Klik <b>Hapus</b> untuk hapus 1 logo (file storage ikut dihapus).</div>
                    </div>
                    <div class="text-muted small">Total: {{ count($clients) }}</div>
                  </div>

                  @if(count($clients) === 0)
                    <div class="text-muted">Belum ada logo client.</div>
                  @else
                    <div class="row g-2 mt-2">
                      @foreach($clients as $i => $path)
                        <div class="col-6 col-md-4">
                          <div class="rg-client-card">
                            <div class="rg-client-thumb">
                              <img src="{{ asset('storage/'.$path) }}" alt="client-{{ $i }}">
                            </div>

                            <div class="rg-client-path"><code>{{ $path }}</code></div>

                            <div class="rg-client-actions">
                              <span class="text-muted small">#{{ $i+1 }}</span>

                              {{-- ✅ tombol ini submit ke FORM DELETE yang ADA DI LUAR (tidak nested) --}}
                              <button
                                type="submit"
                                class="rg-btn-danger"
                                form="delClientForm{{ $i }}"
                                onclick="return confirm('Hapus logo client ini? File akan ikut terhapus.');"
                                title="Hapus logo ini"
                              >
                                Hapus
                              </button>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                      Tombol <b>Hapus</b> aman (tidak nested form). Error method DELETE ke /admin/tentang tidak akan muncul lagi.
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- ================= COLORS ================= --}}
          <div class="tab-pane fade" id="pane-colors" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Warna</div>
              <div class="rg-section-sub">Untuk hero blob / aksen global halaman tentang (opsional).</div>

              <div class="row g-3">
                @php
                  $colorFields = [
                    ['label'=>'Hero Blob Blue',   'key'=>'blob_blue',   'default'=>'var(--rg-blue)'],
                    ['label'=>'Hero Blob Red',    'key'=>'blob_red',    'default'=>'var(--rg-red)'],
                    ['label'=>'Hero Blob Yellow', 'key'=>'blob_yellow', 'default'=>'var(--rg-yellow)'],
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
                Warna ini dipakai di public halaman Tentang untuk blob hero.
              </div>
            </div>
          </div>

        </div>{{-- tab-content --}}
      </div>{{-- card-body --}}
    </div>{{-- card-shell --}}
  </form>

  {{-- ✅ FORM DELETE CLIENTS (DI LUAR FORM UTAMA, HIDDEN, TAPI TOMBOL DI TAB CLIENTS BISA SUBMIT KE SINI) --}}
  @foreach($clients as $i => $path)
    <form id="delClientForm{{ $i }}"
          method="POST"
          action="{{ route('profil.admin.tentang.clients.delete', $i) }}"
          style="display:none;">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

</div>

{{-- ================== SCRIPT ENGINE (repeat + colorField) ================== --}}
<script>
(function(){
  // ===== repeat remove =====
  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-remove]');
    if(!btn) return;
    const row = btn.closest('[data-repeat-row], .rg-repeat-row');
    if(row) row.remove();
  });

  function el(html){
    const t = document.createElement('template');
    t.innerHTML = html.trim();
    return t.content.firstElementChild;
  }

  // ===== COLOR FIELD ENGINE =====
  function isHex(v){
    return /^#([0-9a-fA-F]{6})$/.test((v||'').trim());
  }

  function guessHexFromVar(v){
    if(!v) return '#000000';
    if(String(v).includes('--rg-blue')) return '#2caae1';
    if(String(v).includes('--rg-red')) return '#eb1f27';
    if(String(v).includes('--rg-yellow')) return '#fbed1c';
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

        hexInp.style.display = 'none';
        picker.style.pointerEvents = 'none';
        hidden.value = mode;

        const preview = guessHexFromVar(mode);
        picker.value = preview;
        hexInp.value = '';
        setSwatch(field, preview);
      }

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

  // ===== ADD: HERO TITLE PART =====
  const heroTitleWrap = document.getElementById('heroTitleParts');
  const btnAddTitle   = document.getElementById('btnAddHeroTitlePart');

  function addHeroTitlePart(){
    if(!heroTitleWrap) return;
    const max = parseInt(heroTitleWrap.dataset.max || '6', 10);
    const rows = heroTitleWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = el(`
      <div class="rg-repeat-row" data-repeat-row="1">
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
      </div>
    `);

    heroTitleWrap.appendChild(div);
    initColorFields(div);
  }
  if(btnAddTitle) btnAddTitle.addEventListener('click', addHeroTitlePart);

  // ===== ADD: HIGHLIGHT =====
  const highlightsWrap = document.getElementById('highlightsWrap');
  const btnAddHighlight = document.getElementById('btnAddHighlight');

  function addHighlight(){
    if(!highlightsWrap) return;
    const max = parseInt(highlightsWrap.dataset.max || '6', 10);
    const rows = highlightsWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = el(`
      <div class="rg-repeat-row" data-repeat-row="1">
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Teks</label>
          <input type="text" class="form-control" name="highlights[${idx}][text]" value="" placeholder="Mis: Timeline jelas">
        </div>

        <div style="min-width: 320px;">
          <div class="rg-color rg-color--compact" data-color-field id="cf-highlight_${idx}">
            <label class="form-label fw-bold mb-1">Dot Color</label>
            <input type="hidden" name="highlights[${idx}][color]" value="var(--rg-blue)" data-color-hidden>

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
      </div>
    `);

    highlightsWrap.appendChild(div);
    initColorFields(div);
  }
  if(btnAddHighlight) btnAddHighlight.addEventListener('click', addHighlight);

  // ===== ADD: FAQ =====
  const faqWrap = document.getElementById('faqWrap');
  const btnAddFaq = document.getElementById('btnAddFaq');

  function addFaq(){
    if(!faqWrap) return;
    const max = parseInt(faqWrap.dataset.max || '12', 10);
    const rows = faqWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = el(`
      <div class="rg-repeat-row" data-repeat-row="1">
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Pertanyaan</label>
          <input type="text" class="form-control" name="faq[${idx}][q]" value="" placeholder="Pertanyaan">
          <label class="form-label fw-bold mt-3 mb-1">Jawaban</label>
          <textarea class="form-control" name="faq[${idx}][a]" rows="3" placeholder="Jawaban"></textarea>
        </div>
        <div>
          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
        </div>
      </div>
    `);

    faqWrap.appendChild(div);
  }
  if(btnAddFaq) btnAddFaq.addEventListener('click', addFaq);

  // ===== ADD: HISTORY STAT =====
  const historyStatsWrap = document.getElementById('historyStatsWrap');
  const btnAddHistoryStat = document.getElementById('btnAddHistoryStat');

  function addHistoryStat(){
    if(!historyStatsWrap) return;
    const max = parseInt(historyStatsWrap.dataset.max || '8', 10);
    const rows = historyStatsWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = el(`
      <div class="rg-repeat-row" data-repeat-row="1">
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Key</label>
          <input type="text" class="form-control" name="history_stats[${idx}][k]" value="" placeholder="Mis: Outdoor">
        </div>
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Value</label>
          <input type="text" class="form-control" name="history_stats[${idx}][v]" value="" placeholder="Mis: Baliho, billboard...">
        </div>
        <div>
          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
        </div>
      </div>
    `);

    historyStatsWrap.appendChild(div);
  }
  if(btnAddHistoryStat) btnAddHistoryStat.addEventListener('click', addHistoryStat);

  // ===== ADD: MISSION ITEM =====
  const missionWrap = document.getElementById('missionItemsWrap');
  const btnAddMissionItem = document.getElementById('btnAddMissionItem');

  function addMissionItem(){
    if(!missionWrap) return;
    const max = parseInt(missionWrap.dataset.max || '12', 10);
    const rows = missionWrap.querySelectorAll('[data-repeat-row], .rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = el(`
      <div class="rg-repeat-row" data-repeat-row="1">
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Item</label>
          <input type="text" class="form-control" name="mission_items[${idx}]" value="" placeholder="Isi misi">
        </div>
        <div>
          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
        </div>
      </div>
    `);

    missionWrap.appendChild(div);
  }
  if(btnAddMissionItem) btnAddMissionItem.addEventListener('click', addMissionItem);

  // init all colors
  initColorFields(document);

})();
</script>
@endsection
