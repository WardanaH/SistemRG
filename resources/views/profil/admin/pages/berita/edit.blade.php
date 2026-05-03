{{-- resources/views/profil/admin/pages/berita/edit.blade.php --}}
@extends('profil.admin.layouts.app')

@section('title', 'Edit Berita')
@section('page_title', 'Edit Berita')

@section('content')
@php
  /** @var \App\Models\PBerita $berita */
  $ov = fn($key, $default = '') => old($key, $default);
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

  .rg-preview-img{
    width: 100%;
    max-height: 220px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid rgba(0,0,0,.12);
    margin-top: 10px;
  }

  .rg-help{ font-size:.88rem; color:#64748b; line-height:1.6; }

  .form-control, .form-select{
    border-radius: 12px;
    font-weight: 800;
  }

  .rg-content-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
  .rg-btn-editcontent{
    display:inline-flex; align-items:center; gap:10px;
    border-radius: 12px; padding: .62rem 1rem;
    font-weight: 900;
  }
  .rg-content-preview{
    margin-top: 10px;
    border:1px dashed rgba(226,232,240,.95);
    border-radius: 14px;
    padding: 12px;
    background: rgba(248,250,252,.6);
    color:#0f172a;
  }
  .rg-content-preview .muted{ color:#64748b; font-size:.88rem; line-height:1.6; }
  .rg-content-preview .lines{
    margin-top: 8px;
    font-size: .95rem;
    line-height: 1.75;
    max-height: 130px;
    overflow:auto;
    white-space: pre-wrap;
  }

  .rg-modal-editor .modal-content{
    border:0;
    border-radius: 18px;
    overflow:hidden;
    box-shadow: 0 18px 60px rgba(15,23,42,.22);
  }
  .rg-modal-editor .modal-header{
    background: rgba(248,250,252,.9);
    border-bottom:1px solid rgba(226,232,240,.95);
  }
  .rg-modal-editor .modal-title{ font-weight: 950; letter-spacing:-.01em; }

  .rg-editor{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 14px;
    background:#fff;
    overflow:hidden;
    box-shadow: 0 10px 22px rgba(15,23,42,.04);
  }
  .rg-editor__bar{
    display:flex; flex-wrap:wrap; gap:10px; align-items:center;
    padding:10px;
    border-bottom:1px solid rgba(226,232,240,.95);
    background: rgba(248,250,252,.85);
    position: sticky; top: 0; z-index: 5;
  }
  .rg-editor__group{
    display:flex; gap:6px; align-items:center;
    padding-right: 10px; margin-right: 10px;
    border-right: 1px solid rgba(226,232,240,.95);
  }
  .rg-editor__group:last-child{ border-right: 0; margin-right: 0; padding-right: 0; }

  .rg-edbtn{
    border:1px solid rgba(226,232,240,.95);
    background:#fff;
    border-radius: 10px;
    padding: 9px 10px;
    font-weight: 950;
    line-height: 1;
    cursor:pointer;
    user-select:none;
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    min-width: 42px;
    text-align: center;
  }
  .rg-edbtn:hover{ transform: translateY(-1px); box-shadow: 0 10px 18px rgba(15,23,42,.06); }
  .rg-edbtn.is-active{ background:#0f172a; color:#fff; border-color:#0f172a; }

  .rg-edselect{
    border:1px solid rgba(226,232,240,.95);
    border-radius: 10px;
    padding: 9px 10px;
    font-weight: 950;
    background:#fff;
    cursor:pointer;
    min-width: 170px;
  }
  .rg-edselect--sm{ min-width: 160px; }

  .rg-editor__body{ padding: 14px; }

  .rg-editor__area{
    min-height: 58vh;
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 12px;
    padding: 14px 14px;
    outline: none;
    background: #fff;
    font-weight: 650;
    line-height: 1.85;
    color: #0f172a;
  }

  .rg-editor__area p{ margin: 0 0 10px; }
  .rg-editor__area h2, .rg-editor__area h3, .rg-editor__area h4{
    margin: 14px 0 10px;
    line-height: 1.25;
    font-weight: 950;
    letter-spacing: -.01em;
  }
  .rg-editor__area ul, .rg-editor__area ol{ padding-left: 1.25rem; margin: 0 0 12px; }
  .rg-editor__area li{ margin: 4px 0; }
  .rg-editor__area blockquote{
    margin: 12px 0;
    padding: 10px 12px;
    border-left: 4px solid rgba(15,23,42,.18);
    background: rgba(248,250,252,.8);
    border-radius: 10px;
  }
  .rg-editor__area code{
    background: rgba(15,23,42,.06);
    padding: 2px 6px;
    border-radius: 8px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: .95em;
  }
  .rg-editor__area hr{
    border:0;
    height:1px;
    background: rgba(226,232,240,.95);
    margin: 14px 0;
  }

  .rg-editor__area span[data-fs="8"]{ font-size: 8pt; }
  .rg-editor__area span[data-fs="9"]{ font-size: 9pt; }
  .rg-editor__area span[data-fs="10"]{ font-size: 10pt; }
  .rg-editor__area span[data-fs="11"]{ font-size: 11pt; }
  .rg-editor__area span[data-fs="12"]{ font-size: 12pt; }
  .rg-editor__area span[data-fs="14"]{ font-size: 14pt; }
  .rg-editor__area span[data-fs="16"]{ font-size: 16pt; }
  .rg-editor__area span[data-fs="18"]{ font-size: 18pt; }
  .rg-editor__area span[data-fs="24"]{ font-size: 24pt; }
  .rg-editor__area span[data-fs="36"]{ font-size: 36pt; }

  .rg-editor__area [data-pa="0"]{ margin-bottom: 0 !important; }
  .rg-editor__area [data-pa="6"]{ margin-bottom: 6pt !important; }
  .rg-editor__area [data-pa="12"]{ margin-bottom: 12pt !important; }

  .rg-editor__hint{
    display:flex; gap:10px; align-items:flex-start;
    margin-top: 10px;
    padding: 10px 12px;
    border-radius: 12px;
    background: rgba(239,246,255,.9);
    border: 1px solid rgba(191,219,254,.8);
    color:#0f172a;
    font-size: .9rem;
    line-height: 1.6;
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

  <form id="newsForm" action="{{ route('profil.admin.berita.update', ['beritum'=>$berita->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rg-topbar">
      <div>
        <div class="t">Edit Berita</div>
        <div class="s">
          Kamu sedang mengedit: <b>{{ $berita->title }}</b> — slug: <code>{{ $berita->slug }}</code>.
        </div>
      </div>

      <div class="rg-actions d-flex gap-2">
        <a href="{{ route('profil.admin.berita.index') }}" class="btn btn-light border">Kembali</a>
        <a href="{{ route('profil.berita.show', ['slug'=>$berita->slug]) }}" target="_blank" class="btn btn-light border">Preview</a>
        <button type="submit" class="btn btn-dark">Simpan</button>
      </div>
    </div>

    <div class="rg-card-shell">
      <div class="rg-card-head">
        <div class="fw-bold">Form Berita</div>
        <div class="text-muted small">Klik <b>Simpan</b> setelah edit.</div>
      </div>

      <div class="rg-card-body">
        <div class="row g-3">
          <div class="col-12 col-lg-8">
            <div class="rg-section-card">
              <div class="rg-section-title">Konten</div>
              <div class="rg-section-sub">Judul, slug (opsional), ringkasan, dan isi konten (popup editor).</div>

              <label class="form-label fw-bold">Tipe</label>
              <select class="form-select" name="type">
                <option value="news" @selected($ov('type', $berita->type)==='news')>Berita</option>
                <option value="education" @selected($ov('type', $berita->type)==='education')>Edukasi</option>
              </select>

              <div class="row g-3 mt-1">
                <div class="col-12">
                  <label class="form-label fw-bold">Judul</label>
                  <input class="form-control" name="title" value="{{ $ov('title', $berita->title) }}">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Slug (opsional)</label>
                  <input class="form-control" name="slug" value="{{ $ov('slug', $berita->slug) }}">
                  <div class="rg-help mt-1">Kalau kosong, slug otomatis dari judul (dibuat unik).</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label fw-bold">Kategori Label (opsional, untuk Edukasi)</label>
                  <input class="form-control" name="category_label" value="{{ $ov('category_label', $berita->category_label) }}">
                </div>

                <div class="col-12">
                  <label class="form-label fw-bold">Ringkasan (Excerpt)</label>
                  <textarea class="form-control" name="excerpt" rows="3">{{ $ov('excerpt', $berita->excerpt) }}</textarea>
                </div>

                <div class="col-12">
                  <label class="form-label fw-bold">Isi Konten</label>

                  {{-- hidden real field --}}
                  <textarea id="contentHidden" class="d-none" name="content">{{ $ov('content', $berita->content) }}</textarea>

                  <div class="rg-content-actions">
                    <button type="button"
                            class="btn btn-dark rg-btn-editcontent"
                            data-bs-toggle="modal"
                            data-bs-target="#rgContentModal">
                      <i class="bi bi-pencil-square"></i>
                      Edit Konten
                    </button>

                    <div class="rg-help">
                      Konten dibuka di popup editor. Hasil disimpan sebagai HTML yang clean.
                    </div>
                  </div>

                  <div class="rg-content-preview" id="rgContentPreview">
                    <div class="muted">
                      Preview singkat (bukan tampilan final). Untuk edit, klik <b>Edit Konten</b>.
                    </div>
                    <div class="lines" id="rgContentPreviewLines"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4">
            <div class="rg-section-card">
              <div class="rg-section-title">Publikasi</div>
              <div class="rg-section-sub">Atur publish date, status, dan cover image.</div>

              @php
                $dtLocal = '';
                if(!empty($berita->published_at)){
                  $dtLocal = $berita->published_at->format('Y-m-d\TH:i');
                }
              @endphp

              <label class="form-label fw-bold">Publish At (opsional)</label>
              <input type="datetime-local" class="form-control" name="published_at" value="{{ $ov('published_at', $dtLocal) }}">

              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="1" id="isPublished" name="is_published"
                  @checked(old('is_published', $berita->is_published))>
                <label class="form-check-label fw-bold" for="isPublished">Published</label>
              </div>

              <hr class="my-4">

              <label class="form-label fw-bold">Cover Image (opsional)</label>
              <input type="file" class="form-control" name="cover" accept="image/*" id="coverInput">

              <input type="hidden" name="cover_current" value="{{ $berita->cover ?? '' }}">

              @if(!empty($berita->cover_url))
                <img id="coverPreview" class="rg-preview-img" src="{{ $berita->cover_url }}" alt="cover">
              @else
                <img id="coverPreview" class="rg-preview-img" alt="cover-preview" style="display:none;">
              @endif

              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="1" id="removeCover" name="remove_cover" @checked(old('remove_cover', false))>
                <label class="form-check-label fw-bold" for="removeCover">Hapus cover</label>
              </div>

              <div class="rg-help mt-2">
                Upload cover baru = replace cover lama. Centang “Hapus cover” = cover jadi kosong.
              </div>

              <hr class="my-4">

              {{-- ✅ Tombol delete dipindah keluar form utama (lihat bawah) --}}
              <button type="button" class="btn btn-danger w-100" style="border-radius:12px; font-weight:900;"
                      onclick="document.getElementById('deleteBeritaForm').requestSubmit()">
                Hapus
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </form>

  {{-- ✅ FORM DELETE TERPISAH (HTML valid) --}}
  <form id="deleteBeritaForm" method="POST"
        action="{{ route('profil.admin.berita.destroy', ['beritum'=>$berita->id]) }}"
        onsubmit="return confirm('Hapus konten ini?')">
    @csrf
    @method('DELETE')
  </form>
</div>

{{-- MODAL: CONTENT EDITOR --}}
<div class="modal fade rg-modal-editor" id="rgContentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="modal-title">Isi Konten</div>
          <div class="text-muted small" style="line-height:1.6;">Toolbar Word-like: font size angka + spacing + justify.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

      <div class="modal-body">
        <div class="rg-editor" data-rg-editor>
          <div class="rg-editor__bar">
            <div class="rg-editor__group">
              <select class="rg-edselect rg-edselect--sm" data-action="fontSize" title="Font size (Word)">
                <option value="">Font: (pilih)</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="14">14</option>
                <option value="16">16</option>
                <option value="18">18</option>
                <option value="24">24</option>
                <option value="36">36</option>
              </select>

              <select class="rg-edselect rg-edselect--sm" data-action="paraAfter" title="Paragraph spacing after">
                <option value="">Spacing: (pilih)</option>
                <option value="0">After 0</option>
                <option value="6">After 6</option>
                <option value="12">After 12</option>
              </select>
            </div>

            <div class="rg-editor__group">
              <button class="rg-edbtn" type="button" data-cmd="bold" title="Bold (Ctrl+B)">B</button>
              <button class="rg-edbtn" type="button" data-cmd="italic" title="Italic (Ctrl+I)"><i>I</i></button>
              <button class="rg-edbtn" type="button" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
              <button class="rg-edbtn" type="button" data-cmd="removeFormat" title="Clear format">Tx</button>
            </div>

            <div class="rg-editor__group">
              <button class="rg-edbtn" type="button" data-cmd="insertUnorderedList" title="Bullet list">•</button>
              <button class="rg-edbtn" type="button" data-cmd="insertOrderedList" title="Numbered list">1.</button>
              <button class="rg-edbtn" type="button" data-cmd="outdent" title="Outdent">←</button>
              <button class="rg-edbtn" type="button" data-cmd="indent" title="Indent">→</button>
            </div>

            <div class="rg-editor__group">
              <button class="rg-edbtn" type="button" data-cmd="justifyLeft" title="Align left">≡</button>
              <button class="rg-edbtn" type="button" data-cmd="justifyCenter" title="Align center">≣</button>
              <button class="rg-edbtn" type="button" data-cmd="justifyRight" title="Align right">≡→</button>
              <button class="rg-edbtn" type="button" data-cmd="justifyFull" title="Justify">⫶</button>
            </div>

            <div class="rg-editor__group">
              <button class="rg-edbtn" type="button" data-cmd="undo" title="Undo (Ctrl+Z)">↶</button>
              <button class="rg-edbtn" type="button" data-cmd="redo" title="Redo (Ctrl+Y)">↷</button>
            </div>
          </div>

          <div class="rg-editor__body">
            <div class="rg-editor__area" contenteditable="true" data-area></div>

            <div class="rg-editor__hint">
              <div>💡</div>
              <div>Paste dari Word/Docs boleh. Sistem rapikan HTML supaya clean.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light border fw-bold" type="button" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-dark fw-bold" type="button" id="rgContentSaveBtn">
          <i class="bi bi-check2-circle me-1"></i> Simpan Konten
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  // =========================
  // COVER PREVIEW + REMOVE
  // =========================
  const input = document.getElementById('coverInput');
  const img = document.getElementById('coverPreview');
  const remove = document.getElementById('removeCover');

  if(input && img){
    input.addEventListener('change', function(){
      const f = input.files && input.files[0];
      if(!f) return;
      const url = URL.createObjectURL(f);
      img.src = url;
      img.style.display = '';
      if(remove) remove.checked = false;
    });
  }
  if(remove && input){
    remove.addEventListener('change', function(){
      if(remove.checked){
        input.value = '';
      }
    });
  }

  // =========================
  // RG MODAL EDITOR ENGINE
  // =========================
  const form   = document.getElementById('newsForm');
  const hidden = document.getElementById('contentHidden');

  const modalEl = document.getElementById('rgContentModal');
  const saveBtn = document.getElementById('rgContentSaveBtn');

  const editor = modalEl ? modalEl.querySelector('[data-rg-editor]') : null;
  const area   = editor ? editor.querySelector('[data-area]') : null;

  const previewLines = document.getElementById('rgContentPreviewLines');

  if(!form || !hidden || !modalEl || !saveBtn || !editor || !area) return;

  const ALLOWED_TAGS = new Set([
    'P','BR','B','STRONG','I','EM','U','S','DEL',
    'H2','H3','H4',
    'UL','OL','LI',
    'BLOCKQUOTE',
    'A',
    'HR',
    'CODE',
    'SPAN'
  ]);

  function sanitizeHtml(html){
    const tpl = document.createElement('template');
    tpl.innerHTML = html || '';

    const walk = (node) => {
      if(node.nodeType === Node.ELEMENT_NODE){
        const tag = node.tagName;

        if(!ALLOWED_TAGS.has(tag)){
          const parent = node.parentNode;
          if(parent){
            while(node.firstChild) parent.insertBefore(node.firstChild, node);
            parent.removeChild(node);
          }
          return;
        }

        const attrs = Array.from(node.attributes || []);
        attrs.forEach(a => {
          const n = a.name.toLowerCase();

          if(n.startsWith('on') || n === 'style' || n === 'class' || n === 'id'){
            node.removeAttribute(a.name);
            return;
          }

          if(tag === 'A' && n === 'href'){
            const v = String(a.value || '').trim();
            if(!/^https?:\/\//i.test(v)){
              node.removeAttribute('href');
            }else{
              node.setAttribute('target','_blank');
              node.setAttribute('rel','noopener noreferrer');
            }
            return;
          }

          if(tag === 'SPAN' && n === 'data-fs'){
            const v = String(a.value || '').trim();
            if(!['8','9','10','11','12','14','16','18','24','36'].includes(v)){
              node.removeAttribute('data-fs');
            }
            return;
          }

          if(['P','LI','BLOCKQUOTE','H2','H3','H4'].includes(tag) && n === 'data-pa'){
            const v = String(a.value || '').trim();
            if(!['0','6','12'].includes(v)){
              node.removeAttribute('data-pa');
            }
            return;
          }

          node.removeAttribute(a.name);
        });

        if(tag === 'SPAN'){
          const has = node.hasAttribute('data-fs');
          if(!has){
            const parent = node.parentNode;
            if(parent){
              while(node.firstChild) parent.insertBefore(node.firstChild, node);
              parent.removeChild(node);
            }
            return;
          }
        }
      }

      Array.from(node.childNodes || []).forEach(walk);
    };

    walk(tpl.content);

    let out = tpl.innerHTML.trim();
    out = out.replace(/(<br>\s*){3,}/gi, '<br><br>');
    if(out === '') out = '<p></p>';
    return out;
  }

  function htmlToPreviewText(html){
    const div = document.createElement('div');
    div.innerHTML = html || '';
    const text = (div.textContent || '').trim();
    return text || '— masih kosong —';
  }

  function renderPreview(){
    const cleaned = sanitizeHtml(hidden.value || '');
    hidden.value = cleaned;

    const text = htmlToPreviewText(cleaned);
    const lines = text.split('\n').map(s => s.trim()).filter(Boolean).slice(0, 10);
    previewLines.textContent = lines.length ? lines.join('\n') : '— masih kosong —';
  }

  renderPreview();

  function runCmd(cmd, val=null){
    area.focus();
    document.execCommand(cmd, false, val);
    refreshActive();
  }

  function wrapFontSize(fs){
    area.focus();
    const sel = window.getSelection();
    if(!sel || sel.rangeCount === 0) return;
    const range = sel.getRangeAt(0);
    if(range.collapsed) return;

    const span = document.createElement('span');
    span.setAttribute('data-fs', String(fs));

    try{
      const frag = range.extractContents();
      span.appendChild(frag);
      range.insertNode(span);

      range.setStartAfter(span);
      range.setEndAfter(span);
      sel.removeAllRanges();
      sel.addRange(range);
    }catch(e){
      const txt = range.toString();
      range.deleteContents();
      span.textContent = txt;
      range.insertNode(span);
    }
    refreshActive();
  }

  function setParagraphAfter(val){
    area.focus();
    const sel = window.getSelection();
    if(!sel || sel.rangeCount === 0) return;
    const node = sel.anchorNode;
    if(!node) return;

    const el = (node.nodeType === 1 ? node : node.parentElement);
    if(!el) return;

    const block = el.closest('p, li, blockquote, h2, h3, h4');
    if(!block) return;

    block.setAttribute('data-pa', String(val));
    refreshActive();
  }

  editor.addEventListener('click', function(e){
    const btn = e.target.closest('button');
    if(!btn) return;
    const cmd = btn.getAttribute('data-cmd');
    if(cmd){
      e.preventDefault();
      runCmd(cmd);
    }
  });

  const selFs = editor.querySelector('select[data-action="fontSize"]');
  if(selFs){
    selFs.addEventListener('change', function(){
      const v = selFs.value;
      if(v) wrapFontSize(v);
      selFs.value = '';
    });
  }

  const selPa = editor.querySelector('select[data-action="paraAfter"]');
  if(selPa){
    selPa.addEventListener('change', function(){
      const v = selPa.value;
      if(v !== '') setParagraphAfter(v);
      selPa.value = '';
    });
  }

  function refreshActive(){
    const map = [
      ['bold','bold'],
      ['italic','italic'],
      ['underline','underline'],
      ['insertUnorderedList','insertUnorderedList'],
      ['insertOrderedList','insertOrderedList'],
      ['justifyLeft','justifyLeft'],
      ['justifyCenter','justifyCenter'],
      ['justifyRight','justifyRight'],
      ['justifyFull','justifyFull'],
    ];

    map.forEach(([cmd, name]) => {
      const btn = editor.querySelector(`button[data-cmd="${cmd}"]`);
      if(!btn) return;
      try{
        const st = document.queryCommandState(name);
        btn.classList.toggle('is-active', !!st);
      }catch(e){}
    });
  }

  area.addEventListener('keyup', refreshActive);
  area.addEventListener('mouseup', refreshActive);
  area.addEventListener('focus', refreshActive);

  area.addEventListener('paste', function(){
    setTimeout(() => {
      area.innerHTML = sanitizeHtml(area.innerHTML);
    }, 0);
  });

  modalEl.addEventListener('show.bs.modal', function(){
    area.innerHTML = (hidden.value || '').trim() || '<p></p>';
    refreshActive();
    setTimeout(() => area.focus(), 50);
  });

  saveBtn.addEventListener('click', function(){
    const cleaned = sanitizeHtml(area.innerHTML);
    area.innerHTML = cleaned;
    hidden.value = cleaned;
    renderPreview();

    const modal = bootstrap.Modal.getInstance(modalEl);
    if(modal) modal.hide();
  });

  form.addEventListener('submit', function(){
    hidden.value = sanitizeHtml(hidden.value || '');
  });

})();
</script>
@endsection
