{{-- resources/views/profil/admin/pages/layanan/edit.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Layanan')
@section('page_title', 'Edit Layanan')

@section('content')
@php
  /** @var \App\Models\PLayanan $s */

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

  $heroParts   = old('hero_title_parts', $s->hero_title_parts ?? []);
  $summaryItems= old('summary_items', $s->summary_items ?? []);
  $whyCards    = old('why_cards', $s->why_cards ?? []);
  $categories  = old('categories', $s->categories ?? []);

  if(!is_array($heroParts)) $heroParts = [];
  if(!is_array($summaryItems)) $summaryItems = [];
  if(!is_array($whyCards)) $whyCards = [];
  if(!is_array($categories)) $categories = [];

  // ensure fixed counts
  $ensure3 = function(array $arr, array $defaults){
      for($i=0;$i<3;$i++){
          if(!isset($arr[$i]) || !is_array($arr[$i])) $arr[$i] = $defaults[$i] ?? [];
      }
      return $arr;
  };

  if (count($heroParts) === 0) {
      $heroParts = [
        ['text'=>'Layanan','color'=>'var(--rg-blue)'],
        ['text'=>'Restu Guru','color'=>'var(--rg-yellow)'],
        ['text'=>'Promosindo','color'=>'var(--rg-red)'],
      ];
  }

  if (count($summaryItems) === 0) {
      $summaryItems = [
        ['text'=>'Outdoor Advertising','dot'=>'var(--rg-blue)'],
        ['text'=>'Indoor Printing','dot'=>'var(--rg-red)'],
        ['text'=>'Multi (Stiker & Kecil)','dot'=>'var(--rg-yellow)'],
      ];
  }

  $whyCards = $ensure3($whyCards, [
      ['title'=>'Kualitas Produksi','desc'=>'Material terkurasi, finishing rapi, QC sebelum kirim.','accent'=>'var(--rg-blue)','image'=>null],
      ['title'=>'Cepat & Tepat','desc'=>'Timeline jelas, komunikasi cepat, pengerjaan efisien.','accent'=>'var(--rg-red)','image'=>null],
      ['title'=>'Support Tim','desc'=>'Dibantu dari konsep sampai file siap produksi.','accent'=>'var(--rg-yellow)','image'=>null],
  ]);

  // categories fixed 3
  if (count($categories) === 0) {
      $categories = \App\Models\PLayanan::defaults()['categories'];
  } else {
      // pastikan 3 kategori ada
      $d = \App\Models\PLayanan::defaults()['categories'];
      $categories = $ensure3($categories, $d);
      for($ci=0;$ci<3;$ci++){
          if(!isset($categories[$ci]['items']) || !is_array($categories[$ci]['items'])) $categories[$ci]['items'] = [];
      }
  }

  /**
   * Color field engine (1 value string; var/hex/custom)
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

  <form action="{{ route('profil.admin.layanan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Layanan</div>
        <div class="s">WA global dipakai untuk tombol WhatsApp di Hero & CTA. Warna: Brand/Black/Custom Hex (1 value).</div>
      </div>
      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.admin.layanan.edit') }}" class="btn btn-light border">Reset</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Pengaturan Layanan</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">

        <ul class="nav nav-pills rg-tabs gap-2" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-wa" type="button">WA Global</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-hero" type="button">Hero</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-summary" type="button">Summary</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-why" type="button">Why</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-categories" type="button">Categories</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-cta" type="button">CTA</button></li>
        </ul>

        <div class="tab-content pt-4">

          {{-- WA GLOBAL --}}
          <div class="tab-pane fade show active" id="pane-wa">
            <div class="rg-section-card">
              <div class="rg-section-title">WhatsApp Global</div>
              <div class="rg-section-sub">Nomor (62812...) atau link wa.me. Dipakai untuk tombol WhatsApp di Hero & CTA.</div>

              <label class="form-label fw-bold">WA Value</label>
              <input class="form-control" name="wa_value" value="{{ $ov('wa_value', $s->wa_value) }}" placeholder="62812xxxx atau https://wa.me/62xxxx">
            </div>
          </div>

          {{-- HERO --}}
          <div class="tab-pane fade" id="pane-hero">
            <div class="rg-section-card">
              <div class="rg-section-title">Hero</div>
              <div class="rg-section-sub">Chip, judul berwarna (repeatable), deskripsi, tombol.</div>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Chip Text</label>
                  <input class="form-control" name="hero_chip_text" value="{{ $ov('hero_chip_text', $s->hero_chip_text) }}">
                </div>
                <div class="col-12 col-lg-6">
                  {!! $colorField('hero_chip_dot', $ov('hero_chip_dot', $s->hero_chip_dot), 'hero_chip_dot', 'Chip Dot Color') !!}
                </div>
              </div>

              <hr class="my-4">

              <div class="rg-section-title">Judul Berwarna (Repeatable)</div>
              <div class="rg-section-sub">Maks 6 potongan.</div>

              <div id="heroTitleParts" data-max="6">
                @foreach($heroParts as $i => $p)
                  <div class="rg-repeat-row" data-repeat-row>
                    <div class="flex-grow-1">
                      <label class="form-label fw-bold mb-1">Teks</label>
                      <input type="text" class="form-control" name="hero_title_parts[{{ $i }}][text]" value="{{ $p['text'] ?? '' }}">
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

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddHeroTitlePart">+ Tambah Potongan</button>
              </div>

              <hr class="my-4">

              <label class="form-label fw-bold">Deskripsi</label>
              <textarea class="form-control" name="hero_desc" rows="3">{{ $ov('hero_desc', $s->hero_desc) }}</textarea>

              <hr class="my-4">

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol WhatsApp (Label)</label>
                  <input class="form-control" name="hero_btn1_text" value="{{ $ov('hero_btn1_text', $s->hero_btn1_text) }}">
                  <div class="form-text">Link otomatis pakai WA Global.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol 2 (Label)</label>
                  <input class="form-control" name="hero_btn2_text" value="{{ $ov('hero_btn2_text', $s->hero_btn2_text) }}">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol 2 (Link)</label>
                  <select class="form-select" name="hero_btn2_route">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('hero_btn2_route', $s->hero_btn2_route) === $r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

            </div>
          </div>

          {{-- SUMMARY --}}
          <div class="tab-pane fade" id="pane-summary">
            <div class="rg-section-card">
              <div class="rg-section-title">Summary</div>
              <div class="rg-section-sub">Judul + list item (repeatable) dengan dot warna.</div>

              <label class="form-label fw-bold">Judul</label>
              <input class="form-control" name="summary_title" value="{{ $ov('summary_title', $s->summary_title) }}">

              <hr class="my-4">

              <div id="summaryItems" data-max="8">
                @foreach($summaryItems as $i => $it)
                  <div class="rg-repeat-row" data-repeat-row>
                    <div class="flex-grow-1">
                      <label class="form-label fw-bold mb-1">Text</label>
                      <input class="form-control" name="summary_items[{{ $i }}][text]" value="{{ $it['text'] ?? '' }}">
                    </div>

                    <div style="min-width: 320px;">
                      {!! $colorField("summary_items[$i][dot]", ($it['dot'] ?? 'var(--rg-blue)'), "summary_dot_$i", 'Dot Color') !!}
                    </div>

                    <div>
                      <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                      <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddSummaryItem">+ Tambah Item</button>
              </div>
            </div>
          </div>

          {{-- WHY --}}
          <div class="tab-pane fade" id="pane-why">
            <div class="rg-section-card">
              <div class="rg-section-title">Why</div>
              <div class="rg-section-sub">Judul, deskripsi, dan 3 kartu (accent + image opsional).</div>

              <div class="row g-3">
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Judul</label>
                  <input class="form-control" name="why_title" value="{{ $ov('why_title', $s->why_title) }}">
                </div>
                <div class="col-12 col-lg-6">
                  <label class="form-label fw-bold">Deskripsi</label>
                  <input class="form-control" name="why_desc" value="{{ $ov('why_desc', $s->why_desc) }}">
                </div>
              </div>

              <hr class="my-4">

              <div class="row g-3">
                @for($i=0;$i<3;$i++)
                  @php $c = $whyCards[$i] ?? []; @endphp
                  <div class="col-12 col-lg-4">
                    <div class="rg-section-card" style="padding:14px;">
                      <div class="fw-bold mb-2">Kartu {{ $i+1 }}</div>

                      <label class="form-label fw-bold">Title</label>
                      <input class="form-control" name="why_cards[{{ $i }}][title]" value="{{ $c['title'] ?? '' }}">

                      <label class="form-label fw-bold mt-3">Desc</label>
                      <textarea class="form-control" rows="3" name="why_cards[{{ $i }}][desc]">{{ $c['desc'] ?? '' }}</textarea>

                      <div class="mt-3">
                        {!! $colorField("why_cards[$i][accent]", ($c['accent'] ?? 'var(--rg-blue)'), "why_acc_$i", 'Accent Color') !!}
                      </div>

                      <label class="form-label fw-bold mt-3">Image (opsional)</label>
                      <input class="form-control" type="file" name="why_cards[{{ $i }}][image]" accept="image/*">

                      @if(!empty($c['image']))
                        <div class="small text-muted mt-2">Current: <code>{{ $c['image'] }}</code></div>
                        <img src="{{ asset('storage/'.$c['image']) }}" style="width:100%;max-height:140px;object-fit:cover;border-radius:12px;border:1px solid rgba(0,0,0,.12);margin-top:8px;">
                      @endif
                    </div>
                  </div>
                @endfor
              </div>

            </div>
          </div>

          {{-- CATEGORIES --}}
          <div class="tab-pane fade" id="pane-categories">
            <div class="rg-section-card">
              <div class="rg-section-title">Categories (Fixed 3)</div>
              <div class="rg-section-sub">Outdoor / Indoor / Multi. Item repeatable + image opsional.</div>

              @for($ci=0;$ci<3;$ci++)
                @php $cat = $categories[$ci] ?? []; $items = $cat['items'] ?? []; if(!is_array($items)) $items=[]; @endphp

                <div class="rg-section-card mb-3" style="padding:14px;">
                  <div class="fw-bold mb-2">Kategori {{ $ci+1 }}</div>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Title</label>
                      <input class="form-control" name="categories[{{ $ci }}][title]" value="{{ $cat['title'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label fw-bold">Desc</label>
                      <input class="form-control" name="categories[{{ $ci }}][desc]" value="{{ $cat['desc'] ?? '' }}">
                    </div>
                  </div>

                  <hr class="my-4">

                  <div class="fw-bold mb-2">Items</div>

                  <div id="catItems-{{ $ci }}" data-max="12" data-ci="{{ $ci }}">
                    @foreach($items as $ii => $it)
                      <div class="rg-repeat-row" data-repeat-row>
                        <div class="flex-grow-1">
                          <label class="form-label fw-bold mb-1">Title</label>
                          <input class="form-control" name="categories[{{ $ci }}][items][{{ $ii }}][title]" value="{{ $it['title'] ?? '' }}">
                          <label class="form-label fw-bold mt-3 mb-1">Desc</label>
                          <textarea class="form-control" rows="2" name="categories[{{ $ci }}][items][{{ $ii }}][desc]">{{ $it['desc'] ?? '' }}</textarea>
                        </div>

                        <div style="min-width: 320px;">
                          <label class="form-label fw-bold mb-1">Image (opsional)</label>
                          <input class="form-control" type="file" name="categories[{{ $ci }}][items][{{ $ii }}][image]" accept="image/*">
                          <input type="hidden" name="categories[{{ $ci }}][items][{{ $ii }}][image_current]" value="{{ $it['image'] ?? '' }}">

                          @if(!empty($it['image']))
                            <div class="small text-muted mt-2">Current: <code>{{ $it['image'] }}</code></div>
                            <img src="{{ asset('storage/'.$it['image']) }}" style="width:100%;max-height:140px;object-fit:cover;border-radius:12px;border:1px solid rgba(0,0,0,.12);margin-top:8px;">
                          @endif
                        </div>

                        <div>
                          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-dark btn-sm fw-bold" data-add-cat-item="{{ $ci }}">+ Tambah Item</button>
                  </div>

                </div>
              @endfor

            </div>
          </div>

          {{-- CTA --}}
          <div class="tab-pane fade" id="pane-cta">
            <div class="rg-section-card">
              <div class="rg-section-title">CTA</div>
              <div class="rg-section-sub">Judul, deskripsi, tombol WA (pakai WA global), tombol 2 route.</div>

              <label class="form-label fw-bold">Judul</label>
              <input class="form-control" name="cta_title" value="{{ $ov('cta_title', $s->cta_title) }}">

              <label class="form-label fw-bold mt-3">Deskripsi</label>
              <textarea class="form-control" rows="3" name="cta_desc">{{ $ov('cta_desc', $s->cta_desc) }}</textarea>

              <hr class="my-4">

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol WhatsApp (Label)</label>
                  <input class="form-control" name="cta_btn1_text" value="{{ $ov('cta_btn1_text', $s->cta_btn1_text) }}">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol 2 (Label)</label>
                  <input class="form-control" name="cta_btn2_text" value="{{ $ov('cta_btn2_text', $s->cta_btn2_text) }}">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Tombol 2 (Link)</label>
                  <select class="form-select" name="cta_btn2_route">
                    @foreach($routes as $r)
                      <option value="{{ $r['value'] }}" @selected($ov('cta_btn2_route', $s->cta_btn2_route) === $r['value'])>{{ $r['label'] }}</option>
                    @endforeach
                  </select>
                </div>
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
  // ===== remove row =====
  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-remove]');
    if(!btn) return;
    const row = btn.closest('[data-repeat-row], .rg-repeat-row');
    if(row) row.remove();
  });

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

  initColorFields(document);

  // ===== add hero title part =====
  const heroWrap = document.getElementById('heroTitleParts');
  const btnAddHero = document.getElementById('btnAddHeroTitlePart');

  function addHeroPart(){
    if(!heroWrap) return;
    const max = parseInt(heroWrap.dataset.max || '6', 10);
    const rows = heroWrap.querySelectorAll('.rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = document.createElement('div');
    div.className = 'rg-repeat-row';
    div.setAttribute('data-repeat-row','1');

    div.innerHTML = `
      <div class="flex-grow-1">
        <label class="form-label fw-bold mb-1">Teks</label>
        <input type="text" class="form-control" name="hero_title_parts[${idx}][text]" value="">
      </div>
      <div style="min-width: 320px;">
        <div class="rg-color rg-color--compact" data-color-field>
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

    heroWrap.appendChild(div);
    initColorFields(div);
  }

  if(btnAddHero) btnAddHero.addEventListener('click', addHeroPart);

  // ===== add summary item =====
  const sumWrap = document.getElementById('summaryItems');
  const btnAddSum = document.getElementById('btnAddSummaryItem');

  function addSummaryItem(){
    if(!sumWrap) return;
    const max = parseInt(sumWrap.dataset.max || '8', 10);
    const rows = sumWrap.querySelectorAll('.rg-repeat-row');
    const idx = rows.length;
    if(idx >= max) return;

    const div = document.createElement('div');
    div.className = 'rg-repeat-row';
    div.setAttribute('data-repeat-row','1');

    div.innerHTML = `
      <div class="flex-grow-1">
        <label class="form-label fw-bold mb-1">Text</label>
        <input class="form-control" name="summary_items[${idx}][text]" value="">
      </div>
      <div style="min-width: 320px;">
        <div class="rg-color rg-color--compact" data-color-field>
          <label class="form-label fw-bold mb-1">Dot Color</label>
          <input type="hidden" name="summary_items[${idx}][dot]" value="var(--rg-blue)" data-color-hidden>

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
    sumWrap.appendChild(div);
    initColorFields(div);
  }

  if(btnAddSum) btnAddSum.addEventListener('click', addSummaryItem);

  // ===== add category item (fixed 3 category) =====
  document.querySelectorAll('[data-add-cat-item]').forEach(btn => {
    btn.addEventListener('click', () => {
      const ci = parseInt(btn.getAttribute('data-add-cat-item'), 10);
      const wrap = document.getElementById('catItems-' + ci);
      if(!wrap) return;

      const max = parseInt(wrap.dataset.max || '12', 10);
      const rows = wrap.querySelectorAll('.rg-repeat-row');
      const idx = rows.length;
      if(idx >= max) return;

      const div = document.createElement('div');
      div.className = 'rg-repeat-row';
      div.setAttribute('data-repeat-row','1');

      div.innerHTML = `
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Title</label>
          <input class="form-control" name="categories[${ci}][items][${idx}][title]" value="">
          <label class="form-label fw-bold mt-3 mb-1">Desc</label>
          <textarea class="form-control" rows="2" name="categories[${ci}][items][${idx}][desc]"></textarea>
        </div>

        <div style="min-width: 320px;">
          <label class="form-label fw-bold mb-1">Image (opsional)</label>
          <input class="form-control" type="file" name="categories[${ci}][items][${idx}][image]" accept="image/*">
          <input type="hidden" name="categories[${ci}][items][${idx}][image_current]" value="">
          <div class="small text-muted mt-2">Belum ada image</div>
        </div>

        <div>
          <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
          <button type="button" class="btn btn-light border" data-remove>Hapus</button>
        </div>
      `;
      wrap.appendChild(div);
    });
  });

})();
</script>
@endsection
