@extends('inventaris.layouts.app')

@section('title', 'Permintaan Pengiriman Barang')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIF
    ===================== --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
    @endif

    {{-- =====================
    FORM PERMINTAAN
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Permintaan Pengiriman Barang ke Gudang Pusat
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('gudangcabang.permintaan.store') }}" method="POST">
                        @csrf

                        {{-- TANGGAL --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Permintaan</label>
                                <input type="date"
                                       name="tanggal_permintaan"
                                       class="form-control border border-2 border-grey"
                                       required>
                            </div>
                        </div>

                        {{-- DAFTAR BARANG --}}
                        <div id="barang-wrapper">

                            <div class="row barang-item mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Barang</label>
                                    <select name="barang[0][gudang_barang_id]"
                                            class="form-control select2"
                                            required>
                                        <option value="">Pilih Barang</option>
                                        @foreach($barangs as $barang)
                                            <option value="{{ $barang->id }}">
                                                {{ $barang->nama_bahan }} ({{ $barang->satuan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Jumlah</label>
                                    <input type="text"
                                        name="barang[0][jumlah]"
                                        class="form-control border border-2 border-grey"
                                        inputmode="decimal"
                                        {{-- placeholder="contoh: 1,5" --}}
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text"
                                           name="barang[0][keterangan]"
                                           class="form-control border border-2 border-grey"
                                           placeholder="Opsional">
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button"
                                            class="btn btn-danger btn-remove-barang d-none">
                                        Hapus
                                    </button>
                                </div>
                            </div>

                        </div>

                        <button type="button"
                                id="btnTambahBarang"
                                class="btn btn-outline-primary mb-3">
                            + Tambah Barang
                        </button>

                        {{-- CATATAN --}}
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan"
                                      class="form-control border border-2 border-grey"
                                      rows="2"></textarea>
                        </div>

                        <div class="text-end">
                            <button class="btn bg-gradient-success mb-0">
                                <i class="material-icons text-sm">send</i>
                                &nbsp;Kirim Permintaan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Riwayat Permintaan Pengiriman
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $i => $row)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $row->kode_permintaan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->tanggal_permintaan)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        @if ($row->status === 'Menunggu')
                                            <span class="badge bg-warning">
                                                {{ $row->status }}
                                            </span>

                                        @elseif ($row->status === 'Diproses')
                                            <span class="badge bg-primary">
                                                {{ $row->status }}
                                            </span>

                                        @elseif ($row->status === 'Selesai')
                                            <span class="text-secondary">
                                                {{ $row->status }}
                                            </span>

                                        @else
                                            <span class="text-muted">
                                                {{ $row->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Belum ada permintaan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
let indexBarang = 1;

$(document).ready(function () {
    $('.select2').select2();
});

$('#btnTambahBarang').on('click', function () {

    let html = `
    <div class="row barang-item mb-3">
        <div class="col-md-4">
            <select name="barang[${indexBarang}][gudang_barang_id]"
                    class="form-control select2"
                    required>
                <option value="">Pilih Barang</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}">
                        {{ $barang->nama_bahan }} ({{ $barang->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="text"
                name="barang[${indexBarang}][jumlah]"
                class="form-control border border-2 border-grey"
                inputmode="decimal"
                placeholder="contoh: 1,5"
                required>

        </div>

        <div class="col-md-4">
            <input type="text"
                   name="barang[${indexBarang}][keterangan]"
                   class="form-control border border-2 border-grey"
                   placeholder="Keterangan">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button"
                    class="btn btn-danger btn-remove-barang">
                Hapus
            </button>
        </div>
    </div>
    `;

    $('#barang-wrapper').append(html);
    $('.select2').select2();
    indexBarang++;
});

$(document).on('click', '.btn-remove-barang', function () {
    $(this).closest('.barang-item').remove();
});
</script>
@endpush
