@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert --}}
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
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                {{-- Gunakan Dark untuk membedakan visual sedikit bahwa ini menu Bantuan, tapi layout tetap sama --}}
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Input SPK Bantuan (Eksternal)</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk-bantuan.store') }}" method="POST">
                    @csrf

                    {{-- KHUSUS SPK BANTUAN: INPUT ASAL CABANG --}}
                    <div class="alert alert-secondary text-white text-sm mb-3" role="alert">
                        <strong>Info:</strong> SPK ini untuk order dari cabang lain. Nomor SPK akan berawalan <strong>'B'</strong>.
                    </div>
                    <p class="text-sm text-uppercase font-weight-bold mb-2">Asal Cabang Peminta Bantuan</p>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="input-group input-group-outline is-filled">
                                <select name="asal_cabang_id" class="form-control select2" style="appearance: auto; padding-left: 10px;" required>
                                    <option value="" disabled selected>-- Pilih Cabang Pengirim --</option>
                                    @foreach($cabangLain as $cb)
                                    <option value="{{ $cb->id }}" {{ old('asal_cabang_id') == $cb->id ? 'selected' : '' }}>
                                        {{ $cb->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- I. INFORMASI ORDER (Susunan Sama persis dengan SPK Biasa) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Informasi Order</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. SPK</label>
                                <input type="text" class="form-control" value="Generate Otomatis (Format BBJM...)" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label mb-1 ms-1 text-xs text-secondary">Jenis Order:</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="outdoor" value="outdoor" {{ old('jenis_order') == 'outdoor' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="outdoor">Outdoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="indoor" value="indoor" {{ old('jenis_order') == 'indoor' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="indoor">Indoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="multi" value="multi" {{ old('jenis_order') == 'multi' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multi">Multi</label>
                                </div>
                            </div>
                            @error('jenis_order') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- II. DATA PELANGGAN (Susunan Sama persis) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">II. Data Pelanggan</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline @error('nama_pelanggan') is-invalid @enderror">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan') }}" required>
                            </div>
                            @error('nama_pelanggan') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>

                        {{-- PERBEDAAN: No Telp di sini WAJIB dan BISA DIEDIT --}}
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline @error('no_telepon') is-invalid @enderror">
                                <label class="form-label">No. Telepon (WA)</label>
                                <input type="number" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" required>
                            </div>
                            @error('no_telepon')
                            <div class="text-danger text-xs mt-1">
                                <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- III. DETAIL PESANAN (Susunan Sama persis) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">III. Detail Pesanan</p>
                    <div class="border p-3 border-radius-lg mb-3">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group input-group-outline @error('nama_file') is-invalid @enderror">
                                    <label class="form-label">Nama File</label>
                                    <input type="text" name="nama_file" class="form-control" value="{{ old('nama_file') }}" required>
                                </div>
                                @error('nama_file') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- Ukuran P --}}
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline @error('ukuran_p') is-invalid @enderror">
                                    <label class="form-label">Ukuran (P)</label>
                                    <input type="number" step="0.01" name="ukuran_p" class="form-control" value="{{ old('ukuran_p') }}" required>
                                </div>
                            </div>
                            {{-- Ukuran L --}}
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline @error('ukuran_l') is-invalid @enderror">
                                    <label class="form-label">Ukuran (L)</label>
                                    <input type="number" step="0.01" name="ukuran_l" class="form-control" value="{{ old('ukuran_l') }}" required>
                                </div>
                            </div>

                            {{-- Dropdown Bahan --}}
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline @error('bahan_id') is-invalid @enderror">
                                    <select class="select2" name="bahan_id" class="form-control" style="appearance: auto; padding-left: 10px;" required>
                                        <option value="" disabled selected>Pilih Bahan</option>
                                        @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}" {{ old('bahan_id') == $bahan->id ? 'selected' : '' }}>
                                            {{ $bahan->nama_bahan }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('bahan_id') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                            </div>

                            {{-- Qty --}}
                            <div class="col-md-1 mb-3">
                                <div class="input-group input-group-outline @error('qty') is-invalid @enderror">
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="qty" class="form-control" value="{{ old('qty') }}" min="1" required>
                                </div>
                            </div>

                            {{-- Finishing --}}
                            <div class="col-md-4 mb-3">
                                <select name="finishing" class="form-control select2" style="appearance: auto; padding-left: 10px;">
                                    <option value="" disabled selected>Pilih Finishing (Opsional)</option>
                                    @foreach($finishings as $fin)
                                    <option value="{{ $fin->nama_finishing }}">{{ $fin->nama_finishing }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Catatan / Keterangan</label>
                                    <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- IV. PENANGGUNG JAWAB (Susunan Sama persis) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">IV. Penanggung Jawab</p>
                    <div class="row">
                        {{-- Designer otomatis terisi user login, tapi kita tampilkan dropdown readonly agar sama --}}
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Designer (Anda)</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->nama }}" readonly>
                                <input type="hidden" name="designer_id" value="{{ Auth::id() }}">
                            </div>
                        </div>

                        {{-- Dropdown Operator --}}
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline @error('operator_id') is-invalid @enderror">
                                <select class="select2" name="operator_id" class="form-control" style="appearance: auto; padding-left: 10px;" required>
                                    <option value="" disabled selected>Pilih Operator</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}" {{ old('operator_id') == $operator->id ? 'selected' : '' }}>
                                        {{ $operator->nama }} ({{ $operator->roles->pluck('name')->implode(',')}})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('operator_id') <small class="text-danger text-xs">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-icons text-sm">save</i>&nbsp;&nbsp;Simpan SPK Bantuan
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
