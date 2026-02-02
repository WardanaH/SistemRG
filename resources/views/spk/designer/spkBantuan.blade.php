@extends('spk.layout.app')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">
                        <i class="material-icons opacity-10">input</i> Input SPK Bantuan (Eksternal)
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('spk-bantuan.store') }}" method="POST">
                    @csrf

                    {{-- HEADER KHUSUS BANTUAN --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-secondary text-white text-sm" role="alert">
                                <strong>Mode Bantuan:</strong> Nomor SPK akan otomatis berawalan <strong>'B'</strong>.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Asal Order (Dari Cabang Mana?)</label>
                                <select name="asal_cabang_id" class="form-control" required style="appearance: auto;">
                                    <option value="" disabled selected>-- Pilih Cabang Pengirim --</option>
                                    @foreach($cabangLain as $cb)
                                        <option value="{{ $cb->id }}">{{ $cb->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Detail Order</h6>

                    {{-- JENIS ORDER --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-xs">Kategori:</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="out" value="outdoor" required>
                                    <label class="custom-control-label" for="out">Outdoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="in" value="indoor">
                                    <label class="custom-control-label" for="in">Indoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="mul" value="multi">
                                    <label class="custom-control-label" for="mul">Multi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama File</label>
                                <input type="text" name="nama_file" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    {{-- UKURAN & BAHAN --}}
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">P (cm)</label>
                                <input type="number" step="0.01" name="ukuran_p" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">L (cm)</label>
                                <input type="number" step="0.01" name="ukuran_l" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Bahan</label>
                                <select name="bahan_id" class="form-control select2" required style="width:100%">
                                    <option value="" disabled selected>Pilih Bahan</option>
                                    @foreach($bahans as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Qty</label>
                                <input type="number" name="qty" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Finishing</label>
                                <select name="finishing" class="form-control">
                                    <option value="" disabled selected>Pilih...</option>
                                    @foreach($finishings as $f)
                                        <option value="{{ $f->nama_finishing }}">{{ $f->nama_finishing }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="catatan" class="form-control">
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- INFO KONTAK (WAJIB ISI) --}}
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Info Kontak & Produksi</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Nama Pelanggan (Sesuai Nota Asal)</label>
                                <input type="text" name="nama_pelanggan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. Telepon (Wajib)</label>
                                <input type="number" name="no_telepon" class="form-control" required placeholder="08xxx">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Operator (Pelaksana)</label>
                                <select name="operator_id" class="form-control" required style="appearance: auto;">
                                    <option value="" disabled selected>Pilih Operator</option>
                                    @foreach($operators as $op)
                                        <option value="{{ $op->id }}">{{ $op->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn bg-gradient-dark">
                            <i class="material-icons text-sm">save</i> Simpan SPK Bantuan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
