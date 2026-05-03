{{-- resources/views/profil/admin/pages/kontak/edit.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Kontak')
@section('page_title', 'Edit Kontak')

@section('content')
@php
  /** @var \App\Models\PKontakPage $page */
  /** @var \Illuminate\Support\Collection|\App\Models\PKontakBranch[] $branches */

  $routes = $routes ?? [
      ['label' => '— Pilih Link —', 'value' => ''],
      ['label' => 'Beranda', 'value' => 'profil.beranda'],
      ['label' => 'Layanan', 'value' => 'profil.layanan'],
      ['label' => 'Tentang', 'value' => 'profil.tentang'],
      ['label' => 'Berita',  'value' => 'profil.berita'],
      ['label' => 'Kontak',  'value' => 'profil.kontak'],
  ];

  $colorModes = [
      ['label' => 'Brand Blue',   'value' => 'var(--rg-blue)'],
      ['label' => 'Brand Yellow', 'value' => 'var(--rg-yellow)'],
      ['label' => 'Brand Red',    'value' => 'var(--rg-red)'],
      ['label' => 'Black',        'value' => '#000000'],
      ['label' => 'Custom Hex',   'value' => 'custom'],
  ];

  $ov = fn($key, $default = '') => old($key, $default);

  $pills = old('hero_pills', $page->hero_pills ?? []);
  if (!is_array($pills)) $pills = [];
  for ($i=0; $i<3; $i++) {
      if (!isset($pills[$i]) || !is_array($pills[$i])) $pills[$i] = [];
      $pills[$i]['text']  = $pills[$i]['text']  ?? '';
      $pills[$i]['color'] = $pills[$i]['color'] ?? 'var(--rg-blue)';
  }

  $stats = old('stats', $page->stats ?? []);
  if (!is_array($stats)) $stats = [];
  $defaultAcc = ['var(--rg-blue)','var(--rg-red)','var(--rg-yellow)'];
  for ($i=0; $i<3; $i++) {
      if (!isset($stats[$i]) || !is_array($stats[$i])) $stats[$i] = [];
      $stats[$i]['k'] = $stats[$i]['k'] ?? '';
      $stats[$i]['v'] = $stats[$i]['v'] ?? '';
      $stats[$i]['accent'] = $stats[$i]['accent'] ?? ($defaultAcc[$i] ?? 'var(--rg-blue)');
  }

  // branches rows
  $branchRows = old('branches', $branches ? $branches->map(function($b){
      return [
          'id' => $b->id,
          'name' => $b->name,
          'address' => $b->address,
          'maps_url' => $b->maps_url,
          'lat' => $b->lat,
          'lng' => $b->lng,
          'is_active' => $b->is_active ? 1 : 0,
          'sort_order' => $b->sort_order ?? 0,
      ];
  })->values()->all() : []);

  if (!is_array($branchRows)) $branchRows = [];

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
  }

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

  .rg-branch-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .rg-branch-row{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 14px;
    padding: 12px;
    background: rgba(248,250,252,.55);
  }

  .rg-branch-row .row{ --bs-gutter-x: .8rem; --bs-gutter-y: .8rem; }
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

  <form action="{{ route('profil.admin.kontak.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Kontak</div>
        <div class="s">
          Konsisten dengan halaman lain: teks, label tombol, warna dot (brand/black/custom), dan data cabang bisa diubah di sini.
          <b>WhatsApp link</b> ngikut dari Beranda (field <code>wa_value</code>).
        </div>
      </div>
      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.admin.kontak.edit') }}" class="btn btn-light border">Reset</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Pengaturan Kontak</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">
        {{-- Tabs --}}
        <ul class="nav nav-pills rg-tabs gap-2" id="contactEditTabs" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-hero" type="button" role="tab">Hero</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-stats" type="button" role="tab">Stats</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-branches" type="button" role="tab">Cabang</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-map" type="button" role="tab">Map</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-cta" type="button" role="tab">CTA Bottom</button></li>
        </ul>

        <div class="tab-content pt-4">

          {{-- ================= HERO ================= --}}
          <div class="tab-pane fade show active" id="pane-hero" role="tabpanel" tabindex="0">
            <div class="row g-3">

              <div class="col-12 col-xl-7">
                <div class="rg-section-card">
                  <div class="rg-section-title">Hero (Left)</div>
                  <div class="rg-section-sub">Chip, judul, deskripsi, tombol (WA + tombol 2).</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Chip</label>
                      <input name="hero_chip" class="form-control" value="{{ $ov('hero_chip', $page->hero_chip) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Judul</label>
                      <input name="hero_title" class="form-control" value="{{ $ov('hero_title', $page->hero_title) }}">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Lead</label>
                      <textarea name="hero_lead" class="form-control" rows="3">{{ $ov('hero_lead', $page->hero_lead) }}</textarea>
                    </div>
                  </div>

                  <hr class="my-4">

                  <div class="rg-section-title">Tombol Hero</div>

                  <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol WA (Label)</label>
                      <input name="hero_btn_wa_label" class="form-control" value="{{ $ov('hero_btn_wa_label', $page->hero_btn_wa_label) }}">
                      <div class="form-text">Link WA ngikut dari Beranda (wa_value).</div>
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Label)</label>
                      <input name="hero_btn2_label" class="form-control" value="{{ $ov('hero_btn2_label', $page->hero_btn2_label) }}">
                    </div>

                    <div class="col-12">
                      <label class="form-label fw-bold">Tombol 2 (Link)</label>
                      <select name="hero_btn2_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('hero_btn2_route', $page->hero_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>
              </div>

              <div class="col-12 col-xl-5">
                <div class="rg-section-card">
                  <div class="rg-section-title">Panel Kanan</div>
                  <div class="rg-section-sub">Judul, deskripsi (multi-line), dan 3 pill (text + dot color).</div>

                  <label class="form-label fw-bold">Judul Panel</label>
                  <input name="panel_title" class="form-control" value="{{ $ov('panel_title', $page->panel_title) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi Panel</label>
                  <textarea name="panel_desc" class="form-control" rows="3">{{ $ov('panel_desc', $page->panel_desc) }}</textarea>

                  <hr class="my-4">

                  <div class="rg-section-title">Pills (3 item)</div>
                  <div class="rg-section-sub">Text + dot color (brand/black/custom).</div>

                  <div class="row g-3">
                    @for($i=0; $i<3; $i++)
                      <div class="col-12">
                        <div class="rg-section-card" style="padding: 14px;">
                          <div class="row g-2">
                            <div class="col-12 col-md-6">
                              <label class="form-label fw-bold">Teks</label>
                              <input class="form-control" name="hero_pills[{{ $i }}][text]" value="{{ $pills[$i]['text'] ?? '' }}">
                            </div>
                            <div class="col-12 col-md-6">
                              {!! $colorField("hero_pills[$i][color]", ($pills[$i]['color'] ?? 'var(--rg-blue)'), "pill_$i", 'Dot Color') !!}
                            </div>
                          </div>
                        </div>
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- ================= STATS ================= --}}
          <div class="tab-pane fade" id="pane-stats" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Mini Stats (3 box)</div>
              <div class="rg-section-sub">Tiap box: Key + Value + Accent Color.</div>

              <div class="row g-3">
                @for($i=0; $i<3; $i++)
                  <div class="col-12 col-lg-4">
                    <div class="rg-section-card" style="padding:14px;">
                      <div class="fw-bold mb-2">Stat {{ $i+1 }}</div>

                      <label class="form-label fw-bold">Key</label>
                      <input class="form-control" name="stats[{{ $i }}][k]" value="{{ $stats[$i]['k'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Value</label>
                      <input class="form-control" name="stats[{{ $i }}][v]" value="{{ $stats[$i]['v'] ?? '' }}">

                      <div class="mt-3">
                        {!! $colorField("stats[$i][accent]", ($stats[$i]['accent'] ?? $defaultAcc[$i]), "stat_acc_$i", 'Accent Color') !!}
                      </div>
                    </div>
                  </div>
                @endfor
              </div>
            </div>
          </div>

          {{-- ================= CABANG ================= --}}
          <div class="tab-pane fade" id="pane-branches" role="tabpanel" tabindex="0">
            <div class="row g-3">
              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">Section Cabang</div>
                  <div class="rg-section-sub">Heading, deskripsi, dan label “Buka →”.</div>

                  <label class="form-label fw-bold">Heading</label>
                  <input name="branches_heading" class="form-control" value="{{ $ov('branches_heading', $page->branches_heading) }}">

                  <label class="form-label fw-bold mt-3">Deskripsi</label>
                  <input name="branches_desc" class="form-control" value="{{ $ov('branches_desc', $page->branches_desc) }}">

                  <label class="form-label fw-bold mt-3">Label “Buka →”</label>
                  <input name="branch_open_label" class="form-control" value="{{ $ov('branch_open_label', $page->branch_open_label) }}">
                </div>

                <div class="rg-section-card mt-3">
                  <div class="rg-section-title">Help Box</div>
                  <div class="rg-section-sub">Teks kecil + tombol WA + tombol 2.</div>

                  <label class="form-label fw-bold">Judul</label>
                  <input name="help_title" class="form-control" value="{{ $ov('help_title', $page->help_title) }}">

                  <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol WA (Label)</label>
                      <input name="help_btn_wa" class="form-control" value="{{ $ov('help_btn_wa', $page->help_btn_wa) }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Tombol 2 (Label)</label>
                      <input name="help_btn2_label" class="form-control" value="{{ $ov('help_btn2_label', $page->help_btn2_label) }}">
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Tombol 2 (Link)</label>
                      <select name="help_btn2_route" class="form-select">
                        @foreach($routes as $r)
                          <option value="{{ $r['value'] }}" @selected($ov('help_btn2_route', $page->help_btn2_route)===$r['value'])>{{ $r['label'] }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

              </div>

              <div class="col-12 col-xl-6">
                <div class="rg-section-card">
                  <div class="rg-section-title">Daftar Cabang (CRUD sederhana)</div>
                  <div class="rg-section-sub">Tambah/hapus row. Isi nama, alamat, maps url, lat/lng, aktif, urutan.</div>

                  <div id="branchesWrap" class="rg-branch-grid">
                    @foreach($branchRows as $i => $b)
                      <div class="rg-branch-row" data-branch-row>
                        <input type="hidden" name="branches[{{ $i }}][id]" value="{{ $b['id'] ?? '' }}">

                        <div class="row g-2">
                          <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Nama</label>
                            <input class="form-control" name="branches[{{ $i }}][name]" value="{{ $b['name'] ?? '' }}">
                          </div>
                          <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Urutan</label>
                            <input type="number" class="form-control" name="branches[{{ $i }}][sort_order]" value="{{ $b['sort_order'] ?? 0 }}" min="0" max="9999">
                          </div>

                          <div class="col-12">
                            <label class="form-label fw-bold">Alamat</label>
                            <input class="form-control" name="branches[{{ $i }}][address]" value="{{ $b['address'] ?? '' }}">
                          </div>

                          <div class="col-12">
                            <label class="form-label fw-bold">Google Maps URL</label>
                            <input class="form-control" name="branches[{{ $i }}][maps_url]" value="{{ $b['maps_url'] ?? '' }}" placeholder="https://maps.app.goo.gl/...">
                          </div>

                          <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Lat</label>
                            <input class="form-control" name="branches[{{ $i }}][lat]" value="{{ $b['lat'] ?? '' }}" placeholder="-3.3219">
                          </div>
                          <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Lng</label>
                            <input class="form-control" name="branches[{{ $i }}][lng]" value="{{ $b['lng'] ?? '' }}" placeholder="114.5936">
                          </div>
                          <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Aktif</label>
                            <select class="form-select" name="branches[{{ $i }}][is_active]">
                              <option value="1" @selected(($b['is_active'] ?? 0)==1)>Ya</option>
                              <option value="0" @selected(($b['is_active'] ?? 0)==0)>Tidak</option>
                            </select>
                          </div>

                          <div class="col-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-light border" data-remove-branch>Hapus</button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddBranchRow">+ Tambah Cabang</button>
                  </div>

                  <div class="alert alert-info mt-3 mb-0">
                    Cabang di public: hanya yang <b>Aktif</b>. Urutan pakai <b>sort_order</b> (kecil dulu).
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- ================= MAP ================= --}}
          <div class="tab-pane fade" id="pane-map" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">Map</div>
              <div class="rg-section-sub">Judul, deskripsi, dan teks fallback kalau Leaflet gagal load.</div>

              <label class="form-label fw-bold">Heading</label>
              <input name="map_heading" class="form-control" value="{{ $ov('map_heading', $page->map_heading) }}">

              <label class="form-label fw-bold mt-3">Deskripsi</label>
              <input name="map_desc" class="form-control" value="{{ $ov('map_desc', $page->map_desc) }}">

              <label class="form-label fw-bold mt-3">Fallback Text</label>
              <input name="map_fallback" class="form-control" value="{{ $ov('map_fallback', $page->map_fallback) }}">
            </div>
          </div>

          {{-- ================= CTA ================= --}}
          <div class="tab-pane fade" id="pane-cta" role="tabpanel" tabindex="0">
            <div class="rg-section-card">
              <div class="rg-section-title">CTA Bottom</div>
              <div class="rg-section-sub">Judul, deskripsi, tombol WA, tombol kembali.</div>

              <label class="form-label fw-bold">Judul</label>
              <input name="cta_title" class="form-control" value="{{ $ov('cta_title', $page->cta_title) }}">

              <label class="form-label fw-bold mt-3">Deskripsi</label>
              <textarea name="cta_desc" class="form-control" rows="3">{{ $ov('cta_desc', $page->cta_desc) }}</textarea>

              <div class="row g-3 mt-1">
                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol WA (Label)</label>
                  <input name="cta_btn_wa" class="form-control" value="{{ $ov('cta_btn_wa', $page->cta_btn_wa) }}">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol Kembali (Label)</label>
                  <input name="cta_btn_back" class="form-control" value="{{ $ov('cta_btn_back', $page->cta_btn_back) }}">
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">Tombol Kembali (Link)</label>
                  <select name="cta_btn_back_route" class="form-select">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('cta_btn_back_route', $page->cta_btn_back_route)===$r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="alert alert-warning mt-3 mb-0">
                Link WA tidak diatur di sini. Ambil dari Beranda (field <code>wa_value</code>).
              </div>
            </div>
          </div>

        </div>{{-- tab-content --}}
      </div>{{-- card-body --}}
    </div>{{-- card-shell --}}
  </form>
