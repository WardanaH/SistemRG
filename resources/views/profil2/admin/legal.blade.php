@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">LEGAL & PRIVASI</h1>
</div>

@if(session('success'))
    <div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background-color: var(--neon-g);">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<form action="{{ route('profil2.legal.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-5 mb-5">

        <div class="col-xl-6">
            <div class="brutal-card p-4 p-md-5 h-100" style="background-color: #fafafa;">
                <div class="d-flex align-items-center mb-4 border-bottom border-dark border-4 pb-2">
                    <i class="fa-solid fa-file-contract fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0 fw-bold hl-yellow d-inline-block px-2">SYARAT & KETENTUAN</h3>
                        <p class="text-secondary fw-bold mb-0 mt-1">Aturan transaksi wan order gasan palanggan.</p>
                    </div>
                </div>

                <div class="mb-3">
                    <textarea name="syarat_ketentuan" class="form-control brutal-input fs-5" rows="12" required>{{ old('syarat_ketentuan', $syarat->isi_konten) }}</textarea>
                    <small class="fw-bold text-secondary mt-2 d-block">*Kawa manggunakan angka gasan daftar (1, 2, 3...) atawa tag HTML amun paham.</small>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="brutal-card p-4 p-md-5 h-100" style="background-color: #fafafa;">
                <div class="d-flex align-items-center mb-4 border-bottom border-dark border-4 pb-2">
                    <i class="fa-solid fa-shield-halved fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0 fw-bold d-inline-block px-2 text-white" style="background: var(--neon-m);">KEBIJAKAN PRIVASI</h3>
                        <p class="text-secondary fw-bold mb-0 mt-1">Panggunaan data bapanduan lawan UU PDP No. 27 Th 2022.</p>
                    </div>
                </div>

                <div class="mb-3">
                    <textarea name="kebijakan_privasi" class="form-control brutal-input fs-5" rows="12" required>{{ old('kebijakan_privasi', $privasi->isi_konten) }}</textarea>
                    <small class="fw-bold text-secondary mt-2 d-block">*Teks bawaan sudah kami sasuaiakan lawan UU PDP gasan parlindungan data palanggan.</small>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-4 mb-5 position-sticky bottom-0 bg-white p-3 border border-dark border-4" style="box-shadow: 0px -5px 15px rgba(0,0,0,0.1); z-index: 10;">
        <button type="submit" class="brutal-btn w-100 fs-4 py-3" style="background: var(--neon-c); color: #000;">
            <i class="fa-solid fa-floppy-disk me-2"></i> SIMPAN ATURAN LEGAL
        </button>
    </div>

</form>

@endsection
