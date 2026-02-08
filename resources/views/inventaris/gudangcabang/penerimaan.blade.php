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
                text: '{{ is_array(session('success')) ? implode(", ", session('success')) : session('success') }}',
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
                text: '{{ is_array(session('error')) ? implode(", ", session('error')) : session('error') }}'
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
@php
    $detail = is_array($r->keterangan)
        ? $r->keterangan
        : json_decode($r->keterangan, true) ?? [];
@endphp

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
                                            {{ collect($detail)->pluck('nama_barang')->implode(', ') }}
                                        </h6>
                                    </div>
                                </div>
                            </td>

                            {{-- JUMLAH --}}
                            <td>
                                {{ collect($detail)->pluck('jumlah')->implode(', ') }}
                            </td>

                            {{-- SATUAN --}}
                            <td>
                                {{ collect($detail)->pluck('satuan')->implode(', ') }}
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
                            @if($r->status_pengiriman === 'Diterima')
                                <span class="badge {{ $r->status_kelengkapan === 'Lengkap' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $r->status_kelengkapan }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            </td>

                            {{-- DETAIL --}}
                            <td class="text-center">
                                <button type="button"
                                    class="btn btn-link text-primary btn-detail"
                                    data-detail='@json($detail)'
                                    data-kode="{{ $r->kode_pengiriman }}"
                                    data-cabang="{{ $cabang->nama }}"
                                    data-tanggal="{{ $r->created_at }}"
                                    data-foto="{{ $r->foto_penerimaan }}"
                                    data-catatan-permintaan='@json(optional($r->permintaan)->catatan)'
                                    data-catatan-gudang="{{ $r->catatan_gudang ?? '' }}"
                                    data-catatan-terima='@json($r->keterangan_terima)'>
                                    <i class="material-icons-round">receipt_long</i>
                                </button>
                            </td>

                            {{-- AKSI --}}
                            <td class="text-center">
                                @if($r->status_pengiriman == 'Dikirim')
                                    <button
                                        class="btn btn-success btn-sm btn-terima"
                                        data-id="{{ $r->id }}"
                                        data-detail='@json($detail)'>
                                        Terima
                                    </button>
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
<!-- modal detail -->
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
<!-- modal terima -->
<div class="modal fade" id="modalTerima">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" id="formTerima"
            enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Penerimaan Barang</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>✔</th>
                <th>Barang</th>
                <th>Dikirim</th>
                <th>Diterima</th>
              </tr>
            </thead>
            <tbody id="bodyTerima"></tbody>
          </table>

          <div class="mb-2">
            <label>Foto Penerimaan</label>
            <input type="file" name="foto" class="form-control">
          </div>

          <div>
            <label>Keterangan</label>
            <textarea name="keterangan_terima" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-success">Simpan Penerimaan</button>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // KONFIRMASI TERIMA BARANG
    $(document).on('click', '.btn-terima', function () {

        let id = $(this).data('id');
        let detail = $(this).data('detail');

        let html = '';

        detail.forEach((d, i) => {
            html += `
            <tr>
            <td>
                <input type="checkbox"
                    name="barang[${i}][checked]"
                    checked>
                <input type="hidden"
                    name="barang[${i}][gudang_barang_id]"
                    value="${d.gudang_barang_id}">
            </td>
            <td>${d.nama_barang}</td>
            <td>${d.jumlah}</td>
            <td>
                <input type="number"
                    name="barang[${i}][jumlah]"
                    value="${d.jumlah}"
                    step="0.01"
                    class="form-control">
            </td>
            </tr>
            `;
        });

        $('#bodyTerima').html(html);
        $('#formTerima').attr(
            'action',
            `/gudang-cabang/penerimaan/terima/${id}`
        );

        new bootstrap.Modal(
            document.getElementById('modalTerima')
        ).show();
    });
</script>
<script>
$(document).on('click', '.btn-detail', function () {

    let detail = $(this).data('detail');
    let kode   = $(this).data('kode');
    let cabang = $(this).data('cabang');
    let tanggal = $(this).data('tanggal');
    let foto   = $(this).data('foto');

    let catPermintaan = $(this).data('catatan-permintaan');
    let catGudang     = $(this).data('catatan-gudang');
    let catTerima     = $(this).data('catatan-terima');

    if (Array.isArray(catPermintaan)) {
        catPermintaan = catPermintaan.join(', ');
    }

    if (Array.isArray(catGudang)) {
        catGudang = catGudang.join(', ');
    }

if (Array.isArray(catTerima) && catTerima.length > 0) {

    let list = '';

    catTerima.forEach(item => {
        if(item.keterangan){
            list += `• ${item.nama_barang} : ${item.keterangan}<br>`;
        }
    });

    if(list){
        html += `
            <div class="alert border-0 shadow-sm">
                <b>Catatan Penerimaan Cabang</b><br>
                ${list}
            </div>
        `;
    }
}


    let html = `

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="text-xs text-muted">Kode Pengiriman</div>
                    <div class="fw-bold">${kode}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-xs text-muted">Cabang</div>
                    <div class="fw-bold">${cabang}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-xs text-muted">Tanggal</div>
                    <div class="fw-bold">${tanggal}</div>
                </div>
            </div>

        </div>
    </div>
    `;


    if (foto) {
        html += `
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="fw-bold mb-2">Foto Penerimaan</div>
                    <img src="/storage/${foto}"
                        class="img-fluid rounded border">
                </div>
            </div>
        `;
    }

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
                <td>${i + 1}</td>
                <td class="fw-semibold">${d.nama_barang}</td>
                <td>${d.jumlah}</td>
                <td>${d.satuan}</td>
                <td class="text-muted">${d.keterangan ? d.keterangan : '-'}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    </div>
    `;

        if (catPermintaan) {
        html += `
            <div class="alert border-0 shadow-sm">
                <b>Catatan Permintaan</b><br>
                ${catPermintaan}
            </div>
        `;
    }

    if (catGudang) {
        html += `
            <div class="alert border-0 shadow-sm">
                <b>Catatan Pengiriman Gudang</b><br>
                ${catGudang}
            </div>
        `;
    }

if (Array.isArray(catTerima)) {

    let list = '';

    catTerima.forEach(item => {
        if(item.keterangan && item.keterangan.trim() !== ''){
            list += `• ${item.nama_barang} : ${item.keterangan}<br>`;
        }
    });

    // ✅ hanya tampil kalau ADA isi catatan
    if(list !== ''){
        html += `
            <div class="alert border-0 shadow-sm">
                <b>Catatan Penerimaan Cabang</b><br>
                ${list}
            </div>
        `;
    }
}


    $('#notaContent').html(html);
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
});

</script>
@endpush
