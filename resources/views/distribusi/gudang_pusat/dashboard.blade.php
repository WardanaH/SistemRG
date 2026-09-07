@extends('distribusi.layouts.app')

@section('title', 'Dashboard Gudang Pusat')

@section('content')
    <div class="row">
        <!-- Permintaan Hari Ini -->
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-1 text-uppercase font-weight-bold text-muted">Permintaan Masuk</p>
                                <h2 class="font-weight-bolder mb-0 text-dark">
                                    {{ $permintaanHariIni }}
                                    <span class="text-success text-sm font-weight-bolder ms-1">Hari Ini</span>
                                </h2>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 55px; height: 55px;">
                                <i class="material-icons-round text-white fs-4">assignment</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Belum Diproses -->
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-1 text-uppercase font-weight-bold text-muted">Belum Diproses</p>
                                <h2 class="font-weight-bolder mb-0 text-warning">
                                    {{ $belumDiproses }}
                                </h2>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 55px; height: 55px;">
                                <i class="material-icons-round text-white fs-4">pending_actions</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dikirim Hari Ini -->
        <div class="col-xl-4 col-sm-6">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-1 text-uppercase font-weight-bold text-muted">Barang Dikirim</p>
                                <h2 class="font-weight-bolder mb-0 text-primary">
                                    {{ $dikirimHariIni }}
                                    <span class="text-primary text-sm font-weight-bolder ms-1">Hari Ini</span>
                                </h2>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 55px; height: 55px;">
                                <i class="material-icons-round text-white fs-4">local_shipping</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
