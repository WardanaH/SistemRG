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

<div class="container-fluid py-4">
    <div class="row mb-5">

        {{-- ======================================= --}}
        {{-- BAGIAN KIRI: INFORMASI AKUN (PROFIL)    --}}
        {{-- ======================================= --}}
        <div class="col-lg-5 col-md-6 mb-4">
            <div class="card h-100 mt-4 shadow-sm">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-md-8 d-flex align-items-center">
                            <h6 class="mb-0">Informasi Akun</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            {{-- Menampilkan Role User --}}
                            @php
                                $roles = auth()->user()->getRoleNames();
                                $roleName = $roles->count() > 0 ? strtoupper($roles[0]) : 'USER';
                            @endphp
                            <span class="badge bg-gradient-info">{{ $roleName }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <div class="avatar avatar-xl position-relative bg-gradient-dark d-flex align-items-center justify-content-center rounded-circle" style="width: 74px; height: 74px;">
                                <i class="material-icons text-white" style="font-size: 36px;">person</i>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="mb-1 font-weight-bolder">
                                {{ auth()->user()->nama }}
                            </h5>
                            <p class="mb-0 font-weight-normal text-sm text-secondary">
                                {{ auth()->user()->email ?? auth()->user()->username }}
                            </p>
                        </div>
                    </div>

                    <hr class="horizontal gray-light my-4">

                    <ul class="list-group">
                        <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Nama Lengkap:</strong> &nbsp; {{ auth()->user()->nama }}</li>
                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Username:</strong> &nbsp; {{ auth()->user()->username }}</li>
                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Cabang:</strong> &nbsp; {{ auth()->user()->cabang->nama ?? 'Pusat' }}</li>
                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">No. Telepon:</strong> &nbsp; {{ auth()->user()->telepon ?? '-' }}</li>

                        <li class="list-group-item border-0 ps-0 mt-3 text-xs text-secondary"><strong class="text-dark">Dibuat:</strong> &nbsp; {{ auth()->user()->created_at->format('d-m-Y H:i') }}</li>
                        <li class="list-group-item border-0 ps-0 pb-0 text-xs text-secondary"><strong class="text-dark">Update Terakhir:</strong> &nbsp; {{ auth()->user()->updated_at->format('d-m-Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ======================================= --}}
        {{-- BAGIAN KANAN: FORM UBAH PASSWORD        --}}
        {{-- ======================================= --}}
        <div class="col-lg-7 col-md-6 mb-4">
            <div class="card h-100 mt-4 shadow-sm">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0">Ubah Password</h6>
                    <p class="text-sm text-secondary mb-0">Isi form di bawah ini jika Anda ingin mengubah password akun Anda.</p>
                </div>

                <div class="card-body p-3">
                    {{-- Ganti route() dengan route yang sesuai di web.php kamu nanti --}}
                    <form action="#" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Password Lama --}}
                        <div class="input-group input-group-outline mb-3 mt-2">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        @error('current_password')
                            <small class="text-danger text-xs mb-3 d-block mt-n2">{{ $message }}</small>
                        @enderror

                        {{-- Password Baru --}}
                        <div class="input-group input-group-outline mb-3 mt-4">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        @error('password')
                            <small class="text-danger text-xs mb-3 d-block mt-n2">{{ $message }}</small>
                        @enderror

                        {{-- Konfirmasi Password Baru --}}
                        <div class="input-group input-group-outline mb-3 mt-4">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn bg-gradient-info mb-0">
                                <i class="material-icons text-sm">save</i> Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Script agar label pada input outline tidak tumpang tindih
        // jika ada isinya atau saat diklik (Spesifik Material Dashboard)
        let inputs = document.querySelectorAll('.form-control');
        inputs.forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('is-focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('is-focused');
                if(this.value != "") {
                    this.parentElement.classList.add('is-filled');
                } else {
                    this.parentElement.classList.remove('is-filled');
                }
            });
        });
    });
</script>
@endpush
