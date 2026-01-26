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

<div class="container">
    <h3>Manajemen Cabang</h3>
    <hr>

    <form method="POST" action="{{ route('manajemen.cabang.store') }}" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-3 mb-2">
                <input type="text" name="kode" class="form-control" placeholder="Kode Cabang" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" name="nama" class="form-control" placeholder="Nama Cabang" required>
            </div>
            <div class="col-md-3 mb-2">
                <input type="text" name="email" class="form-control" placeholder="Email Cabang">
            </div>
            <div class="col-md-3 mb-2">
                <select name="jenis" class="form-control">
                    <option value="pusat">Pusat</option>
                    <option value="cabang">Cabang</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-2">
                <input type="text" name="telepon" class="form-control" placeholder="Telepon Cabang">
            </div>
            <div class="col-md-6 mb-2">
                <input type="text" name="alamat" class="form-control" placeholder="Alamat Cabang">
            </div>
        </div>

        <button class="btn btn-primary mt-2">Tambah Cabang</button>
    </form>

    <table class="table table-bordered table-striped styletable">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cabangs as $cabang)
            <tr>
                <td>{{ $cabang->kode }}</td>
                <td>{{ $cabang->nama }}</td>
                <td>{{ ucfirst($cabang->jenis) }}</td>
                <td>{{ $cabang->email }}</td>
                <td>{{ $cabang->telepon }}</td>
                <td>{{ $cabang->alamat }}</td>
                <td>

                    <a href="{{ route('manajemen.cabang.edit', $cabang) }}" class="btn btn-warning btn-sm">Edit</a>

                    {{-- ============================= --}}
                    {{-- DELETE FORM + SWEETALERT --}}
                    {{-- ============================= --}}
                    <form method="POST" action="{{ route('manajemen.cabang.destroy', $cabang) }}" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">
                            Hapus
                        </button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
