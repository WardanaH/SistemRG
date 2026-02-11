@extends('spk.layout.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        {{-- CARD 1: SPK REGULER --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">description</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">SPK Reguler Hari Ini</p>
                        <h4 class="mb-0">{{ $spkRegulerHariIni }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">Terinput </span>hari ini</p>
                </div>
            </div>
        </div>

        {{-- CARD 2: SPK BANTUAN --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">handshake</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">SPK Bantuan Hari Ini</p>
                        <h4 class="mb-0">{{ $spkBantuanHariIni }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-info text-sm font-weight-bolder">Titipan </span>cabang lain</p>
                </div>
            </div>
        </div>

        {{-- CARD 3: SPK LEMBUR (BARU) --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    {{-- Gunakan warna Warning (Oranye/Kuning) untuk Lembur --}}
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">access_time_filled</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">SPK Lembur Hari Ini</p>
                        <h4 class="mb-0">{{ $spkLemburHariIni }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-warning text-sm font-weight-bolder">Lembur </span>di luar jam</p>
                </div>
            </div>
        </div>

        {{-- CARD 4: TOTAL KUMULATIF --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">leaderboard</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Seluruh SPK</p>
                        <h4 class="mb-0">{{ $totalSemuaSpk }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">Performa total Anda</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Welcome Card --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="font-weight-bolder">Selamat Datang, Designer {{ $user->nama }}!</h5>
                    <p class="text-sm">
                        Panel ini merangkum hasil kerja keras Anda dalam menginput data Surat Perintah Kerja (SPK).
                        Sekarang Anda dapat memantau pesanan <b>Reguler</b>, <b>Bantuan</b>, dan <b>Lembur</b> dalam satu tampilan.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('spk') }}" class="btn btn-primary btn-sm mb-0">Input SPK Baru</a>
                        <a href="{{ route('spk.index') }}" class="btn btn-outline-primary btn-sm mb-0">Lihat Daftar SPK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
