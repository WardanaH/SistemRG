<!-- Offcanvas Sidebar (Muncul dari Kanan) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel" style="background-color: var(--neon-m); border-left: 6px solid #000;">
    <div class="offcanvas-header border-bottom border-dark border-4" style="background: var(--neon-y);">
        <h3 class="offcanvas-title fw-bold text-dark" id="sidebarOffcanvasLabel">MENU UTAMA</h3>
        <!-- Tombol Tutup Brutal -->
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close" style="border: 2px solid #000; box-shadow: 2px 2px 0px #000; opacity: 1; background-color: #fff;"></button>
    </div>

    <div class="offcanvas-body">
        <ul class="list-group list-group-flush" style="border: none;">
            <li class="list-group-item px-0 border-0 mb-3 bg-transparent">
                <a href="/beranda" class="d-block brutal-card p-3 text-dark text-decoration-none fw-bold fs-5 text-center" style="background: var(--neon-c);">BERANDA</a>
            </li>
            <li class="list-group-item px-0 border-0 mb-3 bg-transparent">
                <a href="{{ route('profil2.tentang') }}" class="d-block brutal-card p-3 text-dark text-decoration-none fw-bold fs-5 text-center" style="background: var(--neon-g);">TENTANG KAMI</a>
            </li>
            <li class="list-group-item px-0 border-0 mb-3 bg-transparent">
                <a href="/kontak" class="d-block brutal-card p-3 text-dark text-decoration-none fw-bold fs-5 text-center" style="background: var(--neon-y);">HUBUNGI KAMI</a>
            </li>
            <li class="list-group-item px-0 border-0 bg-transparent">
                <a href="{{ route('auth.login') }}" class="d-block brutal-card p-3 text-dark text-decoration-none fw-bold fs-5 text-center" style="background: var(--neon-r);">LOGIN</a>
            </li>
        </ul>

        <div class="mt-5 p-3 brutal-card" style="background: #fff;">
            <h5 class="fw-bold"><i class="fa-solid fa-print"></i> Butuh Cetak Cepat?</h5>
            <p class="mb-0 fw-bold text-secondary">Langsung upload file desain pian atau chat CS kami di Kontak.</p>
        </div>
    </div>
</div>
