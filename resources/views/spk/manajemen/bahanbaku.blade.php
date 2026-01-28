@extends('spk.layout.app')

@section('content')

{{-- ========================= --}}
{{-- SWEETALERT SESSION ALERT  --}}
{{-- ========================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 1500
        });
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "{{ session('error') }}",
            showConfirmButton: true
        });
    });
</script>
@endif

<div class="row">
    <div class="col-12">

        {{-- ========================= --}}
        {{-- FORM TAMBAH BAHAN BAKU    --}}
        {{-- ========================= --}}
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tambah Bahan Baku Baru</h6>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('manajemen.bahanbaku.store') }}">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Kode Bahan (Cth: BHN-001)</label>
                                <input type="text" name="kode" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-8 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Nama Bahan (Cth: Vinyl China 280gr)</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn bg-gradient-success mt-2">
                            <i class="material-icons text-sm">save</i> Simpan Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========================= --}}
        {{-- TABEL DATA BAHAN BAKU     --}}
        {{-- ========================= --}}
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">

                    <h6 class="text-white text-capitalize ps-3 mb-0">Tabel Data Bahan Baku</h6>

                    <div class="me-3">
                        <form action="{{ route('manajemen.bahanbaku') }}" method="GET">

                            <div class="bg-white rounded d-flex align-items-center px-2" style="height: 40px; min-width: 220px;">

                                <i class="material-icons text-secondary text-sm">search</i>

                                <input type="text"
                                    name="search"
                                    class="form-control border-0 ps-2"
                                    placeholder="Cari bahan..."
                                    value="{{ request('search') }}"
                                    style="box-shadow: none !important; height: 100%; background: transparent;">

                                @if(request('search'))
                                <a href="{{ route('manajemen.bahanbaku') }}" class="text-danger d-flex align-items-center cursor-pointer" title="Reset">
                                    <i class="material-icons text-sm">close</i>
                                </a>
                                @endif

                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bahan Baku (Kode/Nama)</th>
                                <th class="text-secondary opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bahans as $bahan)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-info d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">inventory_2</i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $bahan->nama_bahan }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $bahan->kode_bahan }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle text-end pe-4">
                                    <a href="{{ route('manajemen.bahanbaku.edit', $bahan->id) }}"
                                        class="text-secondary font-weight-bold text-xs me-3"
                                        data-toggle="tooltip"
                                        title="Edit Bahan">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('manajemen.bahanbaku.destroy', $bahan->id) }}" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-link text-danger text-gradient p-0 m-0 text-xs font-weight-bold btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <span class="text-secondary text-sm">
                                        @if(request('search'))
                                        Data "{{ request('search') }}" tidak ditemukan.
                                        @else
                                        Belum ada data bahan baku.
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // SweetAlert Delete
        const deleteButtons = document.querySelectorAll(".btn-delete");
        deleteButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");
                Swal.fire({
                    title: "Hapus bahan ini?",
                    text: "Data akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Search Input Outline Fix (Agar label tidak menumpuk saat ada value)
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && searchInput.value !== "") {
            searchInput.parentElement.classList.add("is-filled");
        }
    });
</script>
@endpush
