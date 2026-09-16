<section class="container mt-5" id="products">
    <h1 class="text-center fw-bold text-primary mb-4" style="font-size: 3rem;">PRODUK & LAYANAN KAMI</h1>
    <div class="row g-4">
        <!-- Looping Data Dummy -->
        @php
            $products = [
                ['name' => 'SPANDUK / BANNER FLEXI 280', 'img' => 'FOTO BANNER FLEXI 280.png'],
                ['name' => 'STIKER INDOOR / OUTDOOR', 'img' => 'STIKER INDOOR OUTDOOR.png'],
                ['name' => 'CUSTOM AKRILIK', 'img' => 'AKRILIK.png'],
                ['name' => 'SPANDUK / BANNER KOREA', 'img' => 'FOTO BANNER KOREA.png'],
                ['name' => 'STIKER VINYL', 'img' => 'STIKER VINYL.png'],
                ['name' => 'KARTU NAMA', 'img' => 'KARTU NAMA.png'],
            ];
        @endphp

        @foreach ($products as $prod)
            <div class="col-md-4">
                <div class="card product-card border-0 p-3 h-100 d-flex flex-row align-items-center">
                    <div class="w-50 pe-2">
                        <h6 class="fw-bold">{{ $prod['name'] }}</h6>
                        <p class="small text-muted" style="font-size: 0.7rem;">Cetak kualitas tinggi, warna tajam, cocok
                            untuk kebutuhanmu.</p>
                    </div>
                    <div class="w-50">
                        <img src="{{ asset('assets/landing/' . $prod['img']) }}" class="img-fluid rounded"
                            alt="{{ $prod['name'] }}">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="text-center mt-4">
        <button class="btn btn-secondary rounded-pill px-4 shadow-sm">TAMPILKAN LEBIH BANYAK PRODUK</button>
    </div>
</section>
