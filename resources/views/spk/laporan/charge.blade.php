@extends('spk.layout.app')

@section('content')

<div class="container-fluid py-4">

    {{-- 1. CARD FILTER --}}
    <div class="card mb-4">
        <div class="card-header pb-0">
            <h6><i class="material-icons text-sm me-1">date_range</i> Filter Laporan Charge</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.charge') }}" method="GET" id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pilih Rentang Waktu</label>
                        <div class="input-group input-group-outline is-filled">
                            <select name="filter_type" id="filterType" class="form-control" onchange="toggleCustomDate()">
                                <option value="bulan_ini" {{ $filterType == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="tri_wulan" {{ $filterType == 'tri_wulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                                <option value="semester" {{ $filterType == 'semester' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="tahun_ini" {{ $filterType == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ $filterType == 'custom' ? 'selected' : '' }}>Atur Tanggal Sendiri</option>
                            </select>
                        </div>
                    </div>

                    {{-- INPUT TANGGAL CUSTOM (Hidden by default) --}}
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ $filterType == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Dari Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 custom-date" style="display: {{ $filterType == 'custom' ? 'block' : 'none' }};">
                        <label class="form-label">Sampai Tanggal</label>
                        <div class="input-group input-group-outline is-filled">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- TOMBOL FILTER & EXPORT --}}
                    <div class="col-md-5 mb-3 d-flex gap-2">
                        <button type="submit" class="btn bg-gradient-primary mb-0 flex-fill">
                            <i class="material-icons text-sm me-1">search</i> Tampil
                        </button>

                        {{-- request()->all() berfungsi membawa parameter tanggal saat tombol export ditekan --}}
                        <a href="{{ route('laporan.charge.pdf', request()->all()) }}" target="_blank" class="btn btn-outline-danger mb-0 flex-fill">
                            <i class="material-icons text-sm me-1">picture_as_pdf</i> PDF
                        </a>

                        <a href="{{ route('laporan.charge.excel', request()->all()) }}" target="_blank" class="btn btn-outline-success mb-0 flex-fill">
                            <i class="material-icons text-sm me-1">table_view</i> Excel
                        </a>
                    </div>
                </div>
            </form>
            <div class="mt-2 text-xs text-secondary">
                <i class="material-icons text-xs me-1">info</i>
                Menampilkan data dari: <b>{{ $startDate->format('d M Y') }}</b> s/d <b>{{ $endDate->format('d M Y') }}</b>
            </div>
        </div>
    </div>

    {{-- 2. CARD REKAP STATISTIK --}}
    <div class="row mb-4">
        {{-- Total Item --}}
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">draw</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Item Charge Dikerjakan</p>
                        <h4 class="mb-0">{{ $totalItem }} <span class="text-sm font-weight-normal">Item</span></h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-xs">Akumulasi jumlah pesanan charge desain</p>
                </div>
            </div>
        </div>

        {{-- Total Nominal --}}
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">payments</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Pendapatan Charge</p>
                        <h4 class="mb-0 text-success">Rp {{ number_format($totalNominal, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-xs">Total uang masuk dari charge desain</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABEL DATA DETAIL --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Detail SPK Charge Desain</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tanggal & No SPK</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>

                                    {{-- Kolom Designer hanya muncul jika Admin yg login --}}
                                    @hasrole('manajemen|admin')
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Designer</th>
                                    @endhasrole

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Keterangan / File</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $item->spk->no_spk }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ $item->spk->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-sm">{{ $item->spk->nama_pelanggan }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $item->spk->no_telepon ?? '-' }}</p>
                                    </td>

                                    @hasrole('manajemen|admin')
                                    <td>
                                        <span class="text-xs font-weight-bold">{{ $item->spk->designer->nama ?? 'Unknown' }}</span>
                                    </td>
                                    @endhasrole

                                    <td>
                                        <span class="text-sm font-weight-bold text-dark">{{ $item->nama_file }}</span><br>
                                        <span class="text-xs text-secondary">{{ $item->catatan != '-' ? $item->catatan : '' }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-gradient-success text-xs">
                                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-secondary text-sm mb-0">Belum ada data charge desain pada periode ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                <div class="card-footer py-3">
                    {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

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
</script>

@endsection
