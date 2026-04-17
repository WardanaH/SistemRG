@extends('spk.layout.app')

@section('content')
<div class="container-fluid py-4">

    {{-- BARIS 1: STATISTIK RINGKAS --}}
    <div class="row">
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">assignment</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">SPK Masuk Hari Ini</p>
                        <h4 class="mb-0">{{ $spkToday }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm">Data masuk per tanggal {{ date('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">precision_manufacturing</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Item Dalam Antrian</p>
                        <h4 class="mb-0">{{ $totalAntrian }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm">Total pekerjaan aktif di produksi</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">task_alt</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Produksi Selesai</p>
                        <h4 class="mb-0">{{ $totalSelesai }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-sm">Item yang sudah status DONE</p>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: GRAFIK --}}
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
                    <h6 class="mb-0">Tren Order 30 Hari Terakhir</h6>
                    <p class="text-sm">Jumlah SPK yang diinput oleh Designer</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Ambil data dari Controller & Parse agar IDE tidak error
    // Tanda kutip '' membungkus hasil JSON, lalu JSON.parse mengembalikannya jadi Array/Object JS
    var chartLabels = JSON.parse('{!! json_encode($labels) !!}');
    var chartData = JSON.parse('{!! json_encode($counts) !!}');

    var ctx = document.getElementById("chart-spk").getContext("2d");

    new Chart(ctx, {
        type: "line",
        data: {
            labels: chartLabels, // Masukkan variabel labels
            datasets: [{
                label: "Jumlah SPK",
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointBackgroundColor: "rgba(255, 255, 255, .8)",
                pointBorderColor: "transparent",
                borderColor: "rgba(255, 255, 255, .8)",
                backgroundColor: "transparent",
                fill: true,

                // PERBAIKAN UTAMA DI SINI:
                // Langsung panggil variabel array, JANGAN pakai kurung kurawal { } lagi
                data: chartData,

                maxBarLength: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
                            weight: 300,
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
                            size: 14,
                            weight: 300,
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
