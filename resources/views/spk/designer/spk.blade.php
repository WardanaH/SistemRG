@extends('spk.layout.app')

@section('content')

{{-- Tambahkan SweetAlert Session Script di sini jika belum ada di layout --}}

<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Form Input SPK</h6>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('designer.spk.store') }}" method="POST"> @csrf

                    {{-- I. INFORMASI ORDER (SAMA SEPERTI SEBELUMNYA) --}}
                    {{-- ... (Kode Bagian I & II Anda tidak berubah) ... --}}

                    {{-- Paste kode Bagian I & II di sini --}}
                    {{-- ... --}}

                    {{-- CONTOH BAGIAN I (Singkat) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">I. Informasi Order</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            {{-- NO SPK (SAMA) --}}
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">No. SPK</label>
                                @php
                                $fullKode = Auth::user()->cabang->kode ?? 'CBG-XXX';
                                $prefix = \Illuminate\Support\Str::after($fullKode, '-');
                                $randomNumbers = mt_rand(100000, 999999);
                                $generatedSPK = $prefix . '-' . $randomNumbers;
                                @endphp
                                <input type="text" name="no_spk" class="form-control" value="{{ $generatedSPK }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {{-- TANGGAL (SAMA) --}}
                            <div class="input-group input-group-outline is-filled">
                                <label class="form-label">Tanggal</label>
                                <input type="text" name="tanggal" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            {{-- RADIO BUTTON JENIS ORDER (SAMA) --}}
                            <label class="form-label mb-1 ms-1 text-xs text-secondary">Jenis Order:</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="outdoor" value="outdoor" required>
                                    <label class="form-check-label" for="outdoor">Outdoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="indoor" value="indoor">
                                    <label class="form-check-label" for="indoor">Indoor</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jenis_order" id="multi" value="multi">
                                    <label class="form-check-label" for="multi">Multi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN II (DATA PELANGGAN - SAMA) --}}
                    {{-- ... Copy paste kode Anda yang tadi ... --}}
                    <hr class="horizontal dark my-2">
                    <p class="text-sm text-uppercase font-weight-bold mb-2">II. Data Pelanggan</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- III. DETAIL PESANAN (UPDATE: BAHAN JADI DROPDOWN) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">III. Detail Pesanan</p>

                    <div class="border p-3 border-radius-lg mb-3">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Nama File</label>
                                    <input type="text" name="nama_file" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Ukuran (P)</label>
                                    <input type="number" name="ukuran_p" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Ukuran (L)</label>
                                    <input type="number" name="ukuran_l" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <select name="bahan_id" class="form-control select2" style="appearance: auto; padding-left: 10px;" required>
                                        <option value="" disabled selected>Pilih Bahan</option>
                                        @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}">
                                            {{ $bahan->nama_bahan }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="qty" class="form-control" required min="1">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Finishing</label>
                                    <input type="text" name="finishing" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Catatan / Keterangan</label>
                                    <input type="text" name="catatan" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="horizontal dark my-2">

                    {{-- IV. PENANGGUNG JAWAB (UPDATE: USER DROPDOWN) --}}
                    <p class="text-sm text-uppercase font-weight-bold mb-2">IV. Penanggung Jawab</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <select name="designer_id" class="form-control select2" style="appearance: auto; padding-left: 10px;" required>
                                    <option value="" disabled selected>Pilih Designer</option>
                                    @foreach($designers as $designer)
                                    <option value="{{ $designer->id }}" {{ Auth::id() == $designer->id ? 'selected' : '' }}>
                                        {{ $designer->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="text-muted text-xs ms-1">*Default terpilih: Anda sendiri</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-outline">
                                <select name="operator_id" class="form-control select2" style="appearance: auto; padding-left: 10px;" required>
                                    <option value="" disabled selected>Pilih Operator</option>
                                    @foreach($operators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->nama }} ({{ $operator->roles->pluck('name')->implode(',') }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="material-icons text-sm">print</i>&nbsp;&nbsp;Buat & Cetak SPK
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
