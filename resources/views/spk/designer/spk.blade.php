@extends('spk.layout.app')

@section('content')

{{-- 1. SETUP VARIABEL MODE (CREATE / EDIT) --}}
@php
$isEdit = isset($spk);
$title = $isEdit ? 'Edit Data SPK' : 'Input SPK Baru';
$action = $isEdit ? route('spk.update', $spk->id) : route('spk.store');
@endphp

{{-- 2. ALERT SESSION --}}
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

@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Periksa kembali inputan Anda.",
            showConfirmButton: true
        });
    });
</script>
@endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">

            {{-- HEADER CARD --}}
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-{{ $isEdit ? 'info' : 'primary' }} shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">{{ $title }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    {{-- ================================================= --}}
                    {{-- FITUR BARU: SWITCH SPK BANTUAN (Hanya saat Create) --}}
                    {{-- ================================================= --}}
                    @if(!$isEdit)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-gray-100">
                                <div class="card-body p-3 d-flex align-items-center">
                                    <div class="form-check form-switch ps-0 mb-0">
                                        <input class="form-check-input ms-auto" type="checkbox" id="switchBantuan" name="is_bantuan" value="1"
                                            {{ old('is_bantuan') ? 'checked' : '' }}>
                                        <label class="form-check-label text-body ms-3 font-weight-bold" for="switchBantuan">
                                            Aktifkan Mode SPK Bantuan (Order dari Cabang Lain)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DROPDOWN CABANG ASAL (Hidden by Default) --}}
                    <div class="row mb-3 d-none" id="rowCabangAsal">
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Asal Cabang (Pengirim Order)</label>
                                <select name="asal_cabang_id" class="form-control" style="appearance: auto; padding-left: 10px;">
                                    <option value="" disabled selected>-- Pilih Cabang Asal --</option>
                                    @foreach($cabangLain as $cb)
                                    <option value="{{ $cb->id }}" {{ old('asal_cabang_id') == $cb->id ? 'selected' : '' }}>
                                        {{ $cb->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-danger text-xs">*Nomor SPK akan otomatis berawalan 'B' (Contoh: BBJM-001)</small>
                        </div>
                    </div>
                    @endif

                    {{-- ================================================= --}}
                    {{-- I. INFORMASI ORDER --}}
                    {{-- ================================================= --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Informasi Order</p>
                    <div class="row">
                        {{-- No SPK --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. SPK</label>
                                @if($isEdit)
                                <input type="text" class="form-control" value="{{ $spk->no_spk }}" readonly>
                                @else
                                <input type="text" name="dummy_no_spk" class="form-control" value="Generate Otomatis" readonly>
                                @endif
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control"
                                    value="{{ $isEdit ? \Carbon\Carbon::parse($spk->tanggal_spk)->format('d-m-Y') : date('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        {{-- Jenis Order --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label mb-1 ms-1 text-xs text-secondary">Jenis Order:</label>
                            <div class="d-flex align-items-center gap-3">
                                @foreach(['outdoor', 'indoor', 'multi'] as $jenis)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="{{ $jenis }}" value="{{ $jenis }}"
                                        {{ (old('jenis_order', $isEdit ? $spk->jenis_order_spk : '') == $jenis) ? 'checked' : '' }} required>
                                    <label class="form-check-label text-capitalize" for="{{ $jenis }}">{{ $jenis }}</label>
                                </div>
                                @endforeach
                            </div>
                            @error('jenis_order') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- ================================================= --}}
                    {{-- II. DATA PELANGGAN --}}
                    {{-- ================================================= --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">II. Data Pelanggan</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled @error('nama_pelanggan') is-invalid @enderror">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control"
                                    value="{{ old('nama_pelanggan', $isEdit ? $spk->nama_pelanggan : '') }}" required>
                            </div>
                            @error('nama_pelanggan') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled @error('no_telp') is-invalid @enderror">
                                <label class="form-label">No. Telepon (WA)</label>
                                {{-- Logic: Jika Edit, Input Number. Jika Create, Text Readonly (kecuali Bantuan diaktifkan via JS) --}}
                                <input type="{{ $isEdit ? 'number' : 'text' }}" name="no_telp" id="inputNoTelp" class="form-control"
                                    value="{{ old('no_telp', $isEdit ? $spk->no_telepon : 'Di Isi Oleh Admin') }}"
                                    {{ $isEdit ? 'required' : 'readonly' }}>
                            </div>
                            @error('no_telp')
                            <div class="text-danger text-xs mt-1"><i class="fa fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- ================================================= --}}
                    {{-- III. DETAIL PESANAN --}}
                    {{-- ================================================= --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">III. Detail Pesanan</p>
                    <div class="border p-3 border-radius-lg mb-3">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group input-group-outline is-filled @error('nama_file') is-invalid @enderror">
                                    <label class="form-label">Nama File</label>
                                    <input type="text" name="nama_file" class="form-control"
                                        value="{{ old('nama_file', $isEdit ? $spk->nama_file : '') }}" required>
                                </div>
                                @error('nama_file') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Ukuran P --}}
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Ukuran (P)</label>
                                    <input type="number" step="0.01" name="ukuran_p" class="form-control"
                                        value="{{ old('ukuran_p', $isEdit ? $spk->ukuran_panjang : '') }}" required>
                                </div>
                            </div>
                            {{-- Ukuran L --}}
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Ukuran (L)</label>
                                    <input type="number" step="0.01" name="ukuran_l" class="form-control"
                                        value="{{ old('ukuran_l', $isEdit ? $spk->ukuran_lebar : '') }}" required>
                                </div>
                            </div>

                            {{-- Bahan --}}
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline is-filled @error('bahan_id') is-invalid @enderror">
                                    <select class="select2 form-control" name="bahan_id" style="width: 100%;" required>
                                        <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>Pilih Bahan</option>
                                        @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}"
                                            {{ (old('bahan_id', $isEdit ? $spk->bahan_id : '') == $bahan->id) ? 'selected' : '' }}>
                                            {{ $bahan->nama_bahan }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('bahan_id') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                            </div>

                            {{-- Qty --}}
                            <div class="col-md-1 mb-3">
                                <div class="input-group input-group-outline is-filled @error('qty') is-invalid @enderror">
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="qty" class="form-control"
                                        value="{{ old('qty', $isEdit ? $spk->kuantitas : '') }}" required min="1">
                                </div>
                            </div>

                            {{-- Finishing --}}
                            <div class="col-md-4 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <select name="finishing" class="form-control select2">
                                        <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>Pilih Finishing (Opsional)</option>
                                        @foreach($finishings as $fin)
                                        <option value="{{ $fin->nama_finishing }}"
                                            {{ (old('finishing', $isEdit ? $spk->finishing : '') == $fin->nama_finishing) ? 'selected' : '' }}>
                                            {{ $fin->nama_finishing }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Catatan / Keterangan</label>
                                    <input type="text" name="catatan" class="form-control"
                                        value="{{ old('catatan', $isEdit ? $spk->keterangan : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- ================================================= --}}
                    {{-- IV. PENANGGUNG JAWAB --}}
                    {{-- ================================================= --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">IV. Penanggung Jawab</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled @error('designer_id') is-invalid @enderror">
                                <select name="designer_id" class="form-control select2" required>
                                    <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>Pilih Designer</option>
                                    @foreach($designers as $designer)
                                    <option value="{{ $designer->id }}"
                                        {{ (old('designer_id', $isEdit ? $spk->designer_id : Auth::id()) == $designer->id) ? 'selected' : '' }}>
                                        {{ $designer->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('designer_id') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled @error('operator_id') is-invalid @enderror">
                                <select name="operator_id" class="form-control select2" required>
                                    <option value="" disabled {{ !$isEdit ? 'selected' : '' }}>Pilih Operator</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}"
                                        {{ (old('operator_id', $isEdit ? $spk->operator_id : '') == $operator->id) ? 'selected' : '' }}>
                                        {{ $operator->nama }} ({{ $operator->roles->pluck('name')->implode(',') }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('operator_id') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            @if($isEdit)
                            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                            @endif
                            <button type="submit" class="btn bg-gradient-{{ $isEdit ? 'info' : 'primary' }}">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;{{ $isEdit ? 'Simpan Perubahan' : 'Buat SPK' }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC SWITCH BANTUAN --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const switchBantuan = document.getElementById('switchBantuan');

        // Elemen yang dipengaruhi
        const rowCabang = document.getElementById('rowCabangAsal');
        const inputNoSpk = document.querySelector('input[name="dummy_no_spk"]');
        const inputNoTelp = document.getElementById('inputNoTelp');

        function toggleBantuan() {
            if (!switchBantuan) return; // Guard clause jika di mode Edit (elemen tidak ada)

            if (switchBantuan.checked) {
                // Mode BANTUAN Aktif
                rowCabang.classList.remove('d-none');

                // Ubah Visual No SPK
                if (inputNoSpk) inputNoSpk.value = "Generate Otomatis (Format BBJM...)";

                // Ubah Input Telp jadi Wajib & Bisa diedit (Karena dari cabang lain)
                inputNoTelp.readOnly = false;
                inputNoTelp.type = "number";
                inputNoTelp.value = "";
                inputNoTelp.placeholder = "Masukkan No WA Pelanggan";
                inputNoTelp.setAttribute('required', 'required');

            } else {
                // Mode BIASA
                rowCabang.classList.add('d-none');

                if (inputNoSpk) inputNoSpk.value = "Generate Otomatis";

                // Kembalikan Input Telp jadi Readonly
                inputNoTelp.readOnly = true;
                inputNoTelp.type = "text";
                inputNoTelp.value = "Di Isi Oleh Admin";
                inputNoTelp.removeAttribute('required');
            }
        }

        if (switchBantuan) {
            switchBantuan.addEventListener('change', toggleBantuan);
            // Run on load (untuk handle old value saat validasi error)
            toggleBantuan();
        }
    });
</script>
@endpush

@endsection
