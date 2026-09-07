@extends('profil2.layout.app')

@section('content')

<style>
    .brutal-tab {
        border: 3px solid #000;
        border-radius: 0;
        background-color: #fff;
        color: #000;
        font-weight: 900;
        text-transform: uppercase;
        margin: 0 5px 10px 5px;
        box-shadow: 4px 4px 0px #000;
        transition: all 0.1s;
    }
    .brutal-tab:hover {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px #000;
    }
    .nav-pills .nav-link.brutal-tab.active {
        background-color: var(--tab-bg, #000);
        color: #000;
        border: 3px solid #000;
        box-shadow: inset 2px 2px 0px rgba(255,255,255,0.5), 4px 4px 0px #000;
    }
    .product-box {
        border: 3px solid #000;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        background-color: #f8f9fa; /* Background amun kadada foto */
        box-shadow: inset 4px 4px 0px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .product-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="brutal-card p-5 mb-5 position-relative"
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
        <br><br>
        <a href="{{ route('profil2.produk') }}" class="brutal-btn mt-3"><i class="fa-solid fa-bolt"></i> Order Sekarang</a>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-12">
        <div class="brutal-card p-4" style="background-color: var(--neon-g);">
            <h2 class="border-bottom border-dark border-4 pb-2 mb-3">KENAPA MEMILIH KAMI?</h2>
            <p class="fs-5 fw-bold text-dark mb-0">
                {{ $perusahaan->deskripsi_perusahaan ?? 'CV Restu Guru Promosindo melayani segala kebutuhan cetak ikam...' }}
            </p>
        </div>
    </div>
</div>

<div class="d-flex flex-column align-items-center mb-5">
    <h2 class="mb-4 text-center"><span class="hl-yellow" style="box-shadow: 4px 4px 0px #000;">PRODUK UNGGULAN</span></h2>

    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active brutal-tab" id="pills-indoor-tab" data-bs-toggle="pill" data-bs-target="#pills-indoor" type="button" role="tab" style="--tab-bg: var(--neon-c);">INDOOR & OUTDOOR</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link brutal-tab" id="pills-dtf-tab" data-bs-toggle="pill" data-bs-target="#pills-dtf" type="button" role="tab" style="--tab-bg: var(--neon-m);">SABLON DTF</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link brutal-tab" id="pills-merch-tab" data-bs-toggle="pill" data-bs-target="#pills-merch" type="button" role="tab" style="--tab-bg: var(--neon-g);">MERCHANDISE</button>
        </li>
    </ul>

    <div class="tab-content w-100" id="pills-tabContent">

        <div class="tab-pane fade show active" id="pills-indoor" role="tabpanel" aria-labelledby="pills-indoor-tab">
            <div class="swiper mySwiperProduct w-100 py-3 px-2">
                <div class="swiper-wrapper">
                    @forelse($produkIndoor as $item)
                        <div class="swiper-slide">
                            <div class="brutal-card p-3 h-100 text-center" style="background: #fff;">
                                <div class="product-box border-bottom border-dark border-3" style="background: var(--neon-c);">
                                    @if($item->gambar_produk)
                                        <img src="{{ asset($item->gambar_produk) }}" alt="{{ $item->nama_produk }}">
                                    @else
                                        <i class="fa-solid fa-scroll fa-4x text-dark"></i>
                                    @endif
                                </div>
                                <h4 class="fs-5 mt-3">{{ $item->nama_produk }}</h4>
                            </div>
                        </div>
                    @empty
                        <div class="w-100 text-center py-4 fw-bold">Balum ada produk unggulan di kategori ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-dtf" role="tabpanel" aria-labelledby="pills-dtf-tab">
            <div class="swiper mySwiperProduct w-100 py-3 px-2">
                <div class="swiper-wrapper">
                    @forelse($produkDtf as $item)
                        <div class="swiper-slide">
                            <div class="brutal-card p-3 h-100 text-center" style="background: #fff;">
                                <div class="product-box border-bottom border-dark border-3" style="background: var(--neon-m);">
                                    @if($item->gambar_produk)
                                        <img src="{{ asset($item->gambar_produk) }}" alt="{{ $item->nama_produk }}">
                                    @else
                                        <i class="fa-solid fa-shirt fa-4x text-white"></i>
                                    @endif
                                </div>
                                <h4 class="fs-5 mt-3">{{ $item->nama_produk }}</h4>
                            </div>
                        </div>
                    @empty
                        <div class="w-100 text-center py-4 fw-bold">Balum ada produk unggulan di kategori ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-merch" role="tabpanel" aria-labelledby="pills-merch-tab">
            <div class="swiper mySwiperProduct w-100 py-3 px-2">
                <div class="swiper-wrapper">
                    @forelse($produkMerch as $item)
                        <div class="swiper-slide">
                            <div class="brutal-card p-3 h-100 text-center" style="background: #fff;">
                                <div class="product-box border-bottom border-dark border-3" style="background: var(--neon-g);">
                                    @if($item->gambar_produk)
                                        <img src="{{ asset($item->gambar_produk) }}" alt="{{ $item->nama_produk }}">
                                    @else
                                        <i class="fa-solid fa-mug-hot fa-4x text-dark"></i>
                                    @endif
                                </div>
                                <h4 class="fs-5 mt-3">{{ $item->nama_produk }}</h4>
                            </div>
                        </div>
                    @empty
                        <div class="w-100 text-center py-4 fw-bold">Balum ada produk unggulan di kategori ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<div class="d-flex flex-column align-items-center mb-5 w-100">
    <h2 class="mb-4 text-center"><span class="hl-cyan" style="box-shadow: 4px 4px 0px #000;">KLIEN KAMI</span></h2>

    <div class="swiper mySwiperClient w-100 py-3 px-2">
        <div class="swiper-wrapper">
            @forelse($kliens as $klien)
                <div class="swiper-slide">
                    <div class="brutal-card bg-white d-flex align-items-center justify-content-center p-3" style="height: 120px;">
                        @if($klien->logo)
                            <img src="{{ asset(str_replace('\\', '/', $klien->logo)) }}" alt="{{ $klien->nama_klien }}" style="max-width: 100%; max-height: 80px; object-fit: contain;">
                        @else
                            <h5 class="mb-0 fw-bold text-center">{{ strtoupper($klien->nama_klien) }}</h5>
                        @endif
                    </div>
                </div>
            @empty
                <div class="w-100 text-center fw-bold">Balum ada data klien ditambahkan.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Swiper Produk
        var swiperProduct = new Swiper(".mySwiperProduct", {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: false, // Diganti false, supaya kada error amun produknya halus/sadikit
            observer: true,
            observeParents: true,
            breakpoints: {
                768: { slidesPerView: 3 },
                1024: { slidesPerView: 4 }
            },
        });

        // Swiper Klien
        var swiperClient = new Swiper(".mySwiperClient", {
            slidesPerView: 2,
            spaceBetween: 20,
            loop: {{ $kliens->count() > 5 ? 'true' : 'false' }},
            autoplay: { delay: 2500, disableOnInteraction: false },
            breakpoints: {
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 5 },
            },
        });
    });
</script>

@endsection
