@extends('inventaris.layouts.app')

@section('title', 'Update Stok Gudang Pusat')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIFIKASI
    ===================== --}}
    @foreach (['success' => 'Berhasil', 'error' => 'Gagal'] as $key => $title)
        @if(session($key))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: '{{ $key === "success" ? "success" : "error" }}',
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
    FORM UPDATE STOK
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Update Stok Barang</h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('barang.pusat.updatestok.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Barang</label>
                                <select name="barang_id" class="form-control select2" required>
                                    <option disabled selected>Pilih Barang</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->nama_bahan }} ({{ $b->stok_formatted }} {{ $b->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Tambah Stok</label>
                                <input type="text"
                                       name="tambah_stok"
                                       id="tambah_stok"
                                       class="form-control number-format"
                                       placeholder="contoh = 100 / 10,5">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Kurangi Stok</label>
                                <input type="text"
                                       name="kurangi_stok"
                                       id="kurangi_stok"
                                       class="form-control number-format"
                                       placeholder="contoh: 50 / 1,25">
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn bg-gradient-success">
                                <i class="material-icons text-sm">sync</i>
                                Simpan Update
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
                        <h6 class="text-white ps-3">Data Stok Barang</h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama</th>
                                    {{-- <th>Satuan</th> --}}
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>

                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="avatar avatar-sm me-3 border-radius-md
                                                bg-gradient-primary d-flex align-items-center justify-content-center">
                                                <i class="material-icons text-white text-sm">inventory_2</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $item->nama_bahan }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $item->satuan }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- <td>{{ $item->satuan }}</td> --}}
                                    <td>{{ $item->stok_formatted }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
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
SCRIPT
===================== --}}
<script>
$('.select2').select2({ width:'100%' });

$('#tambah_stok').on('input', function () {
    $('#kurangi_stok').prop('disabled', $(this).val() !== '');
});

$('#kurangi_stok').on('input', function () {
    $('#tambah_stok').prop('disabled', $(this).val() !== '');
});
</script>
@endsection
