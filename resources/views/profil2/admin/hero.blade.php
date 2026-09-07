@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">ATUR TEKS HERO</h1>
</div>

@if(session('success'))
    <div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background-color: var(--neon-g);">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="brutal-card p-4 p-md-5 mb-5" style="background-color: #fafafa;">
    <p class="fs-5 fw-bold mb-4">Ganti tulisan ganal nang ada di bagian atas tiap halaman (Beranda, Layanan, Produk, dll) di mari.</p>

    <form action="{{ route('profil2.hero.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="accordion" id="accordionHero" style="border: 4px solid #000; box-shadow: 6px 6px 0px #000;">

            @foreach($heroes as $index => $hero)
            <div class="accordion-item" style="border: none; border-bottom: 4px solid #000;">
                <h2 class="accordion-header" id="heading{{ $hero->id }}">
                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} fw-bold fs-5 text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $hero->id }}" style="background-color: {{ $index % 2 == 0 ? 'var(--neon-c)' : 'var(--neon-y)' }}; color: #000; border: none;">
                        <i class="fa-solid fa-file-lines me-2"></i> HALAMAN: {{ $hero->halaman }}
                    </button>
                </h2>
                <div id="collapse{{ $hero->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#accordionHero">
                    <div class="accordion-body" style="background: #fff;">

                        <div class="mb-3">
                            <label class="fw-bold mb-2">Judul Utama</label>
                            <input type="text" name="heroes[{{ $hero->id }}][judul_utama]" class="form-control brutal-input" value="{{ $hero->judul_utama }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="fw-bold mb-2">Sub-judul / Paragraf Handap</label>
                            <textarea name="heroes[{{ $hero->id }}][sub_judul]" class="form-control brutal-input" rows="3">{{ $hero->sub_judul }}</textarea>
                        </div>

                        <div class="mb-2 p-3 border border-dark border-2 bg-light">
                            <label class="fw-bold mb-2">Gambar Background</label>
                            @if($hero->gambar_background)
                                <div class="mb-3">
                                    <img src="{{ asset($hero->gambar_background) }}" class="img-fluid border border-2 border-dark" style="max-height: 120px;" alt="Background {{ $hero->halaman }}">
                                </div>
                            @endif
                            <input type="file" name="heroes[{{ $hero->id }}][gambar_background]" class="form-control brutal-input bg-white" accept="image/*">
                            <small class="fw-bold text-secondary mt-1 d-block">*Kusungakan amun kada handak mangganti gambar nang ada.</small>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="mt-5 mb-2 position-sticky bottom-0 bg-white p-3 border border-dark border-4" style="box-shadow: 0px -5px 15px rgba(0,0,0,0.1); z-index: 10;">
            <button type="submit" class="brutal-btn w-100 fs-4 py-3" style="background: var(--neon-m); color: #fff;">
                <i class="fa-solid fa-floppy-disk me-2"></i> SIMPAN TEKS HERO
            </button>
        </div>

    </form>
</div>

@endsection
