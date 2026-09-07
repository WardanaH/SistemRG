@php
    // Tarik data parusahaan langsung gasan dipakai di Footer
    $profilFooter = \App\Models\ProfilPerusahaan::first();
@endphp

<style>
    .brutal-link {
        color: #000;
        text-decoration: none;
        transition: all 0.1s ease;
        padding: 2px 4px;
    }
    .brutal-link:hover {
        background-color: var(--neon-c);
        border: 2px solid #000;
        font-weight: 900;
        margin-left: 5px;
        box-shadow: 2px 2px 0px #000;
    }
    .footer-icon-box {
        border: var(--b-border);
        box-shadow: 3px 3px 0px #000;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: #fff;
    }
</style>

<footer class="mt-5 border-top border-dark" style="border-top-width: 8px !important; background-color: #fff;">
    <div class="container-fluid px-4 py-5">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 border-bottom border-dark border-4 pb-4">
            <a class="navbar-brand fw-bold fs-2 text-dark mb-3 mb-md-0" href="#" style="background: var(--neon-y); padding: 5px 15px; border: var(--b-border); box-shadow: 4px 4px 0px #000;">
                RESTU GURU
            </a>

            <div class="d-flex gap-3">
                @if(!empty($profilFooter->wa_pusat))
                <a href="https://wa.me/{{ $profilFooter->wa_pusat }}" target="_blank" class="brutal-card text-dark text-decoration-none d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; background: var(--neon-g); font-size: 1.5rem;">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                @endif

                @if(!empty($profilFooter->link_instagram))
                <a href="{{ $profilFooter->link_instagram }}" target="_blank" class="brutal-card text-dark text-decoration-none d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; background: var(--neon-m); color: white !important; font-size: 1.5rem;">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                @endif

                @if(!empty($profilFooter->link_tiktok))
                <a href="{{ $profilFooter->link_tiktok }}" target="_blank" class="brutal-card text-dark text-decoration-none d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; background: var(--neon-c); font-size: 1.5rem;">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                @endif
            </div>
        </div>

        <div class="row">

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="d-flex mb-4 align-items-start">
                    <div class="footer-icon-box me-3" style="background: var(--neon-g);"><i class="fa-solid fa-headset"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">WhatsApp CS:</h6>
                        <p class="mb-0 fw-bold text-secondary">{{ $profilFooter->wa_pusat ?? '0812 3456 7890' }}</p>
                    </div>
                </div>

                <div class="d-flex mb-4 align-items-start">
                    <div class="footer-icon-box me-3" style="background: var(--neon-m); color: white;"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Kantor & Pabrik (Pusat):</h6>
                        <p class="mb-0 fw-bold text-secondary">{{ $profilFooter->alamat_cabang_1 ?? 'Jl. Karang Anyar 1, Banjarbaru, Kalimantan Selatan' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6 mb-4">
                <h5 class="fw-bold mb-3" style="border-bottom: 4px solid var(--neon-c); display: inline-block; padding-bottom: 2px;">LAYANAN</h5>
                <ul class="list-unstyled fw-bold">
                    <li class="mb-2"><a href="{{ route('profil2.produk', ['kategori' => 'Spanduk']) }}" class="brutal-link">Spanduk & Baliho</a></li>
                    <li class="mb-2"><a href="{{ route('profil2.produk', ['kategori' => 'Kaos']) }}" class="brutal-link">Sablon DTF</a></li>
                    <li class="mb-2"><a href="{{ route('profil2.produk', ['kategori' => 'Merchandise']) }}" class="brutal-link">Merchandise</a></li>
                    <li class="mb-2"><a href="{{ route('profil2.produk') }}" class="brutal-link">Semua Produk</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-3 col-6 mb-4">
                <h5 class="fw-bold mb-3" style="border-bottom: 4px solid var(--neon-y); display: inline-block; padding-bottom: 2px;">TENTANG KAMI</h5>
                <ul class="list-unstyled fw-bold">
                    <li class="mb-2"><a href="{{ route('profil2.tentang') }}" class="brutal-link">Profil Restu Guru</a></li>
                    <li class="mb-2"><a href="{{ route('profil2.kontak') }}" class="brutal-link">Lokasi Cabang</a></li>
                    <li class="mb-2"><a href="{{ route('profil2.kontak') }}" class="brutal-link">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="brutal-card p-3 bg-light">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-scale-balanced"></i> INFO LEGAL</h5>
                    <p class="text-sm fw-bold text-secondary mb-3">Aturan order wan panggunaan data palanggan (UU PDP).</p>
                    <a href="{{ route('profil2.legal.public', 'syarat_ketentuan') }}" class="brutal-link d-block mb-2"><i class="fa-solid fa-caret-right"></i> Syarat & Ketentuan</a>
                    <a href="{{ route('profil2.legal.public', 'kebijakan_privasi') }}" class="brutal-link d-block"><i class="fa-solid fa-caret-right"></i> Kebijakan Privasi</a>
                </div>
            </div>

        </div>
    </div>

    <div class="py-3 border-top border-dark border-4" style="background: #000;">
        <div class="container-fluid px-4 text-center">
            <span class="text-white fw-bold" style="letter-spacing: 1px;">
                &copy; {{ date('Y') }} - CV RESTU GURU PROMOSINDO.
            </span>
        </div>
    </div>
</footer>
