@extends('profil2.layout.app')

@section('content')

<style>
    .brutal-profile-box {
        border: 4px solid #000;
        box-shadow: 8px 8px 0px #000;
        background: #fff;
    }
    .visi-misi-box {
        border: 4px solid #000;
        box-shadow: 6px 6px 0px #000;
        height: 100%;
    }
</style>

<div class="brutal-card p-4 mb-5 position-relative text-center"
     style="background-color: var(--neon-m);
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
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="brutal-profile-box p-4 p-md-5">
            <h2 class="hl-cyan d-inline-block mb-3 border border-3 border-dark px-3 py-1" style="box-shadow: 3px 3px 0px #000;">CV RESTU GURU PROMOSINDO</h2>

            <div class="fs-5 fw-bold text-secondary lh-base mt-3">
                {!! nl2br(e($perusahaan->tentang_kami ?? 'Kami adalah parusahaan nang bagarak di bidang Digital Printing wan Advertising di Banua...')) !!}
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5 align-items-stretch">
    <div class="col-md-6">
        <div class="visi-misi-box p-4 p-md-5" style="background-color: var(--neon-y);">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-eye fa-3x me-3"></i>
                <h2 class="mb-0 fw-bold">VISI</h2>
            </div>
            <p class="fs-4 fw-bold border-top border-dark border-4 pt-3">
                {{ $perusahaan->visi ?? 'Manjadi parusahaan percetakan wan advertising rujukan utama di Kalimantan...' }}
            </p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="visi-misi-box p-4 p-md-5" style="background-color: var(--neon-c);">
            <div class="d-flex align-items-center mb-3">
                <i class="fa-solid fa-bullseye fa-3x me-3"></i>
                <h2 class="mb-0 fw-bold">MISI</h2>
            </div>
            <div class="fs-5 fw-bold border-top border-dark border-4 pt-3 ps-3 text-start">
                {!! nl2br(e($perusahaan->misi ?? 'Manyadiakan layanan cetak paling hancap...')) !!}
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="brutal-profile-box p-0 d-flex flex-column flex-lg-row">

            <div class="col-lg-4 bg-dark text-center" style="border-right: 4px solid #000;">
                <img src="https://placehold.co/600x800/000/39ff14?text=FOTO+BANG+QOMAL" alt="Putra Qomaluddin Attar Nurriqli" class="img-fluid" style="object-fit: cover; height: 100%; min-height: 400px;">
            </div>

            <div class="col-lg-8 p-4 p-md-5" style="background-color: var(--neon-g);">
                <span class="bg-dark text-white fw-bold px-2 py-1 mb-2 d-inline-block">FOUNDER / PENDIRI</span>
                <h2 class="display-5 fw-bold mb-0">Putra Qomaluddin Attar Nurriqli</h2>
                <h4 class="mb-4">M.I.Kom. (Bang Qomal)</h4>

                <p class="fs-5 fw-bold text-dark lh-base border-bottom border-dark border-3 pb-3">
                    Lahir di Banjarmasin pada 12 Oktober 1985, Bang Qomal marupakan pangusaha anum nang sukses mambangun CV Restu Guru Promosindo. Balasan tahun bapangalaman di dunia percetakan, sidin jua malabarkan sayap bisnis ka sektor *Food and Beverage* (F&B) wan *Garis Kota Work and Space*.
                </p>

                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <h5 class="fw-bold bg-white border border-dark border-2 d-inline-block px-2 py-1" style="box-shadow: 2px 2px 0px #000;">Pendidikan</h5>
                        <ul class="fw-bold mt-2 ps-3">
                            <li>D3 Ilmu Komunikasi Universitas Gadjah Mada (UGM)</li>
                            <li>S1 Ilmu Komunikasi Universitas Sebelas Maret (UNS) Surakarta</li>
                            <li>S2 Ilmu Komunikasi UNISKA MAB</li>
                        </ul>
                    </div>

                    <div class="col-md-6 mb-4">
                        <h5 class="fw-bold bg-white border border-dark border-2 d-inline-block px-2 py-1" style="box-shadow: 2px 2px 0px #000;">Kiprah & Organisasi</h5>
                        <ul class="fw-bold mt-2 ps-3">
                            <li>Anggota Komisi II DPRD Banjarbaru (2024–2029)</li>
                            <li>Ketua BPD HIPMI Kalimantan Selatan (2024-2027)</li>
                            <li>Pembina Gerakan Ekonomi Kreatif (Gekrafs) Kalsel</li>
                            <li>Pendiri komunitas pemuda "Semangat Muda" (2018)</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
