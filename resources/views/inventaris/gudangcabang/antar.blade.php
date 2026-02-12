@extends('inventaris.layouts.app')

@section('title', 'Antar Barang')

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
    TABEL ANTAR BARANG
    ===================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">

                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white ps-3">Daftar Permintaan Antar</h6>
                    </div>
                </div>

                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Cabang Pengirim</th>
                                    <th>Atas Nama</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($permintaan as $p)
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
                                            <span class="text-sm fw-bold">
                                                {{ $p->kode }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- CABANG --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons-round text-primary me-2">
                                                store
                                            </i>
                                            <span class="text-sm">
                                                {{ $p->cabangPengirim->nama ?? '-' }}
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

                                    {{-- STATUS --}}
                                    <td class="text-center">
                                        @php
                                            $badge = match($p->status) {
                                                'Menunggu' => 'bg-gradient-warning',
                                                'Dikirim'  => 'bg-gradient-info',
                                                'Diterima' => 'bg-gradient-success',
                                                default    => 'bg-gradient-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">
                                            {{ $p->status }}
                                        </span>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">

                                        {{-- DETAIL --}}
                                        <button class="btn btn-link text-info px-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $p->id }}">
                                            <i class="material-icons-round">
                                                receipt_long
                                            </i>
                                        </button>

                                        {{-- KIRIM --}}
                                        @if($p->status === 'Menunggu')
                                        <button class="btn btn-sm bg-gradient-success text-white"
                                                onclick="kirimBarang('{{ route('gudangcabang.antar.kirim',$p->id) }}')">
                                            Kirim
                                        </button>
                                        @else
                                            <span class="text-muted fst-italic">
                                                -
                                            </span>
                                        @endif

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        Tidak ada permintaan antar
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
MODAL DETAIL
===================== --}}
@foreach($permintaan as $p)
<div class="modal fade" id="detailModal{{ $p->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-gradient-primary">
                <h5 class="text-white mb-0">Detail Permintaan Antar</h5>
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
                        data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endforeach

{{-- =====================
SCRIPT SWEETALERT KIRIM
===================== --}}
<script>
function kirimBarang(url){
    Swal.fire({
        title:'Kirim barang?',
        text:'Pastikan data sudah benar',
        icon:'question',
        showCancelButton:true,
        confirmButtonText:'Kirim',
        cancelButtonText:'Batal'
    }).then((result)=>{
        if(result.isConfirmed){
            const form = document.createElement('form')
            form.method='POST'
            form.action=url

            const csrf = document.createElement('input')
            csrf.type='hidden'
            csrf.name='_token'
            csrf.value='{{ csrf_token() }}'

            form.appendChild(csrf)
            document.body.appendChild(form)
            form.submit()
        }
    })
}
</script>

@endsection
