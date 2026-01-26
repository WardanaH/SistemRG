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

{{-- CSS BIAR TOMBOL EDIT-HAPUS TIDAK NUMPUK --}}
<style>
    /* Paksa kolom Aksi lebih lebar */
    table th:last-child,
    table td:last-child {
        min-width: 130px;
        white-space: nowrap;
    }

    /* Group tombol agar sejajar */
    .aksi-group {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    /* Form delete jangan memanjang */
    .aksi-group form {
        margin: 0;
        padding: 0;
    }
</style>


<div class="card shadow-sm">
    <div class="card-header">
        <h3>Manajemen User</h3>
    </div>
    <div class="card-body">
        <div class="container">
            <form method="POST" action="{{ route('manajemen.user.store') }}" class="mb-4">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="role" class="form-control">
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="telepon" class="form-control" placeholder="Telepon">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="gaji" class="form-control" placeholder="Gaji">
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text" name="alamat" class="form-control" placeholder="Alamat">
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="cabang_id" class="form-control">
                            @foreach($cabangs as $c)
                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button class="btn btn-primary mt-2">Tambah User</button>
            </form>

            <div class="card mb-4 border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Import Karyawan (.xlsx)</h5>

                        <a href="{{ asset('templates/contoh-import.xlsx') }}" class="btn btn-outline-primary btn-sm" download>
                            <i class="fa fa-download"></i> Download Format Excel
                        </a>
                    </div>

                    <form action="{{ route('manajemen.user.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="file_excel" class="form-control" required>
                            <button type="submit" class="btn btn-primary">Upload & Proses</button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            * Silakan download format di atas sebagai acuan pengisian data.
                        </small>
                    </form>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="table table-bordered table-striped styletable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Cabang</th>
                            <th>Telepon</th>
                            <th>Gaji</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleNames()->implode(', ') }}</td>
                            <td>{{ $user->cabang->nama ?? '-' }}</td>
                            <td>{{ $user->telepon ?? '-' }}</td>
                            <td>{{ $user->gaji ?? '-' }}</td>
                            <td>{{ $user->alamat ?? '-' }}</td>

                            {{-- A K S I --}}
                            <td>
                                <div class="aksi-group">
                                    <a href="{{ route('manajemen.user.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form method="POST" action="{{ route('manajemen.user.destroy', $user) }}" class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- SWEETALERT DELETE --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const deleteButtons = document.querySelectorAll(".btn-delete");

        deleteButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                let form = this.closest("form");

                Swal.fire({
                    title: "Yakin hapus user ini?",
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal",
                    position: "center"
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
