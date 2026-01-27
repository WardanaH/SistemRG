@extends('inventaris.layouts.app')

@section('title', 'Pengiriman Barang')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIFIKASI
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

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}'
            });
        });
    </script>
    @endif

    {{-- =====================
    FORM TAMBAH PENGIRIMAN (ATAS)
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Tambah Pengiriman Barang
                        </h6>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('pengiriman.pusat.store') }}" method="POST">
                        @csrf

                        {{-- =====================
                        CABANG & TANGGAL (1x PER PENGIRIMAN)
                        ===================== --}}
                        <div class="row mb-4">

                            <div class="col-md-6">
                                <label class="form-label">Kirim ke Cabang</label>
                                <select name="cabang_tujuan_id"
                                        class="form-control select2"
                                        required>
                                    <option value="">Pilih Cabang</option>
                                    @foreach($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Kirim</label>
                                <input type="date"
                                    name="tanggal_pengiriman"
                                    class="form-control border border-2 border-grey"
                                    required>
                            </div>

                        </div>

                        {{-- =====================
                        DAFTAR BARANG (BISA BANYAK)
                        ===================== --}}
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
                                                {{ $barang->nama_bahan }} (Stok: {{ $barang->stok }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number"
                                        name="barang[0][jumlah]"
                                        class="form-control border border-2 border-grey"
                                        min="1"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Keterangan (opsional)</label>
                                    <input type="text"
                                        name="barang[0][keterangan]"
                                        class="form-control border border-2 border-grey"
                                        >
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

                        {{-- =====================
                        SUBMIT
                        ===================== --}}
                        <div class="text-end mt-3">
                            <button class="btn bg-gradient-success mb-0">
                                <i class="material-icons text-sm">local_shipping</i>
                                &nbsp;Simpan Pengiriman
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- =====================
    TABEL DATA PENGIRIMAN
    ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Data Pengiriman Barang
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Cabang Tujuan</th>
                                    <th>Tanggal Kirim</th>
                                    <th class="text-center">Status</th>
                                    <th>Tanggal Diterima</th>
                                    <th class="text-center">Detail</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            @forelse($pengiriman as $index => $item)
                            @php
                                $detail = $item->keterangan ?? null;
                            @endphp

                                <tr>
                                    <td>{{ ($pengiriman->currentPage()-1) * $pengiriman->perPage() + $index + 1 }}</td>
                                    <!--barang-->
                                    <td class="fw-bold">
                                    @if($detail)
                                        {{ collect($detail)->pluck('nama_barang')->take(2)->implode(', ') }}
                                        @if(count($detail) > 2), ... @endif
                                    @else
                                        {{ $item->barang->nama_bahan ?? '-' }}
                                    @endif
                                    </td>
                                    <!-- jumlah -->
                                    <td>
                                    @if($detail)
                                        {{ collect($detail)->pluck('jumlah')->take(2)->implode(', ') }}
                                        @if(count($detail) > 2), ... @endif
                                    @else
                                        {{ $item->jumlah ?? '-' }}
                                    @endif
                                    </td>
                                    <!-- satuan-->
                                    <td>
                                    @if($detail)
                                        {{ collect($detail)->pluck('satuan')->take(2)->implode(', ') }}
                                        @if(count($detail) > 2), ... @endif
                                    @else
                                        {{ $item->barang->satuan ?? '-' }}
                                    @endif
                                    </td>

                                    <td>{{ $item->cabangTujuan->nama ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d M Y') }}</td>

                                    <td class="text-center">

                                        @if($item->status_pengiriman === 'Diterima')
                                            <span class="badge bg-gradient-success">Diterima</span>

                                        @elseif($item->status_pengiriman === 'Dikirim')
                                            <span class="badge bg-gradient-info">Dikirim</span>

                                        @else
                                            {{-- HANYA SAAT DIKEMAS --}}
                                            <form action="{{ route('pengiriman.pusat.status', $item->id) }}"
                                                method="POST"
                                                class="form-update-status d-inline">
                                                @csrf
                                                @method('PUT')

                                                <select name="status_pengiriman"
                                                        class="form-control select2-status"
                                                        data-current="Dikemas"
                                                        style="width:120px">
                                                    <option value="Dikemas" selected>Dikemas</option>
                                                    <option value="Dikirim">Dikirim</option>
                                                </select>
                                            </form>
                                        @endif

                                    </td>


                                    <td>
                                        {{ $item->tanggal_diterima
                                            ? \Carbon\Carbon::parse($item->tanggal_diterima)->format('d M Y')
                                            : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-link text-primary btn-detail"
                                            data-detail='@json($detail)'
                                            data-kode="{{ $item->kode_pengiriman }}"
                                            data-cabang="{{ $item->cabangTujuan->nama }}"
                                            data-tanggal="{{ $item->tanggal_pengiriman }}">
                                            <i class="material-icons-round">receipt_long</i>
                                        </button>
                                    </td>


                                    <td class="text-center">
                                        <form action="{{ route('pengiriman.pusat.destroy', $item->id) }}"
                                              method="POST"
                                              class="form-hapus d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-link text-danger px-2 btn-hapus">
                                                <i class="material-icons-round">delete</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Belum ada data pengiriman
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                        </table>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $pengiriman->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-hapus').forEach(function(btn) {
    btn.addEventListener('click', function () {

        let form = btn.closest('form');

        Swal.fire({
            title: 'Yakin hapus?',
            text: 'Data pengiriman akan dihapus permanen',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
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
$(document).ready(function () {

    // SELECT2 STATUS
    $('.select2-status').select2({
        minimumResultsForSearch: Infinity,
        width: 'resolve'
    });

    // SWEETALERT KONFIRMASI UPDATE STATUS
    $('.select2-status').on('change', function () {

        let select = $(this);
        let form   = select.closest('form');
        let lama   = select.data('current');
        let baru   = select.val();

        Swal.fire({
            title: 'Ubah Status?',
            text: `Dari "${lama}" ke "${baru}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            } else {
                // rollback ke status lama
                select.val(lama).trigger('change.select2');
            }
        });
    });

});
</script>
<script>
let indexBarang = 1;

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
                        {{ $barang->nama_bahan }} (Stok: {{ $barang->stok }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="number"
                name="barang[${indexBarang}][jumlah]"
                class="form-control border border-2 border-grey"
                min="1"
                required>
        </div>

        <div class="col-md-4">
            <input type="text"
                name="barang[${indexBarang}][keterangan]"
                class="form-control border border-2 border-grey"
                placeholder="Keterangan (opsional)">
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

// hapus baris
$(document).on('click', '.btn-remove-barang', function () {
    $(this).closest('.barang-item').remove();
});
</script>
<script>
$(document).on('click', '.btn-detail', function () {

    let detail = $(this).data('detail');

    if (!detail || detail.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Tidak ada detail',
            text: 'Pengiriman ini dibuat sebelum fitur multi-barang'
        });
        return;
    }

    let kode = $(this).data('kode');
    let cabang = $(this).data('cabang');
    let tanggal = $(this).data('tanggal');

    let html = `
        <p><b>Kode:</b> ${kode}</p>
        <p><b>Cabang:</b> ${cabang}</p>
        <p><b>Tanggal:</b> ${tanggal}</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
    `;

    detail.forEach((d, i) => {
        html += `
            <tr>
                <td>${i+1}</td>
                <td>${d.nama_barang}</td>
                <td>${d.jumlah}</td>
                <td>${d.satuan}</td>
                <td>${d.keterangan ?? '-'}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;

    $('#notaContent').html(html);

    let modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
});
</script>
@endpush
