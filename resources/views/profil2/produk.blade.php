@extends('profil2.layout.app')

@section('content')

<style>
    .kategori-link {
        display: block;
        padding: 10px 15px;
        color: #000;
        text-decoration: none;
        font-weight: bold;
        border-bottom: 2px solid #000;
        transition: all 0.2s;
    }
    .kategori-link:hover, .kategori-link.active {
        background-color: var(--neon-y);
        padding-left: 25px;
    }
    .produk-card {
        background: #fff;
        border: 4px solid #000;
        box-shadow: 6px 6px 0px #000;
        transition: all 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
</style>

<div class="brutal-card p-4 mb-5 position-relative text-center"
     style="background-color: var(--neon-y);
     @if($hero->gambar_background)
        background-image: url('{{ asset(str_replace('\\', '/', $hero->gambar_background)) }}');
        background-size: cover;
        background-position: center;
     @endif">

    @if($hero->gambar_background)
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(255, 255, 255, 0.7); z-index: 1;"></div>
    @endif

    <div class="position-relative" style="z-index: 2;">
        <h1 class="display-3" style="text-shadow: 3px 3px 0px #fff;">
            {{ $hero->judul_utama ?? 'CETAK CEPAT, KUALITAS DEWA!' }}
        </h1>
        <p class="fs-4 mt-4 fw-bold bg-dark text-white d-inline-block p-3 border border-3 border-dark" style="box-shadow: 5px 5px 0px var(--neon-m);">
            {{ $hero->sub_judul ?? 'Solusi Digital Printing & Advertising Terbaik di Banua.' }}
        </p>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-4 mb-5">
        <div class="brutal-card" style="background: #fff;">
            <h4 class="p-3 mb-0 border-bottom border-dark border-4" style="background: var(--neon-c);">KATEGORI PRODUK</h4>
            <div class="p-0">
                <a href="{{ route('profil2.produk') }}" class="kategori-link {{ !request('kategori') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group me-2"></i> Semua Produk
                </a>
                @foreach($kategoris as $kat)
                    <a href="{{ route('profil2.produk', ['kategori' => $kat->kategori_produk]) }}"
                       class="kategori-link {{ request('kategori') == $kat->kategori_produk ? 'active' : '' }}">
                        <i class="fa-regular fa-circle me-2"></i> {{ $kat->kategori_produk }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-9 col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="hl-cyan mb-0" style="box-shadow: 3px 3px 0px #000;">
                {{ request('kategori') ?? 'SEMUA PRODUK' }}
            </h3>
            <div class="border border-3 border-dark bg-white px-3 py-1 fw-bold" style="box-shadow: 2px 2px 0px #000;">
                Menampilkan {{ $produks->count() }} Produk
            </div>
        </div>

        <div class="row g-4">
            @forelse($produks as $item)
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                <div class="produk-card">
                    <div class="p-3 text-center border-bottom border-dark border-3 bg-light" style="height: 200px; overflow: hidden;">
                        @if($item->gambar_produk)
                            <img src="{{ asset($item->gambar_produk) }}" class="img-fluid" style="height: 100%; object-fit: contain;" alt="{{ $item->nama_produk }}">
                        @else
                            <i class="fa-solid fa-box fa-5x mt-4 text-secondary"></i>
                        @endif
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <span class="badge bg-dark text-white mb-2 py-2 px-3 border border-dark border-2 rounded-0" style="width: fit-content; font-size: 0.7rem;">{{ strtoupper($item->kategori_layanan) }}</span>
                        <h5 class="fw-bold mb-1">{{ $item->nama_produk }}</h5>
                        <p class="text-secondary mb-3 text-sm fw-bold">{{ $item->deskripsi_singkat }}</p>
                        <a href="{{ route('profil2.produk.detail', $item->id) }}" class="brutal-btn mt-auto text-center py-2" style="font-size: 1rem;"><i class="fa-solid fa-eye"></i> Lihat Detail</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 fw-bold fs-4">Produk balum tasadia di kategori ini.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
