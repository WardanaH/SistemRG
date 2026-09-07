@extends('profil2.layout.app')

@section('content')

<style>
    .brutal-tab-layanan {
        border: 4px solid #000;
        background-color: #fff;
        color: #000;
        font-weight: 900;
        font-size: 1.2rem;
        text-transform: uppercase;
        margin: 5px;
        box-shadow: 5px 5px 0px #000;
        transition: all 0.1s;
        border-radius: 0;
    }
    .brutal-tab-layanan:hover {
        transform: translate(2px, 2px);
        box-shadow: 3px 3px 0px #000;
    }
    .nav-pills .nav-link.brutal-tab-layanan.active {
        background-color: var(--tab-bg, #000);
        color: #000;
        border: 4px solid #000;
        box-shadow: inset 4px 4px 0px rgba(255,255,255,0.4), 5px 5px 0px #000;
    }
    .layanan-detail-box {
        border: 4px solid #000;
        box-shadow: 8px 8px 0px #000;
        background: #fff;
    }
</style>

<div class="brutal-card p-4 mb-5 position-relative text-center"
     style="background-color: var(--neon-c);
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
    <div class="col-12">
        <ul class="nav nav-pills mb-5 justify-content-center" id="layanan-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active brutal-tab-layanan py-3 px-4 px-md-5" id="tab-indoor" data-bs-toggle="pill" data-bs-target="#content-indoor" type="button" role="tab" style="--tab-bg: var(--neon-c);">INDOOR & OUTDOOR</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link brutal-tab-layanan py-3 px-4 px-md-5" id="tab-dtf" data-bs-toggle="pill" data-bs-target="#content-dtf" type="button" role="tab" style="--tab-bg: var(--neon-m);">SABLON DTF</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link brutal-tab-layanan py-3 px-4 px-md-5" id="tab-merch" data-bs-toggle="pill" data-bs-target="#content-merch" type="button" role="tab" style="--tab-bg: var(--neon-g);">MERCHANDISE</button>
            </li>
        </ul>

        <div class="tab-content" id="layanan-tabContent">

            <div class="tab-pane fade show active" id="content-indoor" role="tabpanel">
                <div class="layanan-detail-box p-4 p-md-5" style="background-color: var(--neon-c);">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="bg-white border border-4 border-dark d-inline-block p-4 p-md-5" style="box-shadow: 6px 6px 0px #000;">
                                <i class="fa-solid fa-print fa-7x"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="bg-white p-4 p-md-5 border border-4 border-dark" style="box-shadow: 4px 4px 0px #000;">
                                <h2 class="border-bottom border-4 border-dark pb-2 hl-cyan d-inline-block">INDOOR & OUTDOOR</h2>
                                <p class="fs-5 fw-bold mt-3">Layanan cetak ukuran ganal gasan kaperluan promosi higa jalan atawa di dalam ruangan. Kualitas warna tajam, bahan tahan lawas, wan pangarjaan hancap!</p>

                                <h4 class="fw-bold mt-4"><i class="fa-solid fa-caret-right"></i> Daftar Produk Kami:</h4>
                                <ul class="fs-5 fw-bold text-secondary">
                                    @forelse($produkIndoor as $item)
                                        <li>{{ $item->nama_produk }}</li>
                                    @empty
                                        <li>Balum ada produk di-input.</li>
                                    @endforelse
                                </ul>

                                <a href="{{ route('profil2.produk') }}" class="brutal-btn mt-4 d-inline-block" style="background: var(--neon-y);"><i class="fa-solid fa-box-open"></i> Lihat Katalog Langkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="content-dtf" role="tabpanel">
                <div class="layanan-detail-box p-4 p-md-5" style="background-color: var(--neon-m);">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="bg-white border border-4 border-dark d-inline-block p-4 p-md-5" style="box-shadow: 6px 6px 0px #000;">
                                <i class="fa-solid fa-shirt fa-7x"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="bg-white p-4 p-md-5 border border-4 border-dark" style="box-shadow: 4px 4px 0px #000;">
                                <h2 class="border-bottom border-4 border-dark pb-2 d-inline-block text-white px-3 py-1" style="background: var(--neon-m);">SABLON DTF</h2>
                                <p class="fs-5 fw-bold mt-3">Metode sablon digital mutakhir pakai tinta husus. Warnanya mancancur, kada lakas ratak, wan kawa ba-pesan bijian tanpa minimum order!</p>

                                <h4 class="fw-bold mt-4"><i class="fa-solid fa-caret-right"></i> Daftar Produk Kami:</h4>
                                <ul class="fs-5 fw-bold text-secondary">
                                    @forelse($produkDtf as $item)
                                        <li>{{ $item->nama_produk }}</li>
                                    @empty
                                        <li>Balum ada produk di-input.</li>
                                    @endforelse
                                </ul>

                                <a href="{{ route('profil2.produk') }}" class="brutal-btn mt-4 d-inline-block" style="background: var(--neon-y);"><i class="fa-solid fa-box-open"></i> Lihat Katalog Langkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="content-merch" role="tabpanel">
                <div class="layanan-detail-box p-4 p-md-5" style="background-color: var(--neon-g);">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="bg-white border border-4 border-dark d-inline-block p-4 p-md-5" style="box-shadow: 6px 6px 0px #000;">
                                <i class="fa-solid fa-boxes-packing fa-7x"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="bg-white p-4 p-md-5 border border-4 border-dark" style="box-shadow: 4px 4px 0px #000;">
                                <h2 class="border-bottom border-4 border-dark pb-2 d-inline-block text-dark px-3 py-1" style="background: var(--neon-g);">MERCHANDISE</h2>
                                <p class="fs-5 fw-bold mt-3">Gasan sovenir kawinan, acara kanturan, atawa kaperluan panitia. Sadiakan cinderamata nang bujur-bujur diingat urang pakai desain custom.</p>

                                <h4 class="fw-bold mt-4"><i class="fa-solid fa-caret-right"></i> Daftar Produk Kami:</h4>
                                <ul class="fs-5 fw-bold text-secondary">
                                    @forelse($produkMerch as $item)
                                        <li>{{ $item->nama_produk }}</li>
                                    @empty
                                        <li>Balum ada produk di-input.</li>
                                    @endforelse
                                </ul>

                                <a href="{{ route('profil2.produk') }}" class="brutal-btn mt-4 d-inline-block" style="background: var(--neon-y);"><i class="fa-solid fa-box-open"></i> Lihat Katalog Langkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
