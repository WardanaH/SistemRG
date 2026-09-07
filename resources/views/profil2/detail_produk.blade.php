@extends('profil2.layout.app')

@section('content')

<div class="mb-4">
    <a href="{{ route('profil2.produk') }}" class="brutal-btn py-2 px-3" style="background: #fff;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog</a>
</div>

<div class="brutal-card p-4 p-md-5" style="background: #fff;">
    <div class="row">
        <div class="col-md-5 mb-4 mb-md-0">
            <div class="border border-4 border-dark p-2 bg-light" style="box-shadow: 8px 8px 0px #000;">
                @if($produk->gambar_produk)
                <img src="{{ asset($produk->gambar_produk) }}" class="img-fluid border border-2 border-dark" alt="{{ $produk->nama_produk }}">
                @else
                <div class="p-5 text-center"><i class="fa-solid fa-box fa-10x"></i></div>
                @endif
            </div>
        </div>

        <div class="col-md-7 ps-md-5">
            <span class="hl-yellow fw-bold px-2 py-1 border border-2 border-dark">Kategori: {{ $produk->kategori_produk }}</span>
            <span class="bg-dark text-white fw-bold px-2 py-1 border border-2 border-dark ms-2">Divisi: {{ strtoupper($produk->kategori_layanan) }}</span>

            <h1 class="display-4 fw-bold mt-3 mb-3">{{ $produk->nama_produk }}</h1>

            <div class="fs-5 fw-bold text-secondary border-bottom border-dark border-3 pb-4 mb-4">
                {!! nl2br(e($produk->deskripsi_lengkap)) !!}
            </div>

            <div class="mt-5 d-flex gap-3">
                <a href="https://wa.me/{{ $perusahaan->wa_pusat }}?text=Halo%20Restu%20Guru,%20nda%20handak%20batakun%20produk%20{{ urlencode($produk->nama_produk) }}"
                    class="brutal-btn fs-5 w-100 text-center" style="background: var(--neon-g); color: #000;">
                    <i class="fa-brands fa-whatsapp"></i> TANYA HARGA / ORDER
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
