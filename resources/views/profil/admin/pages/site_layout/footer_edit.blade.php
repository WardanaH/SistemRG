@extends('profil.admin.layouts.app')

@section('title', 'Edit Footer')
@section('page_title', 'Edit Footer')

@section('content')
@php
  /** @var \App\Models\PSiteLayout $layout */
  $footer = $layout->footer ?? [];
  if (!is_array($footer)) $footer = [];

  $brandParts = $footer['brand_parts'] ?? [
    ['text'=>'Restu', 'color'=>'var(--rg-blue)'],
    ['text'=>' Guru', 'color'=>'var(--rg-yellow)'],
    ['text'=>' Promosindo', 'color'=>'var(--rg-red)'],
  ];
  if (!is_array($brandParts)) $brandParts = [];

  $tagline = $footer['tagline'] ?? "Percetakan & Advertising\nOutdoor • Indoor • Multi";

  $services = $footer['services'] ?? [];
  if (!is_array($services)) $services = [];

  $branches = $footer['branches'] ?? [];
  if (!is_array($branches)) $branches = [];

  $socials = $footer['socials'] ?? [];
  if (!is_array($socials)) $socials = [];

  $wa = $footer['wa'] ?? 'https://wa.me/6281234567890';
  $email = $footer['email'] ?? 'info@promosindo.com';

  $copyrightLeft = $footer['copyright_left'] ?? ('© '.date('Y').' Restu Guru Promosindo');
  $copyrightRight = $footer['copyright_right'] ?? 'Percetakan • Digital Printing • Advertising';

  $colorModes = [
      ['label' => 'Brand Blue',   'value' => 'var(--rg-blue)'],
      ['label' => 'Brand Yellow', 'value' => 'var(--rg-yellow)'],
      ['label' => 'Brand Red',    'value' => 'var(--rg-red)'],
      ['label' => 'Black',        'value' => '#000000'],
      ['label' => 'Custom Hex',   'value' => 'custom'],
  ];

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
  .rg-page-wrap{ max-width: 1100px; margin:0 auto; }
  .rg-card-shell{ border:1px solid rgba(226,232,240,.95); border-radius:18px; background:#fff; box-shadow:0 10px 26px rgba(15,23,42,.06); overflow:hidden; }
  .rg-card-head{ padding:16px 18px; border-bottom:1px solid rgba(226,232,240,.95); background: rgba(248,250,252,.65); display:flex; justify-content:space-between; gap:12px; }
  .rg-card-body{ padding:18px; }
  .rg-section{ border:1px solid rgba(226,232,240,.95); border-radius:16px; padding:16px; background:#fff; }
  .rg-title{ font-weight:900; margin-bottom:2px; }
  .rg-sub{ color:#64748b; font-size:.9rem; line-height:1.6; margin-bottom:14px; }

  .rg-row{ display:flex; gap:12px; align-items:flex-start; padding:12px; border:1px solid rgba(226,232,240,.95); border-radius:14px; background:rgba(248,250,252,.55); margin-bottom:10px; }
  @media(max-width:991.98px){ .rg-row{ flex-direction:column; } }

  /* COLOR FIELD */
  .rg-color__row{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .rg-color__select{ width: 170px; min-width:170px; font-weight:800; }
  .rg-color__swatch-wrap{ position:relative; width:54px; height:40px; flex:0 0 54px; }
  .rg-color__picker{ width:54px; height:40px; padding:.2rem; opacity:0; position:absolute; inset:0; cursor:pointer; }
  .rg-color__swatch{ display:block; width:54px; height:40px; border-radius:10px; border:1px solid rgba(226,232,240,.95); box-shadow: inset 0 0 0 1px rgba(15,23,42,.05); background:#000; }
  .rg-color__hex{ width:160px; min-width:160px; font-weight:800; letter-spacing:.02em; }
  @media(max-width:575.98px){
    .rg-color__select, .rg-color__hex{ width:100%; min-width:0; }
  }
</style>

<div class="rg-page-wrap">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
    <div>
      <div style="font-weight:900; font-size:1.35rem;">Edit Footer</div>
      <div class="text-muted" style="line-height:1.6;">
        Edit konten footer: brand, tagline, layanan, cabang, kontak.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('profil.admin.tampilan.footer.edit') }}" class="btn btn-light border fw-bold">Reset</a>
      <button form="footerForm" type="submit" class="btn btn-dark fw-bold">Simpan</button>
    </div>
  </div>

  <div class="rg-card-shell">
    <div class="rg-card-head">
      <div class="fw-bold">Pengaturan Footer</div>
      <div class="text-muted small">Tersimpan ke <code>p_site_layouts</code> (row id=1).</div>
    </div>

    <div class="rg-card-body">
      <form id="footerForm" action="{{ route('profil.admin.tampilan.footer.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rg-section mb-3">
          <div class="rg-title">Brand Text (Berwarna)</div>
          <div class="rg-sub">Maks 6 bagian.</div>

          <div id="footBrandWrap" data-max="6">
            @foreach($brandParts as $i => $p)
              <div class="rg-row" data-repeat-row>
                <div class="flex-grow-1">
                  <label class="form-label fw-bold mb-1">Teks</label>
                  <input class="form-control" name="footer[brand_parts][{{ $i }}][text]" value="{{ $p['text'] ?? '' }}">
                </div>

                <div style="min-width:320px;">
                  {!! $colorField("footer[brand_parts][$i][color]", ($p['color'] ?? 'var(--rg-blue)'), "foot_brand_$i", 'Warna') !!}
                </div>

                <div>
                  <label class="form-label fw-bold mb-1 d-none d-lg-block">&nbsp;</label>
                  <button type="button" class="btn btn-light border fw-bold" data-remove>Hapus</button>
                </div>
              </div>
            @endforeach
          </div>

          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-dark btn-sm fw-bold" id="btnAddFootBrand">+ Tambah Bagian</button>
          </div>
        </div>

        <div class="rg-section mb-3">
          <div class="rg-title">Tagline</div>
          <div class="rg-sub">Boleh pakai enter (baris baru).</div>
          <textarea class="form-control" name="footer[tagline]" rows="3">{{ $tagline }}</textarea>
        </div>

        <div class="rg-section mb-3">
          <div class="rg-title">Kontak</div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold">WhatsApp Link</label>
              <input class="form-control" name="footer[wa]" value="{{ $wa }}">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold">Email</label>
              <input class="form-control" name="footer[email]" value="{{ $email }}">
            </div>
          </div>
        </div>

        <div class="rg-section">
          <div class="rg-title">Copyright</div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold">Left</label>
              <input class="form-control" name="footer[copyright_left]" value="{{ $copyrightLeft }}">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-bold">Right</label>
              <input class="form-control" name="footer[copyright_right]" value="{{ $copyrightRight }}">
            </div>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
(function(){
  document.addEventListener('click', function(e){
    const btn = e.target.closest('[data-remove]');
    if(!btn) return;
    const row = btn.closest('[data-repeat-row]');
    if(row) row.remove();
  });

  function el(html){
    const t = document.createElement('template');
    t.innerHTML = html.trim();
    return t.content.firstElementChild;
  }

  // ===== COLOR FIELD ENGINE (same as navbar) =====
  function isHex(v){ return /^#([0-9a-fA-F]{6})$/.test((v||'').trim()); }
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

  // add brand part
  const fb = document.getElementById('footBrandWrap');
  const btnFb = document.getElementById('btnAddFootBrand');
  if(btnFb) btnFb.addEventListener('click', function(){
    const max = parseInt(fb.dataset.max || '6', 10);
    const idx = fb.querySelectorAll('[data-repeat-row]').length;
    if(idx >= max) return;

    fb.appendChild(el(`
      <div class="rg-row" data-repeat-row>
        <div class="flex-grow-1">
          <label class="form-label fw-bold mb-1">Teks</label>
          <input class="form-control" name="footer[brand_parts][${idx}][text]" value="">
        </div>

        <div style="min-width:320px;">
          <div class="rg-color rg-color--compact" data-color-field id="cf-foot_brand_${idx}">
            <label class="form-label fw-bold mb-1">Warna</label>
            <input type="hidden" name="footer[brand_parts][${idx}][color]" value="var(--rg-blue)" data-color-hidden>

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
          <button type="button" class="btn btn-light border fw-bold" data-remove>Hapus</button>
        </div>
      </div>
    `));

    initColorFields(fb);
  });

  initColorFields(document);
})();
</script>
@endsection
