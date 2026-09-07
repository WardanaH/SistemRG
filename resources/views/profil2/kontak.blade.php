@extends('profil2.layout.app')

@section('content')

<style>
    .brutal-input {
        border: 4px solid #000;
        border-radius: 0;
        padding: 15px;
        font-weight: bold;
        box-shadow: 4px 4px 0px #000;
        transition: all 0.2s;
    }
    .brutal-input:focus {
        outline: none;
        background-color: var(--neon-y);
        box-shadow: 6px 6px 0px #000;
        transform: translate(-2px, -2px);
    }
    .info-box {
        border: 4px solid #000;
        box-shadow: 6px 6px 0px #000;
        background: #fff;
    }
    .map-container iframe {
        width: 100%;
        height: 350px;
        border: 4px solid #000;
        box-shadow: 6px 6px 0px #000;
    }
</style>

<div class="brutal-card p-4 mb-5 position-relative text-center"
     style="background-color: var(--neon-y);
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

<div class="row g-5 mb-5">

    <div class="col-lg-5">
        <div class="info-box p-4 p-md-5 h-100" style="background-color: var(--neon-c);">
            <h2 class="mb-4 fw-bold border-bottom border-dark border-4 pb-2">LOKASI & CABANG</h2>

            <div class="mb-4">
                <span class="bg-dark text-white px-2 py-1 fw-bold">CABANG KAMI:</span>
            </div>

            <ul class="list-unstyled fw-bold fs-5">
                @if(!empty($perusahaan->alamat_cabang_1))
                <li class="mb-3 border-bottom border-dark border-2 pb-2">
                    <i class="fa-solid fa-store me-2"></i> Pusat (Martapura)<br>
                    <span class="text-sm fw-normal text-dark"><i class="fa-solid fa-location-dot me-1"></i> {{ $perusahaan->alamat_cabang_1 }}</span>
                    @if(!empty($perusahaan->wa_pusat))
                        <br><span class="text-sm fw-normal text-dark"><i class="fa-brands fa-whatsapp me-1"></i> {{ $perusahaan->wa_pusat }}</span>
                    @endif
                </li>
                @endif

                @if(!empty($perusahaan->alamat_cabang_2))
                <li class="mb-3 border-bottom border-dark border-2 pb-2">
                    <i class="fa-solid fa-store-slash me-2"></i> Cabang Banjarbaru<br>
                    <span class="text-sm fw-normal text-dark"><i class="fa-solid fa-location-dot me-1"></i> {{ $perusahaan->alamat_cabang_2 }}</span>
                    @if(!empty($perusahaan->wa_cabang_2))
                        <br><span class="text-sm fw-normal text-dark"><i class="fa-brands fa-whatsapp me-1"></i> {{ $perusahaan->wa_cabang_2 }}</span>
                    @endif
                </li>
                @endif

                @if(!empty($perusahaan->alamat_cabang_3))
                <li class="mb-3">
                    <i class="fa-solid fa-store-slash me-2"></i> Cabang Banjarmasin<br>
                    <span class="text-sm fw-normal text-dark"><i class="fa-solid fa-location-dot me-1"></i> {{ $perusahaan->alamat_cabang_3 }}</span>
                    @if(!empty($perusahaan->wa_cabang_3))
                        <br><span class="text-sm fw-normal text-dark"><i class="fa-brands fa-whatsapp me-1"></i> {{ $perusahaan->wa_cabang_3 }}</span>
                    @endif
                </li>
                @endif

                @if(!empty($perusahaan->alamat_cabang_4))
                <li class="mb-3">
                    <i class="fa-solid fa-store-slash me-2"></i> Cabang LiangAnggang<br>
                    <span class="text-sm fw-normal text-dark"><i class="fa-solid fa-location-dot me-1"></i> {{ $perusahaan->alamat_cabang_4 }}</span>
                    @if(!empty($perusahaan->wa_cabang_4))
                        <br><span class="text-sm fw-normal text-dark"><i class="fa-brands fa-whatsapp me-1"></i> {{ $perusahaan->wa_cabang_4 }}</span>
                    @endif
                </li>
                @endif
            </ul>

            <div class="mt-5">
                <h4 class="fw-bold mb-3"><i class="fa-solid fa-map-location-dot"></i> Peta Lokasi Pusat</h4>
                <div class="map-container bg-white d-flex align-items-center justify-content-center text-center p-3" style="border: 4px solid #000; box-shadow: 6px 6px 0px #000; min-height: 350px;">
                    @if(!empty($perusahaan->link_maps_cabang_1))
                        {!! $perusahaan->link_maps_cabang_1 !!}
                    @else
                        <span class="fw-bold text-secondary">Link Iframe Maps balum di-set admin.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="info-box p-4 p-md-5 h-100" style="background-color: var(--neon-g);">
            <div class="d-flex align-items-center mb-4 border-bottom border-dark border-4 pb-2">
                <i class="fa-solid fa-paper-plane fa-3x me-3"></i>
                <h2 class="mb-0 fw-bold">KIRIM PESAN</h2>
            </div>

            <p class="fw-bold fs-5 mb-4">Ada nang handak ditakunaka? Isi form di bawah ini, admin kami kaina mambalas ca-hancapnya!</p>

            <form id="formKontakWa">
                <div class="mb-4">
                    <label class="fw-bold mb-2">Ngaran Ikam</label>
                    <input type="text" id="wa_nama" class="form-control brutal-input" placeholder="Cth: M. Riszqi Wardana" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold mb-2">No. WhatsApp</label>
                        <input type="number" id="wa_nomor" class="form-control brutal-input" placeholder="Cth: 0812..." required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="fw-bold mb-2">Email (Opsional)</label>
                        <input type="email" id="wa_email" class="form-control brutal-input" placeholder="Cth: email@domain.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold mb-2">Subjek / Kaperluan</label>
                    <select id="wa_subjek" class="form-control brutal-input" required>
                        <option value="">-- Pilih Kaperluan --</option>
                        <option value="Tanya Harga">Tanya Harga / Price List</option>
                        <option value="Konsultasi Desain">Konsultasi Desain</option>
                        <option value="Komplain">Komplain / Keluhan</option>
                        <option value="Kerjasama">Kerjasama B2B</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="fw-bold mb-2">Pesan Ikam</label>
                    <textarea id="wa_pesan" class="form-control brutal-input" rows="5" placeholder="Tulisakan kaperluan ikam di mari..." required></textarea>
                </div>

                <button type="submit" class="brutal-btn w-100 fs-4 py-3 mt-2" style="background: var(--neon-m); color: white;">
                    <i class="fa-solid fa-paper-plane me-2"></i> KIRIM PESAN VIA WA
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    document.getElementById('formKontakWa').addEventListener('submit', function(e) {
        e.preventDefault(); // Manahan form supaya kada me-refresh halaman

        // Ambil data dari isian form
        let nama = document.getElementById('wa_nama').value;
        let nomor = document.getElementById('wa_nomor').value;
        let email = document.getElementById('wa_email').value;
        let subjek = document.getElementById('wa_subjek').value;
        let pesan = document.getElementById('wa_pesan').value;

        // Ambil nomor WA admin dari database (mun kosong, pakai nomor default)
        let adminWa = "{{ $perusahaan->wa_pusat ?? '081234567890' }}";

        // Ubah angka 0 di depan jadi 62 supaya kawa tabaca di API WhatsApp
        if(adminWa.startsWith('0')) {
            adminWa = '62' + adminWa.substring(1);
        }

        // Ulah format susunan pasan WA-nya
        let textWa = `Halo Admin Restu Guru, ulun *${nama}*.\n\n`;
        textWa += `*Subjek:* ${subjek}\n`;
        textWa += `*No HP:* ${nomor}\n`;
        if(email) {
            textWa += `*Email:* ${email}\n`;
        }
        textWa += `\n*Pesan:*\n${pesan}`;

        // Buka link WhatsApp pakai format nang sudah diulah
        let waUrl = `https://wa.me/${adminWa}?text=${encodeURIComponent(textWa)}`;
        window.open(waUrl, '_blank');
    });
</script>
@endsection
