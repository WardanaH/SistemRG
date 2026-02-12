@extends('inventaris.layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- <div class="container-fluid py-4"> --}}
      <div class="row">

        <!-- kotak 1 -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">local_shipping</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Pengiriman Masuk Hari Ini</p>
                    <h4 class="mb-0">{{ $pengirimanMasukHariIni }}</h4>
                </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                <p class="mb-0">Status: <span class="text-warning">Dikirim</span></p>
                </div>
            </div>
        </div>

        <!-- kotak 2-->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">inventory</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Pengiriman Diterima Hari Ini</p>
                    <h4 class="mb-0">{{ $pengirimanDiterimaHariIni }}</h4>
                </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                <p class="mb-0">Status: <span class="text-success">Diterima</span></p>
                </div>
            </div>
        </div>

        <!-- kotak 3 -->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                    <i class="material-icons opacity-10">add_box</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Barang Masuk Hari Ini</p>
                    <h4 class="mb-0">{{ $totalBarangMasukHariIni }}</h4>
                </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                <p class="mb-0">Total unit barang diterima</p>
                </div>
            </div>
        </div>

        <!-- kotak 4-->
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                <div class="icon icon-lg icon-shape shadow-primary text-center border-radius-xl mt-n4 position-absolute"
                    style="background: linear-gradient(195deg, #ec407a, #d81b60);">
                    <i class="material-icons opacity-10">category</i>
                </div>
                <div class="text-end pt-1">
                    <p class="text-sm mb-0 text-capitalize">Total Jenis Barang</p>
                    <h4 class="mb-0">{{ $totalJenisBarang }}</h4>
                </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                <p class="mb-0">Tersedia di cabang</p>
                </div>
            </div>
        </div>

      </div>

      <!-- grafik-->
      <div class="row mt-4">
        <!-- grafik 1 -->
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
            <h6 class="mb-0">Pengiriman ke Cabang</h6>
            <p class="text-sm">Total pengiriman per hari (7 hari terakhir)</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastPengirimanUpdate
                        ? $lastPengirimanUpdate->created_at->diffForHumans()
                        : 'belum ada data' }}
                </p>

              </div>
            </div>
          </div>
        </div>

        <!-- grafik 2 -->
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
            <h6 class="mb-0">Pengiriman Diterima</h6>
            <p class="text-sm">Total pengiriman diterima per hari</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastPenerimaanUpdate
                        ? $lastPenerimaanUpdate->tanggal_diterima->diffForHumans()
                        : 'belum ada penerimaan' }}
                </p>

              </div>
            </div>
          </div>
        </div>

        <!-- grafik 3 -->
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="border-radius-lg py-3 pe-1"
                style="background: linear-gradient(195deg, #ec407a, #d81b60); box-shadow: 0 4px 20px rgba(216,27,96,.4);">
                <div class="chart">
                    <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
            </div>
            </div>
            <div class="card-body">
            <h6 class="mb-0">Barang Masuk</h6>
            <p class="text-sm">Total stok masuk per hari</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastBarangUpdate
                        ? $lastBarangUpdate->tanggal_diterima->diffForHumans()
                        : 'belum ada perubahan stok' }}
                </p>

              </div>
            </div>
          </div>
        </div>
      </div>


      </div>
    {{-- </div> --}}
@endsection

@push('scripts')
<script>
var ctx = document.getElementById("chart-bars").getContext("2d");

new Chart(ctx, {
  type: "bar",
  data: {
    labels: @json($labels7Hari),
    datasets: [{
      label: "Pengiriman Dikirim",
      tension: 0.4,
      borderWidth: 0,
      borderRadius: 4,
      borderSkipped: false,
      backgroundColor: "rgba(255,255,255,.8)",
      data: @json($grafikPengirimanMasuk),
      maxBarThickness: 6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: "#111827",
        titleColor: "#fff",
        bodyColor: "#fff",
        padding: 10,
        callbacks: {
          label: function(context) {
            return " Total pengiriman dikirim: " + context.parsed.y;
          }
        }
      }
    },
    interaction: { intersect: false, mode: 'index' },
    scales: {
      y: {
        grid: {
          drawBorder: false,
          display: true,
          drawOnChartArea: true,
          drawTicks: false,
          borderDash: [5,5],
          color: 'rgba(255,255,255,.2)'
        },
        ticks: { color: "#fff", padding: 10 }
      },
      x: {
        grid: {
          drawBorder: false,
          display: true,
          drawOnChartArea: true,
          drawTicks: false,
          borderDash: [5,5],
          color: 'rgba(255,255,255,.2)'
        },
        ticks: { color: "#f8f9fa", padding: 10 }
      }
    }
  }
});
</script>

<script>
var ctx2 = document.getElementById("chart-line").getContext("2d");

new Chart(ctx2, {
  type: "line",
  data: {
    labels: @json($labels7Hari),
    datasets: [{
      label: "Pengiriman Diterima",
      tension: 0,
      pointRadius: 5,
      pointBackgroundColor: "rgba(255,255,255,.8)",
      pointBorderColor: "transparent",
      borderColor: "rgba(255,255,255,.8)",
      borderWidth: 4,
      backgroundColor: "transparent",
      fill: true,
      data: @json($grafikPengirimanDiterima),
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: "#111827",
        titleColor: "#fff",
        bodyColor: "#fff",
        callbacks: {
          label: function(context) {
            return " Pengiriman diterima: " + context.parsed.y;
          }
        }
      }
    },
    interaction: { intersect: false, mode: 'index' },
    scales: {
      y: {
        grid: { drawBorder: false, borderDash: [5,5], color: 'rgba(255,255,255,.2)' },
        ticks: { color: "#fff", padding: 10 }
      },
      x: {
        grid: { display: false },
        ticks: { color: "#fff", padding: 10 }
      }
    }
  }
});
</script>

<script>
var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

new Chart(ctx3, {
  type: "line",
  data: {
    labels: @json($labels7Hari),
    datasets: [{
      label: "Barang Masuk",
      tension: 0,
      pointRadius: 5,
      pointBackgroundColor: "rgba(255,255,255,.8)",
      pointBorderColor: "transparent",
      borderColor: "rgba(255,255,255,.8)",
      borderWidth: 4,
      backgroundColor: "transparent",
      fill: true,
      data: @json($grafikBarangMasuk),
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: "#111827",
        titleColor: "#fff",
        bodyColor: "#fff",
        callbacks: {
          label: function(context) {
            return " Total barang masuk: " + context.parsed.y;
          }
        }
      }
    },
    interaction: { intersect: false, mode: 'index' },
    scales: {
      y: {
        grid: { drawBorder: false, borderDash: [5,5], color: 'rgba(255,255,255,.2)' },
        ticks: { color: "#fff", padding: 10 }
      },
      x: {
        grid: { display: false },
        ticks: { color: "#fff", padding: 10 }
      }
    }
  }
});
</script>

@endpush
