<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Restu Guru Promosindo</title>

    {{-- Font: Comfortaa --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Admin CSS --}}
    <link rel="stylesheet" href="{{ asset('profil/css/admin.css') }}">

    @stack('head')
    @stack('styles')
</head>
<body>
    {{-- Sidebar fixed --}}
    @include('profil.admin.partials.sidebar')

    {{-- Main wrapper --}}
    <div class="admin-main-wrap">
        @include('profil.admin.partials.topbar')

        <main class="admin-content">
            @yield('content')
        </main>

        @include('profil.admin.partials.footer')
    </div>

    {{-- Back to Top (GLOBAL) --}}
    <button type="button" class="rg-backtop" id="rgBackTop" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
        <span>Back to top</span>
    </button>

    {{-- MODAL: Confirm Logout (GLOBAL) --}}
    <div class="modal fade" id="rgLogoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 18px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-black" style="font-weight:900;">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-0 text-secondary" style="line-height:1.7;">
                        Kamu yakin mau logout dari halaman admin?
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Batal</button>

                    <form method="POST" action="{{ route('auth.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Confirm Save (GLOBAL) --}}
    <div class="modal fade" id="rgSaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 18px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-black" style="font-weight:900;">Konfirmasi Simpan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-0 text-secondary" style="line-height:1.7;">
                        Simpan perubahan yang kamu buat sekarang?
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary fw-bold" id="rgSaveConfirmBtn">
                        <i class="bi bi-check2-circle me-1"></i> Ya, Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('profil/js/admin.js') }}"></script>

    @stack('scripts')
</body>
</html>
