@extends('spk.layout.app')

@section('content')

{{-- ========================= --}}
{{-- SWEETALERT SESSION ALERT --}}
{{-- ========================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "{{ session('success') }}",
            showConfirmButton: true
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

<div class="card">
    <div class="card-header">
        <h3>Manajemen Cabang</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Tambah Data Cabang</h6>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('manajemen.cabang.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Kode Cabang</label>
                                        <input type="text" name="kode" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Nama Cabang</label>
                                        <input type="text" name="nama" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Email Cabang</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="input-group input-group-outline">
                                        <select name="jenis" class="form-control" style="appearance: auto; padding-left: 10px;">
                                            <option value="" disabled selected>Pilih Jenis</option>
                                            <option value="pusat">Pusat</option>
                                            <option value="cabang">Cabang</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Telepon Cabang</label>
                                        <input type="text" name="telepon" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Alamat Cabang</label>
                                        <input type="text" name="alamat" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn bg-gradient-success mb-0">
                                        <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Simpan Cabang
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-info shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                            <h6 class="text-white text-capitalize ps-3 mb-0">Tabel Data Cabang</h6>
                            {{-- <a href="{{ route('manajemen.cabang.create') }}" class="btn btn-sm btn-light me-3 mb-0">Tambah</a> --}}
                        </div>
                    </div>

                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cabang (Kode/Nama)</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kontak</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Alamat</th>
                                        <th class="text-secondary opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cabangs as $cabang)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-dark d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">store</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $cabang->nama }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $cabang->kode }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ ucfirst($cabang->jenis) }}</p>
                                            <p class="text-xs text-secondary mb-0">Cabang</p>
                                        </td>

                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $cabang->email }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $cabang->telepon }}</p>
                                        </td>

                                        <td class="align-middle">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ Str::limit($cabang->alamat, 30) }} </span>
                                        </td>

                                        <td class="align-middle">
                                            <a href="{{ route('manajemen.cabang.edit', $cabang) }}"
                                                class="text-secondary font-weight-bold text-xs me-3"
                                                data-toggle="tooltip"
                                                data-original-title="Edit user">
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('manajemen.cabang.destroy', $cabang) }}" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link text-danger text-gradient p-0 m-0 text-xs font-weight-bold btn-delete">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- SWEETALERT KONFIRMASI HAPUS --}}
{{-- ============================= --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                let form = this.closest("form");

                Swal.fire({
                    title: "Yakin hapus cabang ini?",
                    text: "Data cabang yang dihapus tidak bisa dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#e3342f",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@endsection
