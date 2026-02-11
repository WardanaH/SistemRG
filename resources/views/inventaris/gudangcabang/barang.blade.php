@extends('inventaris.layouts.app')

@section('title', 'Barang Gudang Cabang')
<style>

.table-modern tbody tr{
    transition: all .2s ease;
}

.table-modern tbody tr:hover{
    background:#f8f9fa;
    transform: scale(1.003);
}

/* stok */
.stok-text{
    font-weight:700;
    font-size:15px;
}
.table-modern{
    border-collapse: separate;
    border-spacing: 0 8px;
}

.table-modern tbody tr{
    background: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,.04);
    border-radius:12px;
    transition:.2s;
}

.table-modern tbody tr:hover{
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,.06);
}

.table-modern td{
    border-top:none !important;
    padding:18px !important;
}

.table-modern tbody tr td:first-child{
    border-radius:12px 0 0 12px;
}

.table-modern tbody tr td:last-child{
    border-radius:0 12px 12px 0;
}

</style>

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
                    <div class="px-3 pt-3">
                        <form method="GET" action="{{ route('gudangcabang.barang') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cari nama barang..."
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn bg-gradient-primary mb-0">
                                        <i class="material-icons text-sm">search</i> Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-items-center">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama</th>
                                    <th>Satuan</th>
                                    <th>Stok</th>
                                    <th class="text-center">Status</th>
                                    {{-- <th class="text-center">Aksi</th> --}}
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
                                        {{-- NO --}}
                                        <td class="text-center">
                                            {{ $datas->firstItem() + $i }}
                                        </td>

                                        {{-- NAMA BARANG --}}
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="avatar avatar-sm me-3 border-radius-md
                                                            bg-gradient-primary d-flex align-items-center justify-content-center">
                                                    <i class="material-icons text-white text-sm">inventory_2</i>
                                                </div>

                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item->nama_bahan }}</h6>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- SATUAN --}}
                                        <td>
                                        @php
                                            $icons = [
                                                'ROLL' => 'refresh',
                                                'PACK' => 'inventory_2',
                                                'LEMBAR' => 'description',
                                                'METER' => 'straighten',
                                                'BOX' => 'inbox',
                                                'PCS' => 'widgets',
                                                'RIM' => 'layers',
                                                'TANK' => 'propane_tank',
                                                'BOTOL' => 'liquor',
                                                'LUSIN' => 'view_comfy'
                                            ];

                                            $icon = $icons[strtoupper(trim($item->satuan))] ?? 'inventory_2';
                                        @endphp

                                        <div class="d-flex align-items-center gap-2">
                                            <i class="material-icons text-secondary" style="font-size:18px">
                                                {{ $icon }}
                                            </i>

                                            <span class="fw-semibold">
                                                {{ ucfirst(strtolower($item->satuan)) }}
                                            </span>
                                        </div>
                                        </td>

                                        {{-- STOK --}}
                                        <td class="stok-text">
                                            {{ $stokFormat }}
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="text-center">
                                            <span class="badge {{ $badge }}">{{ $status }}</span>
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
                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div>
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
