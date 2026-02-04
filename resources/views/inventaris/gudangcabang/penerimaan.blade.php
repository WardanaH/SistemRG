@extends('inventaris.layouts.app')

@section('title', 'Penerimaan Barang')

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
    HEADER
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            📦 Riwayat Pengiriman ke {{ $cabang->nama }}
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    @if($riwayat->isEmpty())
                        <p class="text-muted text-center py-4">
                            Tidak ada data pengiriman dari Gudang Pusat.
                        </p>
                    @else

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                        <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Tanggal Kirim</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal Diterima</th>
                            <th class="text-center">Kelengkapan</th>
                            <th class="text-center">Detail</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($riwayat as $index => $r)
                        <tr>

                            {{-- NO --}}
                            <td class="text-center">{{ $index + 1 }}</td>

                            {{-- BARANG --}}
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="avatar avatar-sm me-3 border-radius-md
                                                bg-gradient-primary d-flex align-items-center justify-content-center">
                                        <i class="material-icons text-white text-sm">inventory_2</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">
                                            {{ collect(is_array($r->keterangan) ? $r->keterangan : json_decode($r->keterangan, true) ?? [])
                                                ->pluck('nama_barang')->implode(', ') }}
                                        </h6>
                                    </div>
                                </div>
                            </td>

                            {{-- JUMLAH --}}
                            <td>
                                {{ collect(is_array($r->keterangan) ? $r->keterangan : json_decode($r->keterangan, true) ?? [])
                                    ->pluck('jumlah')->implode(', ') }}
                            </td>

                            {{-- SATUAN --}}
                            <td>
                                {{ collect(is_array($r->keterangan) ? $r->keterangan : json_decode($r->keterangan, true) ?? [])
                                    ->pluck('satuan')->implode(', ') }}
                            </td>

                            {{-- TANGGAL KIRIM --}}
                            <td>
                                {{ \Carbon\Carbon::parse($r->tanggal_pengiriman)->format('d M Y') }}
                            </td>

                            {{-- STATUS --}}
                            <td class="text-center">
                                @if($r->status_pengiriman == 'Dikemas')
                                    <span class="badge bg-gradient-secondary">Dikemas</span>
                                @elseif($r->status_pengiriman == 'Dikirim')
                                    <span class="badge bg-gradient-info">Dikirim</span>
                                @else
                                    <span class="badge bg-gradient-success">Diterima</span>
                                @endif
                            </td>

                            {{-- TANGGAL DITERIMA --}}
                            <td>
                                {{ $r->tanggal_diterima
                                    ? \Carbon\Carbon::parse($r->tanggal_diterima)->format('d M Y')
                                    : '-' }}
                            </td>

                            {{-- KELENGKAPAN --}}
                            <td class="text-center">
                                @if($r->status_kelengkapan === 'Lengkap')
                                    <span class="badge bg-success">LENGKAP</span>
                                @else
                                    <span class="badge bg-warning">TIDAK LENGKAP</span>
                                @endif
                            </td>

                            {{-- DETAIL --}}
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-link text-primary btn-detail"
                                    data-detail='@json($r->keterangan)'
                                    data-kode="{{ $r->kode_pengiriman }}"
                                    data-cabang="{{ $cabang->nama }}"
                                    data-tanggal="{{ $r->tanggal_pengiriman }}">
                                    <i class="material-icons-round">receipt_long</i>
                                </button>
                            </td>

                            {{-- AKSI --}}
                            <td class="text-center">
                                @if($r->status_pengiriman == 'Dikirim')
                                    <form action="{{ route('gudangcabang.penerimaan.terima', $r->id) }}"
                                        method="POST"
                                        class="form-terima d-inline">
                                        @csrf
                                        <button type="button"
                                                class="btn btn-success btn-sm btn-terima">
                                            Terima
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                        </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div class="text-sm text-muted">
                                Menampilkan {{ $riwayat->firstItem() }} - {{ $riwayat->lastItem() }}
                                dari {{ $riwayat->total() }} data
                            </div>
                            <div>
                                {{ $riwayat->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    @endif
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
    // KONFIRMASI TERIMA BARANG
    $(document).on('click', '.btn-terima', function(e) {
        e.preventDefault();

        let form = $(this).closest('form');

        Swal.fire({
            title: 'Terima Barang?',
            text: 'Pastikan barang sudah benar-benar diterima!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terima',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
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
