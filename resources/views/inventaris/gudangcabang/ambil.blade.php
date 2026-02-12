@extends('inventaris.layouts.app')

@section('title', 'Permintaan Ambil')

@section('content')
<div class="container-fluid py-4">

    {{-- =====================
    SWEETALERT
    ===================== --}}
    @foreach (['success'=>'Berhasil','error'=>'Gagal'] as $key=>$title)
        @if(session($key))
            <script>
                document.addEventListener('DOMContentLoaded',()=>{
                    Swal.fire({
                        icon:'{{ $key }}',
                        title:'{{ $title }}',
                        text:'{{ session($key) }}',
                        timer:2000,
                        showConfirmButton:false
                    })
                })
            </script>
        @endif
    @endforeach

    {{-- =====================
    FORM PERMINTAAN
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">

                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Buat Permintaan Ambil</h6>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('gudangcabang.ambil.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cabang Tujuan</label>
                                <select name="cabang_tujuan_id"
                                        class="form-control select2"
                                        required>
                                    <option disabled selected>Pilih Cabang</option>
                                    @foreach($cabangs as $c)
                                        <option value="{{ $c->id }}">
                                            {{ $c->nama }} ({{ $c->slug }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Atas Nama</label>
                                <input type="text"
                                       name="atas_nama"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        <label class="form-label fw-bold">Keterangan</label>
                        <div id="keterangan-wrapper">
                            <div class="input-group mb-2">
                                <input type="text"
                                       name="keterangan[]"
                                       class="form-control"
                                       required>
                                <button type="button"
                                        class="btn btn-outline-danger remove-keterangan"
                                        disabled>✕</button>
                            </div>
                        </div>

                        <button type="button"
                                class="btn btn-outline-primary btn-sm mb-3"
                                id="add-keterangan">
                            + Tambah
                        </button>

                        <div class="text-end">
                            <button class="btn bg-gradient-success">
                                Kirim permintaan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- =====================
    TABEL PERMINTAAN
    ===================== --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card my-4">

                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Daftar Permintaan Ambil</h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Cabang Tujuan</th>
                                    <th>Atas Nama</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($permintaan as $p)
                            {{-- @php
                                if ($p->status === 'Pending') {
                                    $badge = 'bg-gradient-warning';
                                } elseif ($p->status === 'Dikirim') {
                                    $badge = 'bg-gradient-info';
                                } elseif ($p->status === 'Diterima') {
                                    $badge = 'bg-gradient-success';
                                } else {
                                    $badge = 'bg-gradient-secondary';
                                }
                            @endphp --}}
                            <tr>

                                {{-- KODE --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-primary me-3
                                                    d-flex align-items-center justify-content-center">
                                            <i class="material-icons-round text-white text-sm">
                                                inventory_2
                                            </i>
                                        </div>
                                        <span class="text-sm fw-bold">{{ $p->kode }}</span>
                                    </div>
                                </td>

                                {{-- CABANG TUJUAN --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons-round text-primary me-2">
                                            store
                                        </i>
                                        <span class="text-sm">
                                            {{ $p->cabangTujuan->nama ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- ATAS NAMA --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons-round text-info me-2">
                                            person
                                        </i>
                                        <span class="text-sm">
                                            {{ $p->atas_nama }}
                                        </span>
                                    </div>
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons-round text-warning me-2">
                                            calendar_today
                                        </i>
                                        <span class="text-sm">
                                            {{ $p->tanggal }}
                                        </span>
                                    </div>
                                </td>
                                    <td class="text-center">
                                        @php
                                            $badge = match($p->status) {
                                                'Pending'  => 'bg-gradient-warning',
                                                'Dikirim'  => 'bg-gradient-info',
                                                'Diterima' => 'bg-gradient-success',
                                                default    => 'bg-gradient-secondary',
                                            };
                                        @endphp

                                        <span class="badge {{ $badge }}">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-link text-info px-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $p->id }}">
                                            <i class="material-icons-round">receipt_long</i>
                                        </button>

                                        @if($p->status==='Dikirim')
                                        <button class="btn btn-sm bg-gradient-success text-white"
                                                data-bs-toggle="modal"
                                                data-bs-target="#terimaModal{{ $p->id }}">
                                            Terima
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        Belum ada permintaan ambil
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
MODAL DETAIL & TERIMA
===================== --}}
@foreach($permintaan as $p)

{{-- DETAIL --}}
<div class="modal fade" id="detailModal{{ $p->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-gradient-primary">
                <h5 class="text-white mb-0">Detail Permintaan</h5>
                <button class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table align-items-center">
                    <tr><th>Kode</th><td>{{ $p->kode }}</td></tr>
                    <tr><th>Cabang Pengirim</th><td>{{ $p->cabangPengirim->nama ?? '-' }}</td></tr>
                    <tr><th>Cabang Tujuan</th><td>{{ $p->cabangTujuan->nama ?? '-' }}</td></tr>
                    <tr><th>Atas Nama</th><td>{{ $p->atas_nama }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $p->tanggal }}</td></tr>
                </table>

                <h6 class="fw-bold mt-3">List Pengambilan</h6>
                <ul>
                    @foreach((array)$p->keterangan as $k)
                        <li>{{ is_array($k)?implode(', ',$k):$k }}</li>
                    @endforeach
                </ul>

                @if($p->status==='Diterima')
                    <hr>
                    <h6 class="fw-bold">List Barang Diterima</h6>
                    <ul>
                        @foreach((array)$p->keterangan_diterima as $k)
                            <li>{{ $k }}</li>
                        @endforeach
                    </ul>

                    @if($p->bukti_foto)
                        <img src="{{ asset('storage/'.$p->bukti_foto) }}"
                             class="img-fluid rounded shadow"
                             style="max-height:300px">
                    @endif
                @endif
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- TERIMA --}}
<div class="modal fade" id="terimaModal{{ $p->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST"
              action="{{ route('gudangcabang.ambil.terima',$p->id) }}"
              enctype="multipart/form-data"
              class="modal-content">
            @csrf

            <div class="modal-header bg-gradient-success">
                <h5 class="text-white mb-0">Terima Barang</h5>
                <button class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <h6 class="fw-bold">Barang Diterima</h6>
                @foreach((array)$p->keterangan as $i=>$k)
<div class="form-check form-check-success">
    <input class="form-check-input"
        type="checkbox"
        name="barang_diterima[]"
        id="b{{ $p->id.$i }}"
        value="{{ is_array($k)?implode(', ',$k):$k }}"
    >
    <label class="form-check-label" for="b{{ $p->id.$i }}">
        {{ is_array($k)?implode(', ',$k):$k }}
    </label>
</div>
                @endforeach

                <div class="mt-3">
                    <label class="form-label fw-bold">
                        Upload Foto Bukti
                    </label>
                    <input type="file"
                           name="foto_bukti"
                           class="form-control"
                           required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                <button class="btn bg-gradient-success">
                    Terima
                </button>
            </div>
        </form>
    </div>
</div>

@endforeach

{{-- =====================
SCRIPT
===================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    $('.select2').select2({ width:'100%' })

    const addBtn = document.getElementById('add-keterangan')
    const wrapper = document.getElementById('keterangan-wrapper')

    addBtn.addEventListener('click', function () {
        wrapper.insertAdjacentHTML('beforeend', `
            <div class="input-group mb-2">
                <input type="text" name="keterangan[]" class="form-control" required>
                <button type="button"
                        class="btn btn-outline-danger remove-keterangan">
                    ✕
                </button>
            </div>
        `)
    })

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-keterangan')) {
            e.target.closest('.input-group').remove()
        }
    })

})
</script>

@endsection
