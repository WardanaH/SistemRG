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
                                <input type="text" name="stok" class="form-control number-format" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Batas Stok</label>
                                <input type="text" name="batas_stok" class="form-control number-format" required>
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
                    <div class="px-3 pt-3">
                        <form method="GET" action="{{ route('barang.pusat') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cari nama barang..."
                                        value="{{ request('search') }}"
                                        autofocus>
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
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
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
                                    {{-- NO --}}
                                    <td class="text-center">{{ $i + 1 }}</td>

                                    {{-- NAMA BARANG --}}
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-primary
                                                        d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">inventory_2</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $item->nama_bahan }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $item->satuan }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- SATUAN --}}
                                    <td>{{ $item->satuan }}</td>

                                    {{-- KETERANGAN --}}
                                    <td>{{ $item->keterangan ?? '-' }}</td>

                                    {{-- STOK --}}
                                    <td>{{ $item->stok }}</td>

                                    {{-- STATUS --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badge }}">{{ $status }}</span>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
                                        {{-- EDIT --}}
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

                                        {{-- HAPUS --}}
                                        <button type="button"
                                            class="btn btn-link text-danger px-2"
                                            onclick="hapusData({{ $item->id }})">
                                            <i class="material-icons-round">delete</i>
                                        </button>

                                        {{-- FORM DELETE (WAJIB ADA) --}}
                                        <form id="hapus-{{ $item->id }}"
                                            action="{{ route('barang.pusat.destroy', $item->id) }}"
                                            method="POST"
                                            style="display: none;">
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
                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div>
                                Menampilkan {{ $datas->firstItem() }} - {{ $datas->lastItem() }} dari {{ $datas->total() }} data
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text"
                            name="nama_bahan"
                            id="edit_nama"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="satuan"
                                id="edit_satuan"
                                class="form-control">
                            <option>PCS</option>
                            <option>PAKET</option>
                            <option>KG</option>
                            <option>METER</option>
                            <option>CENTIMETER</option>
                            <option>ROLL</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok</label>
                        <input type="text"
                            name="stok"
                            id="edit_stok"
                            class="form-control number-format"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batas Stok</label>
                        <input type="text"
                            name="batas_stok"
                            id="edit_batas"
                            class="form-control number-format"
                            required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan"
                                id="edit_keterangan"
                                class="form-control"
                                rows="2"
                                placeholder="Opsional"></textarea>
                    </div>
                </div>
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
<script>
function formatNumberID(value) {
    value = value.replace(/[^0-9,]/g, '');

    let parts = value.split(',');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return parts.length > 1
        ? parts[0] + ',' + parts[1].slice(0, 2)
        : parts[0];
}

document.querySelectorAll('.number-format').forEach(input => {
    input.addEventListener('input', function () {
        this.value = formatNumberID(this.value);
    });
});
</script>

@endsection
