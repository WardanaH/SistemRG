@extends('spk.layout.app')

@section('content')

{{-- ALERT SUCCESS (Opsional, kalau ada aksi dari controller) --}}
@if(session('success'))
<div class="alert alert-success text-white alert-dismissible fade show" role="alert">
    <span class="alert-icon align-middle"><i class="material-icons text-md">thumb_up</i></span>
    <span class="alert-text"><strong>Berhasil!</strong> {{ session('success') }}</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="container-fluid py-4">

    {{-- 1. CARD FILTER --}}
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6><i class="material-icons text-sm me-1">date_range</i> Filter Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" id="filterForm">
                <div class="row align-items-end">

                    {{-- Pilihan Rentang Waktu --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pilih Rentang Waktu</label>
                        <div class="input-group input-group-outline is-filled">
                            <select name="rentang" id="filterType" class="form-control" onchange="toggleCustomDate()">
                                <option value="bulan_ini" {{ request('rentang') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="3_bulan" {{ request('rentang') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                                <option value="6_bulan" {{ request('rentang') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="tahun_ini" {{ request('rentang') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ request('rentang') == 'custom' ? 'selected' : '' }}>Atur Tanggal Sendiri</option>
                            </select>
                        </div>
                    </div>

                    {{-- INPUT TANGGAL CUSTOM (Hidden by default jika bukan custom) --}}
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ request('rentang') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Dari Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ request('rentang') == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Sampai Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                    </div>

                    {{-- Tombol Tampil --}}
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn bg-gradient-primary w-100 mb-0">
                            <i class="material-icons text-sm me-1">search</i> Tampil
                        </button>
                    </div>

                </div>
            </form>

            <div class="mt-2 text-xs text-secondary">
                <i class="material-icons text-xs me-1">info</i>
                Menampilkan data dari: <b>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</b> s/d <b>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</b>
            </div>
        </div>
    </div>

    {{-- 2. DAFTAR TABEL LAPORAN BAHAN BAKU PER CABANG --}}
    <div class="row">
        @forelse($dataLaporan as $namaCabang => $itemsCabang)
            {{-- Menggunakan col-lg-6 agar 1 baris berisi 2 card cabang --}}
            <div class="col-lg-6 col-md-12 mb-4">
                {{-- Tambahkan h-100 agar tinggi card sejajar meski isinya beda banyak --}}
                <div class="card h-100 shadow-sm">
                    <div class="card-header p-3 bg-gradient-dark d-flex justify-content-between align-items-center">
                        <div class="text-white">
                            <h6 class="text-white mb-0">
                                <i class="material-icons text-sm me-2">store</i>
                                Data Penggunaan - {{ strtoupper($namaCabang) }}
                            </h6>
                            <span class="text-xs opacity-8">Rekapitulasi material cabang ini</span>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" width="5%">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Bahan Baku</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Penggunaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($itemsCabang as $index => $item)
                                    <tr>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-sm font-weight-bold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm font-weight-bold">{{ $item->bahan->nama_bahan ?? 'Bahan Tidak Diketahui / Dihapus' }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{-- Menampilkan Meter Persegi jika ada --}}
                                            @if($item->total_meter > 0)
                                                <span class="badge badge-sm bg-gradient-info me-2">
                                                    {{ number_format($item->total_meter, 2, ',', '.') }} m²
                                                </span>
                                            @endif

                                            {{-- Jika ada meter dan pcs bersamaan --}}
                                            @if($item->total_meter > 0 && $item->total_pcs > 0)
                                                <span class="text-xs text-secondary font-weight-bold me-2">+</span>
                                            @endif

                                            {{-- Menampilkan Pcs jika ada --}}
                                            @if($item->total_pcs > 0)
                                                <span class="badge badge-sm bg-gradient-success">
                                                    {{ $item->total_pcs }} pcs
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="d-flex flex-column align-items-center text-secondary">
                            <i class="material-icons mb-2" style="font-size: 48px;">inventory_2</i>
                            <h6 class="text-secondary">Tidak ada data penggunaan bahan baku pada periode ini.</h6>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

{{-- SCRIPT TOGGLE FILTER --}}
<script>
    function toggleCustomDate() {
        var type = document.getElementById('filterType').value;
        var customInputs = document.querySelectorAll('.custom-date');

        if (type === 'custom') {
            customInputs.forEach(el => el.style.display = 'block');
        } else {
            customInputs.forEach(el => el.style.display = 'none');
        }
    }

    // Jalankan sekali saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        toggleCustomDate();
    });
</script>

@endsection
