@extends('profil2.layout.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold bg-white d-inline-block px-3 py-1 border border-4 border-dark" style="box-shadow: 5px 5px 0px #000;">INFO PERUSAHAAN</h1>
</div>

@if(session('success'))
    <div class="alert brutal-card mb-4 p-3 fw-bold fs-5" style="background-color: var(--neon-g);">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<form action="{{ route('profil2.perusahaan.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="brutal-card p-4 p-md-5 mb-5" style="background-color: #fff;">
        <h3 class="border-bottom border-dark border-4 pb-2 mb-4 hl-cyan d-inline-block">TENTANG, VISI & MISI</h3>

        <div class="mb-4">
            <label class="fw-bold mb-2">Teks Tentang Perusahaan</label>
            <textarea name="tentang_kami" class="form-control brutal-input" rows="4" placeholder="Kisah / profil singkat parusahaan...">{{ old('tentang_kami', $perusahaan->tentang_kami) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="fw-bold mb-2">Visi</label>
                <textarea name="visi" class="form-control brutal-input" rows="4">{{ old('visi', $perusahaan->visi) }}</textarea>
            </div>
            <div class="col-md-6 mb-4">
                <label class="fw-bold mb-2">Misi</label>
                <textarea name="misi" class="form-control brutal-input" rows="4" placeholder="Kawa di-isi pakai daftar/angka">{{ old('misi', $perusahaan->misi) }}</textarea>
            </div>
        </div>
    </div>

    <div class="brutal-card p-4 p-md-5 mb-5" style="background-color: #fafafa;">
        <h3 class="border-bottom border-dark border-4 pb-2 mb-4 hl-yellow d-inline-block">DATA CABANG & LOKASI</h3>

        <div class="p-3 mb-4 border border-3 border-dark" style="background: #fff;">
            <h5 class="fw-bold"><i class="fa-solid fa-store"></i> Cabang 1 (Martapura)</h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">No. WhatsApp</label>
                    <input type="text" name="wa_cabang_1" class="form-control brutal-input" value="{{ old('wa_cabang_1', $perusahaan->wa_cabang_1) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_cabang_1" class="form-control brutal-input" rows="2">{{ old('alamat_cabang_1', $perusahaan->alamat_cabang_1) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Link Google Maps</label>
                    <textarea name="link_maps_cabang_1" class="form-control brutal-input" rows="2">{{ old('link_maps_cabang_1', $perusahaan->link_maps_cabang_1) }}</textarea>
                </div>
            </div>
        </div>

        <div class="p-3 mb-4 border border-3 border-dark" style="background: #fff;">
            <h5 class="fw-bold"><i class="fa-solid fa-store"></i> Cabang 2 (Banjarbaru)</h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">No. WhatsApp</label>
                    <input type="text" name="wa_cabang_2" class="form-control brutal-input" value="{{ old('wa_cabang_2', $perusahaan->wa_cabang_2) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_cabang_2" class="form-control brutal-input" rows="2">{{ old('alamat_cabang_2', $perusahaan->alamat_cabang_2) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Link Google Maps</label>
                    <textarea name="link_maps_cabang_2" class="form-control brutal-input" rows="2">{{ old('link_maps_cabang_2', $perusahaan->link_maps_cabang_2) }}</textarea>
                </div>
            </div>
        </div>

        <div class="p-3 mb-4 border border-3 border-dark" style="background: #fff;">
            <h5 class="fw-bold"><i class="fa-solid fa-store"></i> Cabang 3 (Banjarmasin)</h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">No. WhatsApp</label>
                    <input type="text" name="wa_cabang_3" class="form-control brutal-input" value="{{ old('wa_cabang_3', $perusahaan->wa_cabang_3) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_cabang_3" class="form-control brutal-input" rows="2">{{ old('alamat_cabang_3', $perusahaan->alamat_cabang_3) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Link Google Maps</label>
                    <textarea name="link_maps_cabang_3" class="form-control brutal-input" rows="2">{{ old('link_maps_cabang_3', $perusahaan->link_maps_cabang_3) }}</textarea>
                </div>
            </div>
        </div>

        <div class="p-3 border border-3 border-dark" style="background: #fff;">
            <h5 class="fw-bold"><i class="fa-solid fa-store"></i> Cabang 4 (LiangAnggang)</h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">No. WhatsApp</label>
                    <input type="text" name="wa_cabang_4" class="form-control brutal-input" value="{{ old('wa_cabang_4', $perusahaan->wa_cabang_4) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Alamat Lengkap</label>
                    <textarea name="alamat_cabang_4" class="form-control brutal-input" rows="2">{{ old('alamat_cabang_4', $perusahaan->alamat_cabang_4) }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold mb-1">Link Google Maps</label>
                    <textarea name="link_maps_cabang_4" class="form-control brutal-input" rows="2">{{ old('link_maps_cabang_4', $perusahaan->link_maps_cabang_4) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="brutal-card p-4 p-md-5 mb-5" style="background-color: var(--neon-m);">
        <h3 class="border-bottom border-dark border-4 pb-2 mb-4 d-inline-block px-2 text-white" style="background: #000;">SOSIAL MEDIA & CS PUSAT</h3>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="fw-bold mb-2 text-white">No. WhatsApp CS Pusat</label>
                <input type="text" name="wa_pusat" class="form-control brutal-input" value="{{ old('wa_pusat', $perusahaan->wa_pusat) }}" placeholder="Cth: 08123456789">
            </div>
            <div class="col-md-6 mb-4">
                <label class="fw-bold mb-2 text-white">Email Pusat</label>
                <input type="email" name="email_pusat" class="form-control brutal-input" value="{{ old('email_pusat', $perusahaan->email_pusat) }}" placeholder="Cth: admin@restuguru.com">
            </div>
            <div class="col-md-4 mb-3">
                <label class="fw-bold mb-2 text-white"><i class="fa-brands fa-instagram"></i> Link Instagram</label>
                <input type="text" name="link_instagram" class="form-control brutal-input" value="{{ old('link_instagram', $perusahaan->link_instagram) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="fw-bold mb-2 text-white"><i class="fa-brands fa-tiktok"></i> Link TikTok</label>
                <input type="text" name="link_tiktok" class="form-control brutal-input" value="{{ old('link_tiktok', $perusahaan->link_tiktok) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="fw-bold mb-2 text-white"><i class="fa-brands fa-facebook"></i> Link Facebook</label>
                <input type="text" name="link_facebook" class="form-control brutal-input" value="{{ old('link_facebook', $perusahaan->link_facebook) }}">
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5 position-sticky bottom-0 bg-white p-3 border border-dark border-4" style="box-shadow: 0px -5px 15px rgba(0,0,0,0.1); z-index: 10;">
        <button type="submit" class="brutal-btn w-100 fs-4 py-3" style="background: var(--neon-g); color: #000;">
            <i class="fa-solid fa-floppy-disk me-2"></i> SIMPAN PERUBAHAN
        </button>
    </div>

</form>

@endsection
