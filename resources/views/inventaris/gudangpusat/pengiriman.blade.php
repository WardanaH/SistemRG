@extends('inventaris.layouts.app')

@section('title', 'Pengiriman Barang')

@section('content')

<div class="container-fluid py-4">
    <h4 class="mb-3">Permintaan Pengiriman Barang</h4>

    {{-- =====================
    SWEETALERT
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
    INFO
    ===================== --}}
    {{-- <div class="alert alert-info text-white">
        <i class="material-icons text-sm">info</i>
        Proses pengiriman dilakukan berdasarkan <b>permintaan barang dari cabang</b>.
    </div> --}}

    {{-- ======================================================
    TABEL ATAS : PERMINTAAN CABANG
    ====================================================== --}}
    <div class="card mb-4">
        <div class="card-header bg-gradient-warning text-white">
            <h6 class="mb-0">Daftar Permintaan Pengiriman Cabang</h6>
        </div>

        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
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
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-bold">{{ $p->kode_permintaan }}</td>
                            <td>{{ $p->cabang->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_permintaan)->format('d M Y') }}</td>
                            <td>
                                <span class="badge
                                    {{ $p->status == 'Menunggu' ? 'bg-warning' : 'bg-success' }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($p->status === 'Menunggu')
                                <button class="btn btn-sm btn-primary btn-proses"
                                    data-id="{{ $p->id }}"
                                    data-kode="{{ $p->kode_permintaan }}"
                                    data-cabang="{{ $p->cabang->nama }}">
                                    Proses
                                </button>
                                @else
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
            </div>
        </div>
    </div>

    {{-- ======================================================
    TABEL BAWAH : DATA PENGIRIMAN
    ====================================================== --}}
    <div class="card my-4">
        <div class="card-header bg-gradient-primary text-white">
            <h6 class="mb-0">Data Pengiriman Barang</h6>
        </div>

        <div class="card-body px-0 pb-2">
            <div class="table-responsive p-0">

                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
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
                            $detail = $item->keterangan ?? [];
                        @endphp
                        <tr>
                            <td>{{ ($pengiriman->currentPage()-1) * $pengiriman->perPage() + $index + 1 }}</td>

                            <td class="fw-bold">{{ $item->kode_pengiriman }}</td>

                            <td>{{ $item->cabang->nama ?? '-' }}</td>

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
                            @else
                                <span class="badge bg-success">Dikirim</span>
                            @endif
                            </td>

                            <td class="text-center">
                            @if($item->status_pengiriman === 'Dikemas')
                                <a href="#"
                                class="btn btn-sm btn-warning">
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
                                <span class="badge
                                    {{ $item->status_kelengkapan == 'Lengkap'
                                        ? 'bg-success'
                                        : 'bg-warning' }}">
                                    {{ $item->status_kelengkapan ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-link text-primary btn-detail"
                                    data-detail='@json($detail)'
                                    data-kode="{{ $item->kode_pengiriman }}"
                                    data-cabang="{{ $item->cabang->nama }}">
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

                <div class="d-flex justify-content-end mt-3 px-3">
                    {{ $pengiriman->links('pagination::bootstrap-5') }}
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
                <th width="50">✔</th>
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
              placeholder="Catatan jika ada barang tidak dikirim"></textarea>
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).on('click', '.btn-detail', function () {

    let detail = $(this).data('detail');
    let kode = $(this).data('kode');
    let cabang = $(this).data('cabang');

    let html = `
        <p><b>Kode Pengiriman:</b> ${kode}</p>
        <p><b>Cabang Tujuan:</b> ${cabang}</p>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
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
            </tr>
        `;
    });

    html += `</tbody></table>`;

    $('#notaContent').html(html);
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
});
</script>
<script>
$(document).on('click', '.btn-proses', function () {

    let id = $(this).data('id');
    let kode = $(this).data('kode');
    let cabang = $(this).data('cabang');

    $('#permintaan_id').val(id);
    $('#kode_permintaan').text(kode);
    $('#nama_cabang').text(cabang);
    $('#listBarang').html('');

    $.ajax({
        url: "{{ url('gudang-pusat/permintaan') }}/" + id + "/detail",
        method: "GET",
        success: function (res) {

            res.forEach((item, index) => {
                $('#listBarang').append(`
                  <tr>
                    <td class="text-center">
                      <input type="checkbox"
                        name="barang[${index}][checked]"
                        checked>
                      <input type="hidden"
                        name="barang[${index}][gudang_barang_id]"
                        value="${item.gudang_barang_id}">
                      <input type="hidden"
                        name="barang[${index}][jumlah]"
                        value="${item.jumlah}">
                    </td>
                    <td>${item.nama_barang}</td>
                    <td>${item.jumlah}</td>
                    <td>${item.satuan}</td>
                    <td>${item.stok}</td>
                  </tr>
                `);
            });

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

@endpush
