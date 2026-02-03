@extends('inventaris.layouts.app')

@section('title', 'Barang Gudang Pusat')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIFIKASI
    ===================== --}}
    @foreach (['tambah' => 'Berhasil', 'edit' => 'Berhasil', 'hapus' => 'Dihapus'] as $key => $title)
        @if(session($key))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ $title }}',
                        text: '{{ session($key) }}',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif
    @endforeach

    {{-- =====================
    FORM TAMBAH BARANG
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Tambah Barang Gudang Pusat</h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('barang.pusat.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Nama Bahan</label>
                                <input type="text" name="nama_bahan" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Satuan</label>
                                <select name="satuan" class="form-control select2" required>
                                    <option disabled selected>Pilih Satuan</option>
                                    <option>PCS</option>
                                    <option>PAKET</option>
                                    <option>KG</option>
                                    <option>METER</option>
                                    <option>CENTIMETER</option>
                                    <option>ROLL</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Batas Stok</label>
                                <input type="number" name="batas_stok" class="form-control" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"
                                          placeholder="Opsional"></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn bg-gradient-success">
                                <i class="material-icons text-sm">add</i> Simpan Barang
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
                        <h6 class="text-white ps-3">Data Barang Gudang Pusat</h6>
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
                                    <th>Keterangan</th>
                                    <th>Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $i => $item)
                                    @php
                                        $persen = $item->batas_stok > 0 ? ($item->stok / $item->batas_stok) * 100 : 0;
                                        if ($item->stok == 0) {
                                            $status = 'Habis'; $badge = 'bg-gradient-danger';
                                        } elseif ($persen <= 20) {
                                            $status = 'Hampir Habis'; $badge = 'bg-gradient-warning';
                                        } elseif ($persen <= 50) {
                                            $status = 'Cukup'; $badge = 'bg-gradient-info';
                                        } else {
                                            $status = 'Aman'; $badge = 'bg-gradient-success';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td class="fw-bold">{{ $item->nama_bahan }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                        <td>{{ $item->stok }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $badge }}">{{ $status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-link text-info px-2"
                                                onclick="editBarang(
                                                    {{ $item->id }},
                                                    '{{ $item->nama_bahan }}',
                                                    '{{ $item->satuan }}',
                                                    {{ $item->stok }},
                                                    {{ $item->batas_stok }},
                                                    '{{ $item->keterangan }}'
                                                )">
                                                <i class="material-icons-round">edit</i>
                                            </button>

                                            <button class="btn btn-link text-danger px-2"
                                                onclick="hapusData({{ $item->id }})">
                                                <i class="material-icons-round">delete</i>
                                            </button>

                                            <form id="hapus-{{ $item->id }}"
                                                  action="{{ route('barang.pusat.destroy',$item->id) }}"
                                                  method="POST" class="d-none">
                                                @csrf @method('DELETE')
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

{{-- =====================
MODAL EDIT BARANG
===================== --}}
<div class="modal fade" id="modalEditBarang">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" id="formEditBarang" class="modal-content">
            @csrf @method('PUT')

            <div class="modal-header">
                <h5>Edit Barang</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="text" name="nama_bahan" id="edit_nama" class="form-control mb-2" required>
                <select name="satuan" id="edit_satuan" class="form-control mb-2">
                    <option>PCS</option><option>PAKET</option><option>KG</option>
                    <option>METER</option><option>CENTIMETER</option>
                </select>
                <input type="number" name="stok" id="edit_stok" class="form-control mb-2" required>
                <input type="number" name="batas_stok" id="edit_batas" class="form-control mb-2" required>
                <textarea name="keterangan" id="edit_keterangan" class="form-control"></textarea>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn bg-gradient-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- =====================
SCRIPT
===================== --}}
<script>
function editBarang(id,nama,satuan,stok,batas,ket){
    edit_nama.value = nama;
    edit_satuan.value = satuan;
    edit_stok.value = stok;
    edit_batas.value = batas;
    edit_keterangan.value = ket ?? '';
    formEditBarang.action = '/gudang-pusat/barang/update/' + id;
    new bootstrap.Modal(modalEditBarang).show();
}
</script>

<script>
function hapusData(id){
    Swal.fire({
        title:'Yakin hapus?',
        icon:'warning',
        showCancelButton:true
    }).then(r=>{
        if(r.isConfirmed) document.getElementById('hapus-'+id).submit();
    })
}
</script>

<script>
$('.select2').select2({ width:'100%' });
</script>

@endsection
