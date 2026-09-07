@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">DASHBOARD PROFIL</h1>
    <a href="{{ route('profil2.beranda') }}" target="_blank" class="brutal-btn" style="background: var(--neon-c); color: #000; box-shadow: 4px 4px 0px #000;">
        <i class="fa-solid fa-eye me-1"></i> Lihat Web
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="brutal-card p-5 text-center" style="background-color: var(--neon-y);">
            <div class="bg-white border border-4 border-dark d-inline-block p-4 mb-4" style="box-shadow: 6px 6px 0px #000; border-radius: 50%;">
                <i class="fa-solid fa-user-shield fa-4x"></i>
            </div>

            <h2 class="display-5 fw-bold mb-3" style="text-shadow: 2px 2px 0px #fff;">Selamat Datang, {{ $adminName }}!</h2>

            <p class="fs-4 fw-bold text-dark border-top border-bottom border-dark border-3 py-3 mx-auto" style="max-width: 800px;">
                Ini adalah Panel Admin husus gasan mangatur konten halaman Profil Web Public Restu Guru Promosindo.
            </p>

            <p class="fs-5 mt-4 fw-bold text-secondary">
                Silakan pilih menu di subalah kiba (kiri) gasan mangganti Teks Hero, Data Perusahaan, Katalog Produk, wan Syarat & Ketentuan.
            </p>
        </div>
    </div>
</div>

@endsection
