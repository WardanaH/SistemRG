@extends('inventaris.layouts.app')

@section('title', 'Barang Gudang Pusat')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIFIKASI
    ===================== --}}
    @if(session('tambah'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('tambah') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @if(session('edit'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('edit') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    @if(session('hapus'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Dihapus',
                    text: '{{ session('hapus') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    {{-- =====================
    FORM TAMBAH BARANG
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-succes   s border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Tambah Barang Gudang Pusat</h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('barang.pusat.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Nama Bahan</label>
                                    <input type="text" name="nama_bahan" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="harga" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline">
                                    <select name="satuan" class="form-control select2" style="appearance:auto;padding-left:10px;">
                                        <option disabled selected>Pilih Satuan</option>
                                        <option value="PCS">PCS</option>
                                        <option value="PAKET">PAKET</option>
                                        <option value="KG">KG</option>
                                        <option value="METER">METER</option>
                                        <option value="CENTIMETER">CENTIMETER</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stok" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Batas Stok</label>
                                    <input type="number" name="batas_stok" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button class="btn bg-gradient-success mb-0">
                                <i class="material-icons text-sm">add</i>&nbsp;Simpan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================
    TABEL DATA BARANG
    ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data Barang Gudang Pusat</h6>
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
                                    <th>Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $index => $item)
                                    @php
                                        $persen = $item->batas_stok > 0
                                            ? ($item->stok / $item->batas_stok) * 100
                                            : 0;

                                        if ($item->stok == 0) {
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
                                        <td>{{ $item->stok }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-sm {{ $badge }}">{{ $status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-link text-info px-2"
                                                onclick="editBarang(
                                                    {{ $item->id }},
                                                    '{{ $item->nama_bahan }}',
                                                    {{ $item->harga }},
                                                    '{{ $item->satuan }}',
                                                    {{ $item->stok }},
                                                    {{ $item->batas_stok }}
                                                )">
                                                <i class="material-icons-round">edit</i>
                                            </button>

                                            <button type="button"
                                                class="btn btn-link text-danger px-2"
                                                onclick="hapusData({{ $item->id }})">
                                                <i class="material-icons-round">delete</i>
                                            </button>

                                            <form id="hapus-{{ $item->id }}"
                                                  action="{{ route('barang.pusat.destroy',$item->id) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
<!-- MODAL EDIT BARANG -->
<div class="modal fade" id="modalEditBarang" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formEditBarang">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Edit Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Nama Bahan</label>
              <input type="text" name="nama_bahan" id="edit_nama" class="form-control" required>
            </div>

            <div class="col-md-3 mb-3">
              <label>Harga</label>
              <input type="number" name="harga" id="edit_harga" class="form-control" required>
            </div>

            <div class="col-md-3 mb-3">
              <label>Satuan</label>
              <select name="satuan" id="edit_satuan" class="form-control">
                <option value="PCS">PCS</option>
                <option value="PAKET">PAKET</option>
                <option value="KG">KG</option>
                <option value="METER">METER</option>
                <option value="CENTIMETER">CENTIMETER</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label>Stok</label>
              <input type="number" name="stok" id="edit_stok" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label>Batas Stok</label>
              <input type="number" name="batas_stok" id="edit_batas" class="form-control" required>
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
function hapusData(id) {
    Swal.fire({
        title: 'Yakin hapus?',
        text: 'Data tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('hapus-'+id).submit();
        }
    });
}
</script>
<script>
function editBarang(id, nama, harga, satuan, stok, batas) {
    document.getElementById('edit_nama').value  = nama;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('edit_satuan').value = satuan;
    document.getElementById('edit_stok').value  = stok;
    document.getElementById('edit_batas').value = batas;

    document.getElementById('formEditBarang')
        .action = '/gudang-pusat/barang/update/' + id;

    new bootstrap.Modal(document.getElementById('modalEditBarang')).show();
}
</script>
<script>
$(document).ready(function () {
    $('.select2').select2({
        placeholder: "Pilih data",
        allowClear: true,
        width: '100%'
    });
});
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
