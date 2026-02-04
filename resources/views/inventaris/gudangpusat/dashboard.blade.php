@extends('inventaris.layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- <div class="container-fluid py-4"> --}}
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">hourglass_empty</i>
              </div>
              <!-- kotak 1-->
              <div class="text-end pt-1">
                <p class="text-sm mb-0">Total Permintaan Menunggu</p>
                <h4 class="mb-0">{{ $totalPermintaanMenunggu }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            <p class="mb-0">
                @if($diffPermintaan >= 0)
                <span class="text-success text-sm font-weight-bolder">
                    +{{ $diffPermintaan }}
                </span>
                @else
                <span class="text-danger text-sm font-weight-bolder">
                    {{ $diffPermintaan }}
                </span>
                @endif
                dari kemarin
            </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">local_shipping</i>
              </div>
              <!-- kotak 2-->
              <div class="text-end pt-1">
                <p class="text-sm mb-0">Barang Sedang Dikirim</p>
                <h4 class="mb-0">{{ $totalBarangDikirim }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            <p class="mb-0">
                @if($diffDikirim >= 0)
                <span class="text-success text-sm font-weight-bolder">
                    +{{ $diffDikirim }}
                </span>
                @else
                <span class="text-danger text-sm font-weight-bolder">
                    {{ $diffDikirim }}
                </span>
                @endif
                dari kemarin
            </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">check_circle</i>
              </div>
              <!-- kotak 3 -->
              <div class="text-end pt-1">
                <p class="text-sm mb-0">Pengiriman Tuntas</p>
                <h4 class="mb-0">{{ $totalPengirimanTuntas }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            <p class="mb-0">
                @if($diffTuntas >= 0)
                <span class="text-success text-sm font-weight-bolder">
                    +{{ $diffTuntas }}
                </span>
                @else
                <span class="text-danger text-sm font-weight-bolder">
                    {{ $diffTuntas }}
                </span>
                @endif
                dari kemarin
            </p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape shadow-primary text-center border-radius-xl mt-n4 position-absolute"
                style="background: linear-gradient(195deg, #ec407a, #d81b60);">
                <i class="material-icons opacity-10">inventory_2</i>
            </div>
              <!-- kotak 4 -->
              <div class="text-end pt-1">
                <p class="text-sm mb-0">Total Jenis Barang</p>
                <h4 class="mb-0">{{ $totalJenisBarang }}</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            <p class="mb-0">
                @if($diffBarang >= 0)
                <span class="text-success text-sm font-weight-bolder">
                    +{{ $diffBarang }}
                </span>
                @else
                <span class="text-danger text-sm font-weight-bolder">
                    {{ $diffBarang }}
                </span>
                @endif
                dari kemarin
            </p>
            </div>
          </div>
        </div>
      </div>
      <!-- GRAFIK -->
      <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <!-- grafik 1 -->
            <div class="card-body">
            <h6 class="mb-0">Permintaan Pengiriman (7 Hari Terakhir)</h6>
            <p class="text-sm">Jumlah permintaan masuk per hari</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastPermintaanUpdate ? $lastPermintaanUpdate->created_at->diffForHumans() : 'belum ada data' }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <!-- grafik 2 -->
            <div class="card-body">
            <h6 class="mb-0">Total Stok Dikirim (7 Hari Terakhir)</h6>
            <p class="text-sm">Jumlah barang dikirim per hari</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastStokDikirimUpdate ? $lastStokDikirimUpdate->created_at->diffForHumans() : 'belum ada data' }}
                </p>
              </div>
            </div>
          </div>
        </div>
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
            <!-- grafik 3 -->
            <div class="card-body">
            <h6 class="mb-0">Pengiriman Dikirim (7 Hari Terakhir)</h6>
            <p class="text-sm">Jumlah pengiriman berstatus dikirim</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">
                    diperbarui
                    {{ $lastPengirimanUpdate ? $lastPengirimanUpdate->created_at->diffForHumans() : 'belum ada data' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                    <h6>Barang Keluar</h6>
                    <p class="text-sm mb-0">
                    <i class="fa fa-check text-info" aria-hidden="true"></i>
                    <span class="font-weight-bold ms-1">Barang keluar terbanyak</span> dalam 1 bulan
                    </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Barang</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Cabang Tujuan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"> </th>
                    </tr>
                  </thead>
                    <tbody>
                    @php
                        $max = $topBarangKeluar->max('total'); // untuk 100%
                    @endphp

                    @foreach($topBarangKeluar as $barang)
                    @php
                        $persen = $max > 0 ? round(($barang['total'] / $max) * 100) : 0;
                    @endphp

                    <tr>
                    <!-- NAMA BARANG + ICON -->
                    <td>
                        <div class="d-flex px-2 py-1">
                        <div>
                            <i class="material-icons text-primary me-2">inventory_2</i>
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $barang['nama_barang'] }}</h6>
                        </div>
                        </div>
                    </td>

                    <!-- CABANG TUJUAN -->
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="avatar avatar-sm rounded-circle bg-primary">
                                {{ substr($barang['cabang'],0,1) }}
                            </a>
                            <span class="ms-2 text-sm">{{ $barang['cabang'] }}</span>
                        </div>
                    </td>


                    <!-- JUMLAH KELUAR -->
                    <td class="align-middle text-center text-sm">
                        {{ $barang['total'] }}
                    </td>

                    <!-- PERSENTASE -->
                    <td class="align-middle">
                        <div class="progress-wrapper w-75 mx-auto">
                        <div class="progress-info">
                            <div class="progress-percentage">
                            <span class="text-xs font-weight-bold">{{ $persen }}%</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-gradient-success"
                                role="progressbar"
                                style="width: {{ $persen }}%">
                            </div>
                        </div>
                        </div>
                    </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>Daftar Permintaan Masuk</h6>
              <p class="text-sm">
                <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                <span class="font-weight-bold"> 6 Permintaan terbaru</span> saat ini
              </p>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                    @forelse ($latestNotifications as $notif)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                {{-- SEMUA LONCENG --}}
                                <i class="material-icons text-warning text-gradient">
                                    notifications
                                </i>
                            </span>

                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    Permintaan Pengiriman
                                </h6>

                                <p class="text-secondary text-xs mt-1 mb-0">
                                    Dari Cabang:
                                    <strong>
                                        {{ $notif->cabang->nama ?? '-' }}
                                    </strong>
                                </p>

                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $notif->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-secondary text-sm">
                            Tidak ada permintaan pengiriman
                        </div>
                    @endforelse

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
    labels: {!! json_encode($days) !!},
    datasets: [{
      label: "Permintaan Masuk",
      tension: 0.4,
      borderWidth: 0,
      borderRadius: 4,
      borderSkipped: false,
      backgroundColor: "rgba(255,255,255,.8)",
      data: {!! json_encode($grafikPermintaan) !!},
      maxBarThickness: 6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
    legend: { display: false },
    tooltip: {
        callbacks: {
        label: function(context) {
            return "Permintaan Masuk: " + context.parsed.y + " data";
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
        ticks: {
          color: "#fff",
          padding: 10,
          font: {
            size: 14,
            weight: 300,
            family: "Roboto"
          }
        }
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
        ticks: {
          color: "#f8f9fa",
          padding: 10,
          font: {
            size: 14,
            weight: 300,
            family: "Roboto"
          }
        }
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
    labels: {!! json_encode($days) !!},
    datasets: [{
      label: "Total Stok Dikirim",
      tension: 0,
      pointRadius: 5,
      pointBackgroundColor: "rgba(255,255,255,.8)",
      pointBorderColor: "transparent",
      borderColor: "rgba(255,255,255,.8)",
      borderWidth: 4,
      backgroundColor: "transparent",
      fill: true,
      data: {!! json_encode($grafikStokDikirim) !!}
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
    legend: { display: false },
    tooltip: {
        callbacks: {
        label: function(context) {
            return "Stok Dikirim: " + context.parsed.y + " barang";
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
          borderDash: [5,5],
          color: 'rgba(255,255,255,.2)'
        },
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
    labels: {!! json_encode($days) !!},
    datasets: [{
    label: "Pengiriman Tuntas",
    tension: 0,
    pointRadius: 5,
    pointBackgroundColor: "rgba(255,255,255,.8)",
    pointBorderColor: "transparent",
    borderColor: "rgba(255,255,255,.8)",
    borderWidth: 4,
    backgroundColor: "transparent",
    fill: true,
    data: {!! json_encode($grafikPengiriman) !!}
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
    legend: { display: false },
    tooltip: {
        callbacks: {
        label: function(context) {
            return "Pengiriman Tuntas: " + context.parsed.y + " kali";
        }
        }
    }
    },

    interaction: { intersect: false, mode: 'index' },
    scales: {
      y: {
        grid: {
          drawBorder: false,
          borderDash: [5,5],
          color: 'rgba(255,255,255,.2)'
        },
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
