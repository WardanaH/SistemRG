@extends('profil2.layout.app')

@section('content')

<div class="brutal-card p-4 p-md-5 mb-5 text-center" style="background-color: var(--neon-c);">
    <h1 class="display-3 mb-0 text-uppercase" style="text-shadow: 4px 4px 0px #fff;">INSPIRASI DESAIN {{ $tema }}</h1>
    <p class="fs-5 mt-3 fw-bold bg-dark text-white d-inline-block p-2 border border-3 border-dark" style="box-shadow: 4px 4px 0px var(--neon-y);">
        Pilih desain favorit ikam, langsung cetak kada pakai ribet!
    </p>
</div>

<div class="row g-4 mb-5">
    @forelse($events as $item)
        <div class="col-md-6 col-lg-4">
            <div class="brutal-card h-100 d-flex flex-column" style="background: #fff; padding: 0; overflow: hidden;">

                <div class="border-bottom border-dark border-4 bg-light" style="height: 250px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    @if($item->gambar)
                        <img src="{{ asset(str_replace('\\', '/', $item->gambar)) }}" alt="{{ $item->nama_produk }}" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-image fa-5x text-secondary"></i>
                    @endif
                </div>

                <div class="p-4 d-flex flex-column flex-grow-1 text-center" style="background: #fafafa;">
                    <span class="bg-dark text-white fw-bold px-2 py-1 mb-3 mx-auto border border-2 border-dark" style="font-size: 0.8rem; width: fit-content; box-shadow: 2px 2px 0px #000;">
                        {{ strtoupper($item->badge_kategori) }}
                    </span>

                    <h3 class="fw-bold mb-2">{{ $item->nama_produk }}</h3>
                    <p class="fw-bold text-secondary mb-4">{{ $item->deskripsi }}</p>

                    <a href="https://wa.me/{{ $perusahaan->wa_pusat ?? '' }}?text=Halo%20Restu%20Guru,%20nda%20handak%20pesan%20pakai%20desain%20{{ urlencode($item->nama_produk) }}" target="_blank" class="brutal-btn mt-auto" style="background: var(--neon-g); color: #000; font-size: 1.1rem; border-width: 3px;">
                        <i class="fa-solid fa-paintbrush me-2"></i> PESAN PAKAI DESAIN INI
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="brutal-card p-5 d-inline-block" style="background: var(--neon-y);">
                <i class="fa-solid fa-triangle-exclamation fa-4x mb-3 text-dark"></i>
                <h2 class="fw-bold">DESAIN BALUM TASADIA</h2>
                <p class="fs-5 fw-bold text-dark">Admin balum ma-upload desain khusus gasan tema <strong>{{ strtoupper($tema) }}</strong> ini.</p>
                <a href="{{ route('profil2.beranda') }}" class="brutal-btn mt-3 bg-white text-dark"><i class="fa-solid fa-arrow-left me-2"></i> KEMBALI KE BERANDA</a>
            </div>
        </div>
    @endforelse
</div>

@endsection
