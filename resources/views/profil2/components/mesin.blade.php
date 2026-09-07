<div class="container-fluid px-4 mb-5">
    <div class="brutal-card p-4 p-md-5" style="background-color: #fff;">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 border-bottom border-dark border-4 pb-3">
            <h2 class="mb-0 hl-yellow" style="box-shadow: 4px 4px 0px #000;">DAPUR CETAK KAMI</h2>
            <span class="fs-5 fw-bold text-secondary text-md-end mt-3 mt-md-0">
                Kualitas dewa, hasil nyata. Disokong mesin-mesin industri handal.
            </span>
        </div>

        <div class="swiper mySwiperMesin w-100 py-3 px-2">
            <div class="swiper-wrapper">

                @forelse($mesins as $mesin)
                    @php
                        // Atur warna background badasarkan inputan admin (default cyan)
                        $bgCard = 'var(--neon-c)';
                        $bgTitle = 'bg-white';
                        $textColor = 'text-dark';

                        if($mesin->warna_tema == 'magenta') {
                            $bgCard = 'var(--neon-m)';
                            $bgTitle = 'bg-black';
                            $textColor = 'text-white';
                        } elseif($mesin->warna_tema == 'yellow') {
                            $bgCard = 'var(--neon-y)';
                        } elseif($mesin->warna_tema == 'green') {
                            $bgCard = 'var(--neon-g)';
                        }
                    @endphp

                    <div class="swiper-slide">
                        <div class="brutal-card p-3 h-100" style="background: {{ $bgCard }};">
                            @if($mesin->gambar)
                                <img src="{{ asset(str_replace('\\', '/', $mesin->gambar)) }}" class="img-fluid border border-4 border-dark mb-3 w-100" style="box-shadow: 4px 4px 0px rgba(0,0,0,0.5); height: 250px; object-fit: cover;" alt="{{ $mesin->nama_mesin }}">
                            @else
                                <div class="border border-4 border-dark mb-3 w-100 d-flex align-items-center justify-content-center" style="box-shadow: 4px 4px 0px rgba(0,0,0,0.5); height: 250px; background: #000;">
                                    <h4 class="text-white fw-bold mb-0">FOTO {{ strtoupper($mesin->nama_mesin) }}</h4>
                                </div>
                            @endif

                            <h4 class="text-center mb-0 fw-bold border border-3 border-dark p-2 {{ $bgTitle }} {{ $textColor }}" style="box-shadow: inset 2px 2px 0px rgba(0,0,0,0.3);">
                                {{ strtoupper($mesin->nama_mesin) }}
                            </h4>
                        </div>
                    </div>
                @empty
                    <div class="w-100 text-center py-5">
                        <h4 class="fw-bold text-secondary">Admin balum ma-upload data mesin.</h4>
                    </div>
                @endforelse

            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var swiperMesin = new Swiper(".mySwiperMesin", {
            slidesPerView: 1, // Amun di HP tampilakan 1 mesin haja biar ganal
            spaceBetween: 25, // Jarak antar kotak mesin
            loop: {{ $mesins->count() > 3 ? 'true' : 'false' }}, // Loop hanya aktif mun mesin labih dari 3 supaya kada error
            autoplay: {
                delay: 3500, // Diulah agak lambat 3.5 detik biar urang tabaca
                disableOnInteraction: false,
            },
            breakpoints: {
                768: { slidesPerView: 2 }, // Amun di Tablet kaluar 2
                1024: { slidesPerView: 3 } // Amun di PC/Laptop kaluar 3
            },
        });
    });
</script>
