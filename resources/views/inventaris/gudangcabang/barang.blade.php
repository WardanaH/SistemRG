@extends('inventaris.layouts.app')

@section('title', 'Barang Gudang Cabang')

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

    {{-- =====================
    TABEL DATA BARANG
    ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Data Barang Cabang ({{ $cabang->nama }})
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Stok Cabang</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($datas as $index => $item)
                                    @php
                                        $persen = $item->batas_stok > 0
                                            ? ($item->stok_cabang / $item->batas_stok) * 100
                                            : 0;

                                        if ($item->stok_cabang == 0) {
                                            $status = 'Habis';
                                            $badge = 'bg-gradient-danger';
                                        } elseif ($persen <= 20) {
                                            $status = 'Hampir Habis';
                                            $badge = 'bg-gradient-warning';
                                        } elseif ($persen <= 50) {
                                            $status = 'Cukup';
                                            $badge = 'bg-gradient-info';
                                        } elseif ($persen >= 80) {
                                            $status = 'Banyak';
                                            $badge = 'bg-gradient-success';
                                        } else {
                                            $status = 'Normal';
                                            $badge = 'bg-gradient-secondary';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $index+1 }}</td>
                                        <td class="font-weight-bold">{{ $item->nama_bahan }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>Rp {{ number_format($item->harga,0,',','.') }}</td>
                                        <td>{{ $item->stok_cabang }}</td>

                                        <td class="text-center">
                                            <span class="badge badge-sm {{ $badge }}">{{ $status }}</span>
                                        </td>

                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-link text-info px-2"
                                                onclick="editStok(
                                                    {{ $item->id }},
                                                    '{{ $item->nama_bahan }}',
                                                    {{ $item->stok_cabang }}
                                                )">
                                                <i class="material-icons-round">edit</i>
                                            </button>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Belum ada data barang
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

<!-- MODAL EDIT STOK CABANG -->
<div class="modal fade" id="modalEditStok" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formEditStok">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Update Stok Cabang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label>Nama Barang</label>
              <input type="text" id="edit_nama" class="form-control" readonly>
            </div>

            <div class="col-12 mb-3">
              <label>Stok Cabang</label>
              <input type="number" name="stok" id="edit_stok" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- =====================
SWEETALERT HAPUS
===================== --}}
<script>
function editStok(id, nama, stok) {
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_stok').value = stok;

    document.getElementById('formEditStok')
        .action = '/gudang-cabang/barang/update/' + id;

    new bootstrap.Modal(document.getElementById('modalEditStok')).show();
}
</script>

<style>
.table-responsive {
    overflow-x: auto;
}

.table td,
.table th,
.btn {
    position: relative;
    z-index: 20 !important;
    pointer-events: auto !important;
}
</style>

@endsection
