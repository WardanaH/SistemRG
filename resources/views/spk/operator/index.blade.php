@extends('spk.layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Card 1: Total Masuk (Tugas Saya) --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment_ind</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Tugas Saya</p>
                        <h4 class="mb-0">{{ $totalMasuk }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-info text-sm font-weight-bolder">Kumulatif </span>item yang ditugaskan</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Belum Beres (Antrian Aktif Saya) --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">format_list_bulleted</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Tugas Belum Selesai</p>
                        <h4 class="mb-0 text-warning">{{ $totalProses }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">Item yang perlu dikerjakan</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Sudah Beres (Pencapaian Saya) --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">task_alt</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Telah Saya Selesaikan</p>
                        <h4 class="mb-0 text-success">{{ $totalSelesai }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Berhasil </span>diproduksi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Detail List Tugas Aktif --}}
        <div class="col-lg-6 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <h6 class="text-white ps-3">Beban Kerja Aktif Saya</h6>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-icon-only btn-rounded btn-outline-primary mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="material-icons">print</i></button>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Order Reguler</h6>
                                    <span class="text-xs">Tugas internal cabang</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-primary text-gradient text-sm font-weight-bold">
                                {{ $ongoingReguler }} Item
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"><i class="material-icons">handshake</i></button>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Order Bantuan</h6>
                                    <span class="text-xs">Titipan cabang lain</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold">
                                {{ $ongoingBantuan }} Item
                            </div>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('spk.produksi') }}" class="btn btn-sm bg-gradient-info w-100">Buka Daftar Pekerjaan</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 mt-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                    <h5 class="font-weight-bolder">Siap Mencetak Hari Ini?</h5>
                    <p class="text-sm">Fokus pada kualitas hasil cetak dan ketepatan ukuran. Jangan lupa update status ke <b>DONE</b> jika item sudah selesai agar admin segera tahu.</p>
                    <div class="stats">
                        <span class="badge badge-pill badge-md bg-gradient-secondary">Operator: {{ auth()->user()->nama }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
