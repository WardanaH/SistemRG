@extends('inventaris.layouts.app')

@section('title', 'Pengiriman Barang')

@section('content')

<div class="container-fluid py-4">
    <h4 class="mb-3">Permintaan Pengiriman Barang</h4>

    {{-- =====================
    SWEETALERT
    ===================== --}}
@if(session('success'))
@php
    $successMsg = session('success');
    if (is_array($successMsg)) {
        $successMsg = implode(', ', $successMsg);
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json($successMsg),
        timer: 2000,
        showConfirmButton: false
    });
});
</script>
@endif

@if(session('error'))
@php
    $errorMsg = session('error');
    if (is_array($errorMsg)) {
        $errorMsg = implode(', ', $errorMsg);
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: @json($errorMsg)
    });
});
</script>
@endif


    {{-- =====================
    INFO
    ===================== --}}
    {{-- <div class="alert alert-info text-white">
        <i class="material-icons text-sm">info</i>
        Proses pengiriman dilakukan berdasarkan <b>permintaan barang dari cabang</b>.
    </div> --}}

    {{-- ======================================================
    TABEL ATAS : PERMINTAAN CABANG
    ====================================================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Daftar Permintaan Pengiriman Cabang</h6>
                    </div>
                </div>

        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Permintaan</th>
                            <th>Cabang</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($permintaan as $i => $p)
                        <tr>
                            <td class="text-center">{{ $permintaan->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-info d-flex align-items-center justify-content-center">
                                        <i class="material-icons text-white text-sm">qr_code</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $p->kode_permintaan }}</h6>
                                        <p class="text-xs text-secondary mb-0">Kode Permintaan</p>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $p->cabang->nama }}</td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                        <i class="material-icons text-white text-sm">event</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">
                                            {{ \Carbon\Carbon::parse($p->tanggal_permintaan)->format('d M Y') }}
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">Tanggal Permintaan</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($p->status == 'Menunggu')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($p->status == 'Diproses')
                                    <span class="badge bg-primary">Diproses</span>
                                @elseif($p->status == 'Selesai')
                                    Selesai
                                @endif
                            </td>

                            <td class="text-center">
                                @if($p->status === 'Menunggu')
                                    <button class="btn btn-sm btn-primary btn-proses"
                                        data-id="{{ $p->id }}"
                                        data-kode="{{ $p->kode_permintaan }}"
                                        data-cabang="{{ $p->cabang->nama }}">
                                        Proses
                                    </button>
                                @elseif($p->status === 'Diproses')
                                    <span class="text-muted">Sedang Diproses</span>
                                @elseif($p->status === 'Selesai')
                                    <span class="text-muted">Selesai</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada permintaan cabang
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <div>
                        Menampilkan {{ $permintaan->firstItem() }} - {{ $permintaan->lastItem() }}
                        dari {{ $permintaan->total() }} data
                    </div>
                    <div>
                        {{ $permintaan->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
    TABEL BAWAH : DATA PENGIRIMAN
    ====================================================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Data Pengiriman Barang</h6>
                    </div>
                </div>

        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">

                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode</th>
                            <th>Cabang Tujuan</th>
                            <th>Barang</th>
                            <th>Tanggal Kirim</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                            <th>Kelengkapan</th>
                            <th class="text-center">Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($pengiriman as $index => $item)
                    @php
                    $detail = $item->permintaan && $item->permintaan->detail_barang
                        ? (is_array($item->permintaan->detail_barang)
                            ? $item->permintaan->detail_barang
                            : json_decode($item->permintaan->detail_barang, true))
                        : [];
                    @endphp
                        <tr>
                            <td class="text-center">{{ $pengiriman->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-primary d-flex align-items-center justify-content-center">
                                        <i class="material-icons text-white text-sm">local_shipping</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $item->kode_pengiriman }}</h6>
                                        <p class="text-xs text-secondary mb-0">Kode Pengiriman</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                        <i class="material-icons text-white text-sm">storefront</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">{{ $item->cabang->nama ?? '-' }}</h6>
                                        <p class="text-xs text-secondary mb-0">Tujuan</p>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ collect($detail)->pluck('nama_barang')->take(2)->implode(', ') }}
                                @if(count($detail) > 2)
                                    <span class="text-muted">, ...</span>
                                @endif
                            </td>

                            <td>
                                {{ $item->tanggal_pengiriman
                                    ? $item->tanggal_pengiriman->format('d M Y')
                                    : '-' }}
                            </td>

                            <td>
                            @if($item->status_pengiriman === 'Dikemas')
                                <form method="POST"
                                    action="{{ route('pengiriman.pusat.status', $item->id) }}"
                                    class="form-status">
                                    @csrf
                                    @method('PUT')

                                    <select name="status_pengiriman"
                                            class="form-select form-select-sm select-status"
                                            data-kode="{{ $item->kode_pengiriman }}">
                                        <option value="Dikemas" selected>Dikemas</option>
                                        <option value="Dikirim">Dikirim</option>
                                    </select>
                                </form>

                            @elseif($item->status_pengiriman === 'Dikirim')
                                <span class="badge bg-primary">Dikirim</span>

                            @elseif($item->status_pengiriman === 'Diterima')
                                <span class="badge bg-success">Diterima</span>
                            @endif
                            </td>

                            <td class="text-center">
                            @if($item->status_pengiriman === 'Dikemas')
                            <a href="#"
                            class="btn btn-sm btn-warning btn-edit"
                            data-id="{{ $item->id }}">
                                <i class="material-icons">edit</i>
                            </a>

                                <form action="{{ route('pengiriman.pusat.destroy', $item->id) }}"
                                    method="POST"
                                    class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">Terkunci</span>
                            @endif
                            </td>

                            <td>
                            @if($item->status_pengiriman === 'Diterima')
                                <span class="badge
                                    {{ $item->status_kelengkapan === 'Lengkap'
                                        ? 'bg-success'
                                        : 'bg-warning' }}">
                                    {{ $item->status_kelengkapan }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            </td>

                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-link text-primary btn-detail"
                                    data-detail='@json($detail)'
                                    data-kode="{{ $item->kode_pengiriman }}"
                                    data-cabang="{{ $item->cabang->nama }}"
                                    data-foto="{{ $item->status_pengiriman === 'Diterima' ? $item->foto_penerimaan : '' }}"
                                    data-catatan-permintaan='@json(optional($item->permintaan)->catatan)'
                                    data-catatan-gudang="{{ $item->catatan_gudang ?? '' }}"
                                    data-catatan-terima='@json($item->keterangan_terima)'>
                                    <i class="material-icons-round">receipt_long</i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data pengiriman
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <div>
                        Menampilkan {{ $pengiriman->firstItem() }} - {{ $pengiriman->lastItem() }}
                        dari {{ $pengiriman->total() }} data
                    </div>
                    <div>
                        {{ $pengiriman->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- =====================
MODAL DETAIL
===================== --}}
<div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Pengiriman</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="notaContent"></div>
            </div>

        </div>
    </div>
</div>
{{-- =====================
MODAL PROSES PERMINTAAN
===================== --}}
<div class="modal fade" id="modalProses">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" action="{{ route('permintaan.pusat.proses') }}">
        @csrf

        <input type="hidden" name="permintaan_id" id="permintaan_id">

        <div class="modal-header">
          <h5 class="modal-title">Proses Permintaan Pengiriman</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <p><b>Kode:</b> <span id="kode_permintaan"></span></p>
          <p><b>Cabang:</b> <span id="nama_cabang"></span></p>

          <table class="table table-bordered mt-3">
            <thead class="table-light">
              <tr>
                {{-- <th width="50">✔</th> --}}
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Stok</th>
              </tr>
            </thead>
            <tbody id="listBarang">
              {{-- diisi JS --}}
            </tbody>
          </table>

          <div class="mt-3">
            <label>Catatan Gudang</label>
            <textarea name="catatan"
              class="form-control"
              placeholder="Tulis catatan disini (opsional)"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Simpan & Kemas</button>
        </div>

      </form>

    </div>
  </div>
</div>
{{-- =====================
MODAL EDIT PENGIRIMAN (PERBAIKAN)
===================== --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengiriman</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p><b>Kode Pengiriman:</b> <span id="edit_kode"></span></p>
                    <p><b>Cabang Tujuan:</b> <span id="edit_cabang"></span></p>

                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                {{-- <th width="50">✔</th> --}}
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody id="editListBarang">
                            <tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <label>Catatan Gudang</label>
                        <textarea name="catatan" class="form-control" placeholder="Catatan jika ada barang tidak dikirim"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('click', '.btn-detail', function () {

    let detail = $(this).data('detail') || [];
    let kode   = $(this).data('kode');
    let cabang = $(this).data('cabang');
    let foto   = $(this).data('foto');

    let catPermintaan = $(this).data('catatan-permintaan');
    let catGudang     = $(this).data('catatan-gudang');
    let catTerima     = $(this).data('catatan-terima');

    if (Array.isArray(catPermintaan)) catPermintaan = catPermintaan.join(', ');
    if (Array.isArray(catGudang))     catGudang     = catGudang.join(', ');
    if (Array.isArray(catTerima))     catTerima     = catTerima.join(', ');

    // =============================
    // HEADER INFO (CARD STYLE)
    // =============================
    let html = `
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-xs text-muted">Kode Pengiriman</div>
                    <div class="fw-bold">${kode}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-xs text-muted">Cabang Tujuan</div>
                    <div class="fw-bold">${cabang}</div>
                </div>
            </div>
        </div>
    </div>
    `;



    // =============================
    // FOTO LAMPIRAN
    // =============================
    if (foto) {
        html += `
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="fw-bold mb-2">Foto Penerimaan</div>
                <img src="/storage/${foto}"
                     class="img-fluid rounded border">
            </div>
        </div>`;
    }

    // =============================
    // TABEL DETAIL BARANG
    // =============================
    html += `
    <div class="card shadow-sm">
        <div class="card-header bg-info fw-bold">
            Detail Barang Dikirim
        </div>

        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
    `;

    detail.forEach((d, i) => {
        html += `
            <tr>
                <td>${i + 1}</td>
                <td class="fw-semibold">${d.nama_barang}</td>
                <td>${d.jumlah}</td>
                <td>${d.satuan}</td>
                <td class="text-muted">${d.keterangan ?? '-'}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    </div>
    `;
        // =============================
    // CATATAN
    // =============================
    if (catPermintaan) {
        html += `
        <div class="alert border-0 shadow-sm">
            <b>Catatan Permintaan</b><br>
            ${catPermintaan}
        </div>`;
    }

    if (catGudang) {
        html += `
        <div class="alert border-0 shadow-sm">
            <b>Catatan Pengiriman Gudang</b><br>
            ${catGudang}
        </div>`;
    }

    if (catTerima) {
        html += `
        <div class="alert border-0 shadow-sm">
            <b>Catatan Penerimaan Cabang</b><br>
            ${catTerima}
        </div>`;
    }

    $('#notaContent').html(html);
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
});
</script>

<script>
$(document).on('click', '.btn-proses', function () {

    let id     = $(this).data('id');
    let kode   = $(this).data('kode');
    let cabang = $(this).data('cabang');

    $('#permintaan_id').val(id);
    $('#kode_permintaan').text(kode);
    $('#nama_cabang').text(cabang);

    $('#listBarang').html('<tr><td colspan="4" class="text-center text-muted">Memuat data...</td></tr>');

    $.ajax({
        url: "{{ route('permintaan.pusat.detail', ':id') }}".replace(':id', id),
        method: "GET",
        success: function (res) {

            let rows = '';

            res.forEach((item, index) => {

                let rowClass = item.stok <= 0 ? 'text-muted' : '';
                let statusStok = item.stok <= 0
                    ? `<span class="badge bg-danger">Stok Habis</span>`
                    : `<span class="badge bg-success">Tersedia</span>`;

                rows += `
                <tr class="${rowClass}">
                    <td class="fw-semibold">${item.nama_barang}</td>
                    <td>${item.jumlah}</td>
                    <td>${item.satuan}</td>
                    <td>
                        ${item.stok}
                        <div class="mt-1">${statusStok}</div>
                    </td>

                    <input type="hidden" name="barang[${index}][gudang_barang_id]" value="${item.gudang_barang_id}">
                    <input type="hidden" name="barang[${index}][jumlah]" value="${item.jumlah}">
                    <input type="hidden" name="barang[${index}][checked]" value="1">
                </tr>
                `;
            });

            $('#listBarang').html(rows);

            new bootstrap.Modal(
                document.getElementById('modalProses')
            ).show();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Gagal mengambil detail permintaan');
        }
    });
});

</script>

<script>
$(document).ready(function () {

    $('.select-status').select2({
        minimumResultsForSearch: Infinity,
        width: '100%'
    });

    $('.select-status').on('change', function () {
        let select = $(this);
        let form = select.closest('form');
        let status = select.val();
        let kode = select.data('kode');

        if (status === 'Dikirim') {
            Swal.fire({
                title: 'Kirim Barang?',
                text: 'Status akan diubah menjadi DIKIRIM dan tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    select.val('Dikemas').trigger('change.select2');
                }
            });
        }
    });

    $('.form-delete').on('submit', function (e) {
        e.preventDefault();
        let form = this;

        Swal.fire({
            title: 'Hapus Pengiriman?',
            text: 'Data akan dihapus dan stok tidak dikurangi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

});
</script>
<script>
$(document).on('click', '.btn-edit', function () {

    let id = $(this).data('id');

    $('#editListBarang').html('<tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>');
    $('#formEdit').attr('action', "{{ url('gudang-pusat/pengiriman') }}/" + id + "/update");

    $.get("{{ url('gudang-pusat/pengiriman') }}/" + id + "/edit-data", function(res) {

        $('#edit_kode').text(res.kode);
        $('#edit_cabang').text(res.cabang || '-');

        let html = '';
        res.detail.forEach((item, i) => {
            html += `
                <tr>

                    <td>${item.nama_barang}</td>
                    <td>${item.jumlah}</td>
                    <td>${item.satuan}</td>
                    <td>${item.stok}</td>
                </tr>
            `;
        });

        $('#editListBarang').html(html);

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    });
});

// Konfirmasi submit edit
$('#formEdit').on('submit', function (e) {
    e.preventDefault();
    let form = this;

    Swal.fire({
        title: 'Simpan Perubahan?',
        text: 'Jumlah dan status barang akan diperbarui.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Simpan'
    }).then((res) => {
        if (res.isConfirmed) {
            form.submit();
        }
    });
});
</script>

@endpush
