@extends('inventaris.layouts.app')

@section('title', 'Inventaris Gudang Cabang')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIF
    ===================== --}}
    @foreach (['success' => 'Berhasil'] as $key => $title)
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
    FORM TAMBAH INVENTARIS
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Tambah Inventaris Kantor Cabang</h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('gudangcabang.inventaris.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Kode Barang</label>
                                <input type="text" name="kode_barang" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah" class="form-control" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Kondisi</label>
                                <select name="kondisi" class="form-control">
                                    <option>Baik</option>
                                    <option>Rusak Ringan</option>
                                    <option>Rusak Berat</option>
                                </select>
                            </div>

                            {{-- <div class="col-md-2 mb-3">
                                <label>Lokasi</label>
                                <input type="text" name="lokasi" class="form-control">
                            </div> --}}

                            <div class="col-md-3 mb-3">
                                <label>Tanggal Input</label>
                                <input type="date" name="tanggal_input" class="form-control" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn bg-gradient-success">
                                <i class="material-icons text-sm">add</i> Simpan Inventaris
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================
    TABEL DATA INVENTARIS
    ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Data Inventaris Gudang Cabang</h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    {{-- <th>No</th> --}}
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Kondisi</th>
                                    {{-- <th>Lokasi</th> --}}
                                    <th>Tanggal</th>
                                    <th class="text-center">QR Code</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $i => $item)
                                <tr>
                                    <td>{{ $item->kode_barang }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>{{ $item->kondisi }}</td>
                                    {{-- <td>{{ $item->lokasi ?? '-' }}</td> --}}
                                    <td>{{ $item->tanggal_input }}</td>

                                    {{-- QR CODE --}}
                                    <td class="text-center">
                                        @if($item->qr_code)
                                            <img
                                                src="{{ asset('storage/'.$item->qr_code) }}"
                                                width="70"
                                                class="mb-1"
                                            >
                                            <br>

                                            <a href="{{ asset('storage/'.$item->qr_code) }}"
                                            download
                                            class="btn btn-sm btn-outline-primary">
                                                Download
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning btn-edit"
                                                data-id="{{ $item->id }}">
                                            Edit
                                        </button>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Data inventaris belum tersedia
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
MODAL EDIT INVENTARIS
===================== --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="formEdit" class="modal-content">
            @csrf
            @method('PUT')

            <input type="hidden" id="edit_id">

            <div class="modal-header">
                <h5>Edit Inventaris</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="text" name="nama_barang" id="edit_nama" class="form-control mb-2" required>
                <input type="number" name="jumlah" id="edit_jumlah" class="form-control mb-2" required>
                <select name="kondisi" id="edit_kondisi" class="form-control mb-2">
                    <option>Baik</option>
                    <option>Rusak Ringan</option>
                    <option>Rusak Berat</option>
                </select>
                <input type="text" name="lokasi" id="edit_lokasi" class="form-control">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn bg-gradient-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editInventaris(id){
    fetch(`/gudang-cabang/inventaris/${id}/edit`)
    .then(res => res.json())
    .then(d => {
        edit_id.value = d.id;
        edit_nama.value = d.nama_barang;
        edit_jumlah.value = d.jumlah;
        edit_kondisi.value = d.kondisi;
        edit_lokasi.value = d.lokasi ?? '';
        formEdit.action = `/gudang-cabang/inventaris/${id}`;
        new bootstrap.Modal(modalEdit).show();
    });
}

document.getElementById('formEdit').addEventListener('submit', function(e){
    e.preventDefault();
    fetch(this.action,{
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:new FormData(this)
    }).then(()=>location.reload());
});
</script>
@endpush
