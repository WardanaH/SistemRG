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
                        <h6 class="text-white ps-3">
                            Data Barang Gudang Cabang ({{ $cabang->nama }})
                        </h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($datas as $i => $item)
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
                                        } else {
                                            $status = 'Aman';
                                            $badge = 'bg-gradient-success';
                                        }

                                        $stokFormat = fmod($item->stok_cabang, 1) == 0
                                            ? number_format($item->stok_cabang, 0, ',', '.')
                                            : number_format($item->stok_cabang, 2, ',', '.');
                                    @endphp

                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td class="fw-bold">{{ $item->nama_bahan }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>{{ $stokFormat }}</td>

                                        <td class="text-center">
                                            <span class="badge {{ $badge }}">{{ $status }}</span>
                                        </td>

                                        <td class="text-center">
                                            <button class="btn btn-link text-info px-2"
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
                                        <td colspan="6" class="text-center text-muted py-4">
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

{{-- =====================
MODAL EDIT STOK
===================== --}}
<div class="modal fade" id="modalEditStok" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formEditStok">
        @csrf @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Update Stok Cabang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <label>Nama Barang</label>
          <input type="text" id="edit_nama" class="form-control mb-3" readonly>

          <label>Stok</label>
          <input type="number" step="0.01" name="stok" id="edit_stok" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn bg-gradient-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function editStok(id, nama, stok) {
    edit_nama.value = nama;
    edit_stok.value = stok;
    formEditStok.action = '/gudang-cabang/barang/update/' + id;
    new bootstrap.Modal(modalEditStok).show();
}
</script>

@endsection