</div>

<script>
(function(){
  // ===== COLOR FIELD ENGINE (sama seperti beranda) =====
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

  initColorFields(document);

  // ===== branches add/remove =====
  const wrap = document.getElementById('branchesWrap');
  const btnAdd = document.getElementById('btnAddBranchRow');

  function branchRowTemplate(idx){
    return `
      <div class="rg-branch-row" data-branch-row>
        <input type="hidden" name="branches[${idx}][id]" value="">

        <div class="row g-2">
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Nama</label>
            <input class="form-control" name="branches[${idx}][name]" value="">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Urutan</label>
            <input type="number" class="form-control" name="branches[${idx}][sort_order]" value="0" min="0" max="9999">
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Alamat</label>
            <input class="form-control" name="branches[${idx}][address]" value="">
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Google Maps URL</label>
            <input class="form-control" name="branches[${idx}][maps_url]" value="" placeholder="https://maps.app.goo.gl/...">
          </div>

          <div class="col-12 col-md-4">
            <label class="form-label fw-bold">Lat</label>
            <input class="form-control" name="branches[${idx}][lat]" value="" placeholder="-3.3219">
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label fw-bold">Lng</label>
            <input class="form-control" name="branches[${idx}][lng]" value="" placeholder="114.5936">
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label fw-bold">Aktif</label>
            <select class="form-select" name="branches[${idx}][is_active]">
              <option value="1" selected>Ya</option>
              <option value="0">Tidak</option>
            </select>
          </div>

          <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-light border" data-remove-branch>Hapus</button>
          </div>
        </div>
      </div>
    `;
  }

  if(btnAdd && wrap){
    btnAdd.addEventListener('click', function(){
      const idx = wrap.querySelectorAll('[data-branch-row]').length;
      const div = document.createElement('div');
      div.innerHTML = branchRowTemplate(idx);
      wrap.appendChild(div.firstElementChild);
    });
  }

  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-remove-branch]');
    if(!btn) return;
    const row = btn.closest('[data-branch-row]');
    if(row) row.remove();
  });

})();
</script>
@endsection
