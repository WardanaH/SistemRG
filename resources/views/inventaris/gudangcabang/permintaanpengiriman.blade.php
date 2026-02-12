@extends('inventaris.layouts.app')

@section('title', 'Permintaan Pengiriman Barang')
<style>

/* INPUT lebih modern */
.form-control{
    border-radius:10px !important;
    border:1.5px solid #e5e7eb !important;
    transition:.2s;
}

.form-control:focus{
    border-color:#ff4d88 !important;
    box-shadow:0 0 0 3px rgba(255,77,136,.15) !important;
}

/* Card barang biar ga flat */
.barang-item{
    background:#fff;
    padding:18px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,.05);
    transition:.25s;
}

.barang-item:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 24px rgba(0,0,0,.08);
}

/* tombol tambah barang */
#btnTambahBarang{
    border-radius:10px;
    font-weight:600;
}

/* tombol hapus */
.btn-remove-barang{
    border-radius:10px;
}

/* textarea */
textarea{
    border-radius:12px !important;
}

.table-modern{
    border-collapse:separate;
    border-spacing:0 10px;
}

.table-modern tbody tr{
    background:#fff;
    box-shadow:0 4px 14px rgba(0,0,0,.05);
    transition:.25s;
}

.table-modern tbody tr:hover{
    transform:translateY(-4px);
}

</style>

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
                                    value="{{ date('Y-m-d') }}"
                                    readonly>
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
                        <table class="table table-modern align-items-center">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($datas as $i => $row)
                            <tr>
                                {{-- NO --}}
                                <td class="text-center">{{ $datas->firstItem() + $i }}</td>
                                {{-- KODE PERMINTAAN --}}
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="avatar avatar-sm me-3 border-radius-md
                                                    bg-gradient-info d-flex align-items-center justify-content-center">
                                            <i class="material-icons text-white text-sm">assignment</i>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $row->kode_permintaan }}</h6>
                                            {{-- <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($row->tanggal_permintaan)->format('d M Y') }}
                                            </p> --}}
                                        </div>
                                    </div>
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 bg-gradient-primary border-radius-md d-flex align-items-center justify-content-center">
                                            <i class="material-icons text-white text-sm">calendar_today</i>
                                        </div>

                                        <span class="text-sm">
                                            {{ \Carbon\Carbon::parse($row->tanggal_permintaan)->format('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="text-center">
                                    @if ($row->status === 'Menunggu')
                                        <span class="badge bg-warning">{{ $row->status }}</span>

                                    @elseif ($row->status === 'Diproses')
                                        <span class="badge bg-primary">{{ $row->status }}</span>

                                    @elseif ($row->status === 'Selesai')
                                        <span class="badge bg-success">{{ $row->status }}</span>

                                    @else
                                        <span class="text-muted">{{ $row->status }}</span>
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
                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div class="text-sm text-muted">
                                Menampilkan {{ $datas->firstItem() }} - {{ $datas->lastItem() }}
                                dari {{ $datas->total() }} data
                            </div>
                            <div>
                                {{ $datas->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
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
