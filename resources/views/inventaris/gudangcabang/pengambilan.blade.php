@extends('inventaris.layouts.app')
@section('title', 'Pengambilan-antar')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT NOTIFIKASI
    ===================== --}}
    @foreach (['success'=>'Berhasil'] as $key => $title)
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
    FORM TAMBAH PENGAMBILAN LANGSUNG
    ===================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card my-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-3 pb-3">
                        <h6 class="text-white ps-3">Tambah Pengambilan Barang</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('gudangcabang.pengambilan.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label>Ambil Ke</label>
                                <input type="text" name="ambil_ke" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                        </div>

                        {{-- Wrapper list barang --}}
                        <div class="mb-3">
                            <label>List Barang</label>
                            <div id="listBarangWrapper">
                                <div class="row barang-item mb-2">
                                    <div class="col-md-4">
                                        <input type="text" name="list_barang[0][nama_barang]" class="form-control" placeholder="Nama Barang" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="list_barang[0][jumlah]" class="form-control" placeholder="Qty" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="list_barang[0][atas_nama]" class="form-control" placeholder="Atas Nama" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-remove-barang">Hapus</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-info" id="btnAddBarang">Tambah Barang</button>
                        </div>

                        <div class="mb-3">
                            <label>Foto (Opsional)</label>
                            <input type="file" name="foto" class="form-control">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn bg-gradient-success">
                                <i class="material-icons text-sm">add</i> Simpan Pengambilan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================
    TABEL DATA PENGAMBILAN
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-3 pb-3">
                        <h6 class="text-white ps-3">Data Pengambilan Barang</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive px-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Tanggal</th>
                                    <th>Ambil Ke</th>
                                    {{-- <th>Atas Nama</th> --}}
                                    <th>List Barang</th>
                                    <th>Foto</th>
                                    <th class="text-center">Detail</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $item->tanggal->format('d-m-Y') }}</td>
                                    <td>{{ $item->ambil_ke }}</td>

                                    <td>
                                        <ul>
                                            @foreach($item->list_barang as $b)
                                                <li>{{ $b['nama_barang'] }} : {{ $b['jumlah'] }} ({{ $b['atas_nama'] ?? '-' }})</li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    {{-- FOTO --}}
                                    <td>
                                        @if($item->foto)
                                            <a href="{{ asset('storage/'.$item->foto) }}" target="_blank">Lihat Foto</a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- DETAIL --}}
                                    <td class="text-center">
                                        <button class="btn btn-link text-info px-2"
                                            onclick="showDetail({{ $item->id }})">
                                            <i class="material-icons">receipt_long</i>
                                        </button>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">

                                        {{-- EDIT --}}
                                        <button class="btn btn-link text-warning px-2"
                                            onclick="editData({{ $item->id }})">
                                            <i class="material-icons-round">edit</i>
                                        </button>

                                        {{-- DELETE --}}
                                        <button class="btn btn-link text-danger px-2"
                                            onclick="hapusData({{ $item->id }})">
                                            <i class="material-icons-round">delete</i>
                                        </button>

                                        <form id="hapus-{{ $item->id }}"
                                            action="{{ route('gudangcabang.pengambilan.destroy',$item->id) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada data pengambilan
                                    </td>
                                </tr>
                                @endforelse
                                </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div>
                                Menampilkan {{ $datas->firstItem() ?? 0 }} - {{ $datas->lastItem() ?? 0 }} dari {{ $datas->total() ?? 0 }} data
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
{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form id="formEdit" method="POST" enctype="multipart/form-data">
@csrf

<div class="modal-header">
    <h5 class="modal-title">Edit Pengambilan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <input type="hidden" id="edit_id">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Ambil Ke</label>
            <input type="text" id="edit_ambil_ke" name="ambil_ke" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Tanggal</label>
            <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
        </div>
    </div>

    <div class="mb-3">
        <label>List Barang</label>
        <div id="edit_list_barang"></div>
    </div>

    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="foto" class="form-control">
    </div>

</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-success">Update</button>
</div>

</form>

</div>
</div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Pengambilan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="detailContent">
                {{-- isi detail dari JS --}}
            </div>

        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let indexBarang = 1;

    // Tambah barang
    $('#btnAddBarang').on('click', function () {
        let html = `
        <div class="row barang-item mb-2">
            <div class="col-md-4">
                <input type="text" name="list_barang[${indexBarang}][nama_barang]" class="form-control" placeholder="Nama Barang" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="list_barang[${indexBarang}][jumlah]" class="form-control" placeholder="Qty" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="list_barang[${indexBarang}][atas_nama]" class="form-control" placeholder="Atas Nama" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-remove-barang">Hapus</button>
            </div>
        </div>
        `;
        $('#listBarangWrapper').append(html);
        indexBarang++;
    });

    // Hapus barang
    $(document).on('click', '.btn-remove-barang', function () {
        $(this).closest('.barang-item').remove();
    });

    // Hapus data pengambilan
    window.hapusData = function(id){
        Swal.fire({
            title:'Yakin hapus?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText:'Ya, hapus!'
        }).then(r=>{
            if(r.isConfirmed) document.getElementById('hapus-'+id).submit();
        })
    }

});

// ================= EDIT =================
window.editData = function(id){

    $.get("{{ route('gudangcabang.pengambilan.edit','') }}/"+id, function(res){

        $('#edit_id').val(res.id);
        $('#edit_ambil_ke').val(res.ambil_ke);
        $('#edit_tanggal').val(res.tanggal);

        let html = '';

        res.list_barang.forEach((b,i)=>{
            html += `
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="text" name="list_barang[${i}][nama_barang]"
                    value="${b.nama_barang}" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="number" name="list_barang[${i}][jumlah]"
                    value="${b.jumlah}" class="form-control">
                </div>
                <div class="col-md-5">
                    <input type="text" name="list_barang[${i}][atas_nama]"
                    value="${b.atas_nama}" class="form-control">
                </div>
            </div>
            `;
        });

        $('#edit_list_barang').html(html);

        $('#formEdit').attr('action','/gudang-cabang/pengambilan/update/'+id);

        $('#modalEdit').modal('show');
    });
};


// ================= DETAIL =================
window.showDetail = function(id){

    $.get("{{ route('gudangcabang.pengambilan.edit','') }}/"+id, function(res){

        let barang = '';

        res.list_barang.forEach(b=>{
            barang += `
            <tr>
                <td>${b.nama_barang}</td>
                <td>${b.jumlah}</td>
                <td>${b.atas_nama ?? '-'}</td>
            </tr>`;
        });

        let html = `
        <div class="text-center mb-3">
            <h5>Memo Pengambilan Barang</h5>
        </div>

        <p><b>Tanggal :</b> ${res.tanggal.substring(0,10)}</p>
        <p><b>Ambil Ke :</b> ${res.ambil_ke}</p>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Atas Nama</th>
                </tr>
            </thead>
            <tbody>${barang}</tbody>
        </table>
        `;

        $('#detailContent').html(html);
        $('#modalDetail').modal('show');
    });
};


// ================= UPDATE SUBMIT =================
$('#formEdit').submit(function(e){
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        success:function(){
            Swal.fire('Berhasil','Data diupdate','success')
            .then(()=>location.reload());
        }
    });
});

</script>
@endpush

