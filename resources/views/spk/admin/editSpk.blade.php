@extends('spk.layout.app')

@section('content')

{{-- Script SweetAlert (Opsional, sama seperti create) --}}
@if (session('error')) ... @endif

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Edit Data SPK: {{ $spk->no_spk }}</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk.update', $spk->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- Penting untuk Update --}}

                    {{-- I. INFORMASI ORDER --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Informasi Order</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. SPK</label>
                                <input type="text" class="form-control" value="{{ $spk->no_spk }}" readonly >
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d-m-Y') }}" readonly >
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label mb-1 ms-1 text-xs text-secondary">Jenis Order:</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="outdoor" value="outdoor" {{ $spk->jenis_order_spk == 'outdoor' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="outdoor">Outdoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="indoor" value="indoor" {{ $spk->jenis_order_spk == 'indoor' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="indoor">Indoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="multi" value="multi" {{ $spk->jenis_order_spk == 'multi' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multi">Multi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- II. DATA PELANGGAN --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">II. Data Pelanggan</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" value="{{ old('nama_pelanggan', $spk->nama_pelanggan) }}" required>
                            </div>
                        </div>

                        {{-- EDIT NOMOR TELEPON --}}
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled @error('no_telp') is-invalid @enderror">
                                <label class="form-label">No. Telepon (WA) - Wajib Diisi</label>
                                <input type="number" name="no_telp" class="form-control" value="{{ old('no_telp', $spk->no_telepon) }}" required>
                            </div>
                            @error('no_telp')
                                <div class="text-danger text-xs mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- III. DETAIL PESANAN --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">III. Detail Pesanan</p>
                    <div class="border p-3 border-radius-lg mb-3">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Nama File</label>
                                    <input type="text" name="nama_file" class="form-control" value="{{ old('nama_file', $spk->nama_file) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Ukuran (P)</label>
                                    <input type="number" step="0.01" name="ukuran_p" class="form-control" value="{{ old('ukuran_p', $spk->ukuran_panjang) }}" required>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Ukuran (L)</label>
                                    <input type="number" step="0.01" name="ukuran_l" class="form-control" value="{{ old('ukuran_l', $spk->ukuran_lebar) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <select class="select2 form-control" name="bahan_id" style="width: 100%;" required>
                                        @foreach($bahans as $bahan)
                                            <option value="{{ $bahan->id }}" {{ $spk->bahan_id == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->nama_bahan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="qty" class="form-control" value="{{ old('qty', $spk->kuantitas) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="input-group input-group-outline is-filled">
                                    <select name="finishing" class="form-control">
                                        <option class="select2" value="">-- Pilih Finishing --</option>
                                        @foreach($finishings as $fin)
                                            <option value="{{ $fin->nama_finishing }}" {{ $spk->finishing == $fin->nama_finishing ? 'selected' : '' }}>
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
                                    <input type="text" name="catatan" class="form-control" value="{{ old('catatan', $spk->keterangan) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- IV. PENANGGUNG JAWAB --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">IV. Penanggung Jawab</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <select class="select2" name="designer_id" class="form-control" required>
                                    @foreach($designers as $designer)
                                        <option value="{{ $designer->id }}" {{ $spk->designer_id == $designer->id ? 'selected' : '' }}>
                                            {{ $designer->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <select class="select2" name="operator_id" class="form-control" required>
                                    @foreach($operators as $operator)
                                        <option value="{{ $operator->id }}" {{ $spk->operator_id == $operator->id ? 'selected' : '' }}>
                                            {{ $operator->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="{{ route('spk.index') }}" class="btn btn-outline-secondary me-2">Batal</a>
                            <button type="submit" class="btn bg-gradient-info">Simpan Perubahan</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
