@extends('landingComponent.app')

@section('content')
    <!-- Hero Section Carousel -->
    @include('landingComponent.hero')

    <!-- Location Section -->
    @include('landingComponent.lokasi')

    <!-- Products Section -->
    @include('landingComponent.produk')

    <!-- Promo Section -->
    @include('landingComponent.promo')

    {{-- Video --}}
    @include('landingComponent.video')
@endsection

@push('scripts')
    <script>
        // Data Dummy Lokasi
        const lokasiData = {
            'liang': {
                address: 'Jl. A. Yani No.Km 21, Landasan Ulin, Banjarbaru',
                link: 'https://maps.app.goo.gl/...'
            },
            'martapura': {
                address: 'JL A. Yani No.Km 38, Komplek Pangeran Antasari (KOMPAS), Kec. Martapura, Kabupaten Banjar',
                link: 'https://maps.app.goo.gl/...'
            },
            'banjarbaru': {
                address: 'Jl. Panglima Batur, Banjarbaru',
                link: 'https://maps.app.goo.gl/...'
            },
            'banjarmasin': {
                address: 'Jl. Sultan Adam, Banjarmasin',
                link: 'https://maps.app.goo.gl/...'
            }
        };

        function showLocation(id) {
            // Reset warna tombol
            document.querySelectorAll('.btn-location').forEach(btn => btn.classList.remove('active'));

            // Aktifkan tombol yang di-klik (Berdasarkan event target)
            event.target.classList.add('active');

            // Update teks dan link bar kuning
            document.getElementById('loc-address').innerText = lokasiData[id].address;
            document.getElementById('loc-link').href = lokasiData[id].link;

            // Tampilkan bar kuning
            document.getElementById('location-bar').classList.remove('d-none');
        }
    </script>

    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('main-navbar');
            if (window.scrollY > 50) {
                // Pas di-scroll ke bawah, kasih background warna biru (sesuaikan warnanya kalau perlu)
                navbar.style.backgroundColor = 'rgba(26, 66, 138, 0.95)'; // Warna biru gelap agak transparan
                navbar.classList.add('shadow');
            } else {
                // Pas di paling atas, balikin transparan lagi
                navbar.style.backgroundColor = 'transparent';
                navbar.classList.remove('shadow');
            }
        });
    </script>
@endpush
