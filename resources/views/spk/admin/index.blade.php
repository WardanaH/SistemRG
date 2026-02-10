@extends('spk.layout.app')

@section('content')
<div class="container-fluid py-4">

    {{-- BARIS 1: STATISTIK RINGKAS --}}
    <div class="row">
        {{-- CARD 1: SPK MASUK --}}
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">SPK Masuk Hari Ini</p>
                        <h4 class="mb-0">
                            {{ $spkRegulerToday }} <span class="text-xs text-secondary font-weight-normal">Reg</span> |
                            <span class="text-warning">{{ $spkBantuanToday }}</span> <span class="text-xs text-secondary font-weight-normal">Ban</span>
                        </h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm"><span class="text-success text-sm font-weight-bolder">Total: {{ $spkRegulerToday + $spkBantuanToday }} </span> order baru</p>
                </div>
            </div>
        </div>

        {{-- CARD 2: ANTRIAN --}}
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Item Dalam Antrian</p>
                        <h4 class="mb-0">
                            {{ $antrianReguler }} <span class="text-xs text-secondary font-weight-normal">Reg</span> |
                            <span class="text-info">{{ $antrianBantuan }}</span> <span class="text-xs text-secondary font-weight-normal">Ban</span>
                        </h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm">Sedang diproses operator</p>
                </div>
            </div>
        </div>

        {{-- CARD 3: SELESAI --}}
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">task_alt</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Produksi Selesai</p>
                        <h4 class="mb-0">
                            {{ $selesaiReguler }} <span class="text-xs text-secondary font-weight-normal">Reg</span> |
                            <span class="text-warning">{{ $selesaiBantuan }}</span> <span class="text-xs text-secondary font-weight-normal">Ban</span>
                        </h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm">Akumulasi item status DONE</p>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: GRAFIK COMPARISON --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                        <div class="chart">
                            <canvas id="chart-spk" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-0">Komparasi Tren Order (30 Hari)</h6>
                    <p class="text-sm">Perbandingan input SPK <span class="text-info font-weight-bold">Reguler</span> vs <span class="text-warning font-weight-bold">Bantuan</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Ambil Data & Parse JSON
    var labels = JSON.parse('{!! json_encode($chartLabels) !!}');
    var dataReguler = JSON.parse('{!! json_encode($chartReguler) !!}');
    var dataBantuan = JSON.parse('{!! json_encode($chartBantuan) !!}');

    var ctx = document.getElementById("chart-spk").getContext("2d");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                // DATASET 1: SPK REGULER (Biru/Putih)
                {
                    label: "SPK Reguler",
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: "#1A73E8", // Biru Google
                    pointBorderColor: "transparent",
                    borderColor: "#1A73E8",
                    backgroundColor: "transparent",
                    fill: false,
                    data: dataReguler,
                    maxBarLength: 6
                },
                // DATASET 2: SPK BANTUAN (Kuning/Oranye)
                {
                    label: "SPK Bantuan",
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: "#FB8C00", // Oranye Material
                    pointBorderColor: "transparent",
                    borderColor: "#FB8C00",
                    backgroundColor: "transparent",
                    fill: false,
                    data: dataBantuan,
                    maxBarLength: 6
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true, // Tampilkan Legend agar tau mana reguler mana bantuan
                    labels: {
                        color: '#ffffff'
                    } // Warna text legend putih (karena background gelap)
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 14,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 12,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });
</script>
@endsection
