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

{{-- ========================= --}}
{{-- 1. FORM TAMBAH USER       --}}
{{-- ========================= --}}
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tambah User Baru</h6>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('manajemen.user.store') }}">
                    @csrf

                    <p class="text-sm text-uppercase font-weight-bold mb-3">Informasi Akun</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <select name="role" class="form-control" style="appearance: auto; padding-left: 10px;">
                                    <option value="" disabled selected>Pilih Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <select name="cabang_id" class="form-control" style="appearance: auto; padding-left: 10px;">
                                    <option value="" disabled selected>Pilih Cabang</option>
                                    @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-3">
                    <p class="text-sm text-uppercase font-weight-bold mb-3">Informasi Pribadi</p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="telepon" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Gaji</label>
                                <input type="text" name="gaji" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn bg-gradient-success mt-2">
                            <i class="material-icons text-sm">save</i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- 2. IMPORT EXCEL SECTION   --}}
{{-- ========================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border border-success">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-success"><i class="fa fa-file-excel-o me-2"></i>Import Data Karyawan (.xlsx)</h6>
                        <small class="text-muted">Gunakan fitur ini untuk upload data massal.</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <form action="{{ route('manajemen.user.import') }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-end align-items-center gap-2">
                            @csrf
                            <input type="file" name="file_excel" class="form-control border p-1" style="max-width: 250px;" required>
                            <button type="submit" class="btn btn-success btn-sm mb-0">Upload</button>
                            <a href="{{ asset('templates/contoh-import.xlsx') }}" class="btn btn-outline-secondary btn-sm mb-0" download>
                                <i class="fa fa-download"></i> Format
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- 3. TABEL DATA USER        --}}
{{-- ========================= --}}
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tabel Manajemen User</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">User / Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role & Cabang</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kontak / Alamat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Gaji</th>
                                <th class="text-secondary opacity-7">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div>
                                            <div class="avatar avatar-sm me-3 border-radius-lg bg-gradient-info d-flex align-items-center justify-content-center text-white font-weight-bold">
                                                {{ substr($user->nama, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $user->nama }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                            <p class="text-xs text-secondary mb-0">@ {{ $user->username }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <p class="text-xs font-weight-bold mb-0 text-uppercase">{{ $user->getRoleNames()->implode(', ') }}</p>
                                    <p class="text-xs text-secondary mb-0">Cabang: {{ $user->cabang->nama ?? 'Semua Cabang' }}</p>
                                </td>

                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $user->telepon ?? '-' }}</p>
                                    <span class="text-secondary text-xs" data-bs-toggle="tooltip" title="{{ $user->alamat }}">
                                        {{ Str::limit($user->alamat ?? '-', 20) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="text-secondary text-xs font-weight-bold">
                                        Rp {{ number_format((float)$user->gaji, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="align-middle">
                                    {{-- Di dalam @foreach($users as $user) --}}
                                    <button type="button"
                                        class="btn btn-link text-dark px-3 mb-0 btn-edit-user"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditUser"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->nama }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->roles->first()->name ?? '' }}"
                                        data-cabang="{{ $user->cabang_id }}"
                                        data-telepon="{{ $user->telepon }}"
                                        data-gaji="{{ $user->gaji }}"
                                        data-alamat="{{ $user->alamat }}"
                                        data-url="{{ route('manajemen.user.update', $user->id) }}">
                                        <i class="material-icons text-sm me-2">edit</i>Edit
                                    </button>

                                    <form method="POST" action="{{ route('manajemen.user.destroy', $user) }}" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-link text-danger text-gradient p-0 m-0 text-xs font-weight-bold btn-delete">
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

{{-- INCLUDE PARTIAL MODAL --}}
@include('spk.manajemen.partials.modal_edit_user')

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Handle Edit User Modal
        const editButtons = document.querySelectorAll('.btn-edit-user');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // 1. Ambil data dari atribut data-* tombol
                const id = this.dataset.id;
                const name = this.dataset.name;
                const username = this.dataset.username;
                const email = this.dataset.email;
                const role = this.dataset.role;
                const cabang = this.dataset.cabang;
                const telepon = this.dataset.telepon;
                const gaji = this.dataset.gaji;
                const alamat = this.dataset.alamat;
                const url = this.dataset.url;

                // 2. Isi value ke dalam input form modal
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_telepon').value = telepon;
                document.getElementById('edit_gaji').value = gaji;
                document.getElementById('edit_alamat').value = alamat;

                // 3. Set Select Option (Role & Cabang)
                // Kita set value select, browser akan otomatis memilih option yg cocok
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_cabang_id').value = cabang;

                // 4. Update Action URL Form
                document.getElementById('formEditUser').action = url;
            });
        });
    });
</script>

{{-- SCRIPT SWEETALERT DELETE (SAMA SEPERTI SEBELUMNYA) --}}
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
    });
</script>

@endsection