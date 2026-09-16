<section class="hero-section" id="hero">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <!-- Indikator Slide -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"
                aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        </div>

        <!-- Konten Slide -->
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active" data-bs-interval="4000">
                <img src="{{ asset('assets/landing/HEADLINE A.png') }}" class="d-block w-100 hero-image"
                    alt="Buat Spanduk Kegiatanmu">
                <!-- Tambahan tombol order jika diperlukan, posisinya absolute -->
                <div class="carousel-caption d-none d-md-block text-start" style="bottom: 20%; left: 10%;">
                    <a href="#"
                        class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark border-0 shadow">ORDER
                        SEKARANG</a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item" data-bs-interval="4000">
                <img src="{{ asset('assets/landing/HEADLINE B.png') }}" class="d-block w-100 hero-image"
                    alt="Merchandise Sebuah Kewajiban">
                <!-- Tambahan tombol order jika diperlukan -->
                <div class="carousel-caption d-none d-md-block text-start" style="bottom: 20%; left: 10%;">
                    <a href="#"
                        class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark border-0 shadow">ORDER
                        SEKARANG</a>
                </div>
            </div>
        </div>

        <!-- Tombol Next/Prev -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
