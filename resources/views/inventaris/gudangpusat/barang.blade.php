@extends('inventaris.layouts.app')

@section('title', 'Barang Gudang Pusat')
<style>

.table-modern tbody tr{
    transition: all .2s ease;
}

.table-modern tbody tr:hover{
    background:#f8f9fa;
    transform: scale(1.003);
}

/* aksi kotak */
.action-box{
    display:inline-flex;
    gap:6px;
    padding:4px;
    background:#f1f3f4;
    border-radius:12px;
}

.action-btn{
    border:none;
    width:34px;
    height:34px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.2s;
}

.action-edit{
    background:#e3f2fd;
    color:#1976d2;
}

.action-edit:hover{
    background:#1976d2;
    color:white;
}

.action-delete{
    background:#fdecea;
    color:#d32f2f;
}

.action-delete:hover{
    background:#d32f2f;
    color:white;
}

/* chip satuan */
.chip{
    background:#eef2ff;
    color:#4f46e5;
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
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

.animated-header {
    background: linear-gradient(270deg, #FFD54F, #FFA726, #FFB74D);
    background-size: 600% 600%;
    animation: gradientMove 8s ease infinite;
    color: #fff;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0;
}

@keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animated-header .modal-title {
    font-size: 1.25rem;
}

.animated-header .btn-close {
    filter: brightness(0) invert(1);
}
</style>

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
                                        <option>ROLL</option>
                                        <option>PACK</option>
                                        <option>LEMBAR</option>
                                        <option>METER</option>
                                        <option>BOX</option>
                                        <option>PCS </option>
                                        <option>RIM</option>
                                        <option>TANK</option>
                                        <option>BOTOL</option>
                                        <option>LUSIN</option>
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
                        <table class="table table-modern align-items-center">
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
                                                {{-- <p class="text-xs text-secondary mb-0">{{ $item->satuan }}</p> --}}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- SATUAN --}}
<td>
@php
    $icons = [
        'ROLL' => 'refresh',            // gulungan
        'PACK' => 'inventory_2',       // paket
        'LEMBAR' => 'description',     // kertas
        'METER' => 'straighten',       // pengukur
        'BOX' => 'inbox',              // kotak
        'PCS' => 'widgets',           // item satuan
        'RIM' => 'layers',            // tumpukan kertas
        'TANK' => 'propane_tank',     // tangki
        'BOTOL' => 'liquor',          // botol
        'LUSIN' => 'view_comfy',       // banyak item
        'GABAR' => 'grid_view'
    ];

    $icon = $icons[strtoupper(trim($item->satuan))] ?? 'inventory_2';
@endphp

<div class="d-flex align-items-center gap-2">
    <i class="material-icons text-secondary" style="font-size:18px">
        {{ $icon }}
    </i>

    <span class="fw-semibold text-dark">
        {{ ucfirst(strtolower($item->satuan)) }}
    </span>
</div>
</td>


                                    {{-- KETERANGAN --}}
                                    <td>{{ $item->keterangan ?? '-' }}</td>

                                    {{-- STOK --}}
                                    <td class="stok-text">
                                        {{ number_format($item->stok,0,',','.') }}
                                    </td>


                                    {{-- STATUS --}}
                                    <td class="text-center">
                                        <span class="badge {{ $badge }}">{{ $status }}</span>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
    <div class="action-box">

        <button class="action-btn action-edit"
            onclick="editBarang(
                {{ $item->id }},
                '{{ $item->nama_bahan }}',
                '{{ $item->satuan }}',
                {{ $item->stok }},
                {{ $item->batas_stok }},
                '{{ $item->keterangan }}'
            )">
            <i class="material-icons-round text-sm">edit</i>
        </button>

        <button type="button"
            class="action-btn action-delete"
            onclick="hapusData({{ $item->id }})">
            <i class="material-icons-round text-sm">delete</i>
        </button>

    </div>

    <form id="hapus-{{ $item->id }}"
        action="{{ route('barang.pusat.destroy', $item->id) }}"
        method="POST"
        style="display:none;">
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
            @csrf
            @method('PUT')

            {{-- Modal Header Animasi --}}
            <div class="modal-header animated-header">
                <h5 class="modal-title fw-bold">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="edit_nama" class="form-label fw-semibold">Nama Bahan</label>
                        <input type="text"
                            name="nama_bahan"
                            id="edit_nama"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_satuan" class="form-label fw-semibold">Satuan</label>
                        <select name="satuan"
                                id="edit_satuan"
                                class="form-control select2">
                            <option>ROLL</option>
                            <option>PACK</option>
                            <option>LEMBAR</option>
                            <option>METER</option>
                            <option>BOX</option>
                            <option>PCS </option>
                            <option>RIM</option>
                            <option>TANK</option>
                            <option>BOTOL</option>
                            <option>LUSIN</option>
                            <option>GABAR</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_stok" class="form-label fw-semibold">Stok</label>
                        <input type="text"
                            name="stok"
                            id="edit_stok"
                            class="form-control number-format"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_batas" class="form-label fw-semibold">Batas Stok Maksimal</label>
                        <input type="text"
                            name="batas_stok"
                            id="edit_batas"
                            class="form-control number-format"
                            required>
                    </div>

                    <div class="col-md-12">
                        <label for="edit_keterangan" class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan"
                                id="edit_keterangan"
                                class="form-control"
                                rows="2"
                                placeholder="Opsional"></textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn bg-gradient-primary">Simpan</button>
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

    // Initialize select2 di modal
    $(edit_satuan).select2({
        dropdownParent: $('#modalEditBarang'),
        width: '100%'
    });

    // Set value setelah select2 aktif
    $(edit_satuan).val(satuan).trigger('change');

    new bootstrap.Modal(modalEditBarang).show();
}


// Hapus data
function hapusData(id){
    Swal.fire({
        title:'Yakin hapus?',
        icon:'warning',
        showCancelButton:true
    }).then(r=>{
        if(r.isConfirmed) document.getElementById('hapus-'+id).submit();
    })
}

// Format number
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

$('.select2').select2({ width:'100%' });
</script>

@endsection
