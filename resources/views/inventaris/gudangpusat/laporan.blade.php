@extends('inventaris.layouts.app')

@section('title', 'Laporan Pengiriman')
<style>

/* ===== MODERN TABLE ===== */
.table-modern{
    border-collapse: separate;
    border-spacing: 0 10px;
}

.table-modern tbody tr{
    background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,.05);
    transition:.25s;
}

.table-modern tbody tr:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 22px rgba(0,0,0,.08);
}

.table-modern td{
    border-top:none !important;
    padding:20px !important;
}

.table-modern tbody tr td:first-child{
    border-radius:14px 0 0 14px;
}

.table-modern tbody tr td:last-child{
    border-radius:0 14px 14px 0;
}

/* header biar ga pucet */
.table-modern thead th{
    border:none;
    font-size:11px;
    letter-spacing:.6px;
    text-transform:uppercase;
    color:#94a3b8;
    font-weight:700;
}

/* tombol detail biar premium */
.btn-detail{
    border-radius:10px;
    padding:6px 14px;
    font-weight:600;
    transition:.2s;
}

.btn-detail:hover{
    transform:scale(1.05);
}

/* filter section biar lebih soft */
.card-body.border-bottom{
    background:#fbfbfc;
}

</style>
@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12">
            <div class="card my-4">

                {{-- HEADER BIRU --}}
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Laporan Pengiriman Barang
                        </h6>
                    </div>
                </div>

                {{-- FILTER DI BAWAH HEADER --}}
                <div class="card-body px-3 pt-2 pb-2 border-bottom">
                    <form method="GET" class="mb-3">
                        <div class="row g-2 align-items-end">

                            {{-- FILTER PERIODE --}}
                            <div class="col-md-3">
                                <label class="form-label">Filter Periode</label>
                                <select name="filter_periode" class="form-control" id="filterPeriodeSelect">
                                    <option value="hari" {{ ($filterPeriode ?? '') == 'hari' ? 'selected' : '' }}>Hari</option>
                                    <option value="bulan" {{ ($filterPeriode ?? '') == 'bulan' ? 'selected' : '' }}>Bulan</option>
                                    <option value="tahun" {{ ($filterPeriode ?? '') == 'tahun' ? 'selected' : '' }}>Tahun</option>
                                    <option value="semua" {{ ($filterPeriode ?? '') == 'semua' ? 'selected' : '' }}>Semua</option>
                                </select>
                            </div>

                            {{-- HARI --}}
                            <div class="col-md-3" id="divTanggal">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal ?? '' }}">
                            </div>
                            <div class="col-md-3" id="divTanggalAkhir">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir ?? '' }}">
                            </div>

                            {{-- BULAN --}}
                            <div class="col-md-3" id="divBulan">
                                <label class="form-label">Bulan Awal</label>
                                <input type="month" name="bulan_awal" class="form-control" value="{{ ($bulanAwal[0] ?? now()->year) }}-{{ str_pad($bulanAwal[1] ?? now()->month, 2, '0', STR_PAD_LEFT) }}">
                            </div>
                            <div class="col-md-3" id="divBulanAkhir">
                                <label class="form-label">Bulan Akhir</label>
                                <input type="month" name="bulan_akhir" class="form-control" value="{{ ($bulanAkhir[0] ?? now()->year) }}-{{ str_pad($bulanAkhir[1] ?? now()->month, 2, '0', STR_PAD_LEFT) }}">
                            </div>

                            {{-- TAHUN --}}
                            <div class="col-md-2" id="divTahun">
                                <label class="form-label">Tahun Awal</label>
                                <input type="number" name="tahun_awal" class="form-control" value="{{ $tahunAwal ?? now()->year }}">
                            </div>
                            <div class="col-md-2" id="divTahunAkhir">
                                <label class="form-label">Tahun Akhir</label>
                                <input type="number" name="tahun_akhir" class="form-control" value="{{ $tahunAkhir ?? now()->year }}">
                            </div>

                            {{-- BUTTON FILTER --}}
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Filter</label>
                                        <button type="submit"
                                            class="btn bg-gradient-info btn-filter-custom w-100"
                                            title="Filter">
                                            <i class="material-icons">search</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABEL LAPORAN --}}
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">

                        <table class="table table-modern align-items-center">
                            <thead>
                                <tr>
                                    @if($filterPeriode == 'hari')
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tanggal</th>
                                    @elseif($filterPeriode == 'bulan')
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Bulan</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tahun</th>
                                    @elseif($filterPeriode == 'tahun')
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tahun</th>
                                    @else
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">Tanggal Pengiriman</th>
                                    @endif
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($laporan as $row)
                                <tr>
                                    @if($filterPeriode == 'hari')
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-info shadow-info d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">event</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Tanggal Laporan</p>
                                                </div>
                                            </div>
                                        </td>
                                    @elseif($filterPeriode == 'bulan')
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success shadow-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">event</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::create()->month((int) $row->bulan)->translatedFormat('F') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Bulan Laporan</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">calendar_today</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $row->tahun }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Tahun Laporan</p>
                                                </div>
                                            </div>
                                        </td>
                                    @elseif($filterPeriode == 'tahun')
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">calendar_today</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $row->tahun }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Tahun Laporan</p>
                                                </div>
                                            </div>
                                        </td>
                                    @else
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-info d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">event</i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($row->tanggal_pengiriman)->translatedFormat('d F Y') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">Tanggal Pengiriman</p>
                                                </div>
                                            </div>
                                        </td>
                                    @endif

<td class="text-center">
@php
    $query = [];

    switch ($filterPeriode) {
        case 'hari':
            $awal  = $row->tanggal ?? $tanggalAwal ?? now()->format('Y-m-d');
            $akhir = $row->tanggal ?? $tanggalAkhir ?? $awal;
            $query = [
                'filter_periode' => 'hari',
                'tanggal_awal' => $awal,
                'tanggal_akhir' => $akhir
            ];
            break;

        case 'bulan':
            $b = $row->bulan ?? now()->month;
            $t = $row->tahun ?? now()->year;
            $query = [
                'filter_periode' => 'bulan',
                'bulan' => $b,
                'tahun' => $t
            ];
            break;

        case 'tahun':
            $t = $row->tahun ?? now()->year;
            $query = [
                'filter_periode' => 'tahun',
                'tahun' => $t
            ];
            break;

        case 'semua':
            $awal  = $tanggalAwal ?? now()->format('Y-m-d');
            $akhir = $tanggalAkhir ?? now()->format('Y-m-d');
            $query = [
                'filter_periode' => 'semua',
                'tanggal_awal' => $awal,
                'tanggal_akhir' => $akhir
            ];
            break;
    }

    $detailRoute = route('laporan.pengiriman.detail', $query);
@endphp

<a href="{{ $detailRoute }}" class="btn btn-sm bg-gradient-primary btn-detail">
    <i class="material-icons text-sm">visibility</i>
    Detail
</a>

</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Belum ada data laporan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-between align-items-center px-3 mt-3">
                            <div>
                                Menampilkan {{ $laporan->firstItem() }} - {{ $laporan->lastItem() }} dari {{ $laporan->total() }} data
                            </div>
                            <div>
                                {{ $laporan->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filterPeriodeSelect');
    const divTanggal = document.getElementById('divTanggal');
    const divBulan = document.getElementById('divBulan');
    const divTahun = document.getElementById('divTahun');

    function updateVisibility() {
        const val = filterSelect.value;

        divTanggal.style.display = val === 'hari' ? 'block' : 'none';
        document.getElementById('divTanggalAkhir').style.display = val === 'hari' ? 'block' : 'none';

        divBulan.style.display = val === 'bulan' ? 'block' : 'none';
        document.getElementById('divBulanAkhir').style.display = val === 'bulan' ? 'block' : 'none';

        divTahun.style.display = val === 'tahun' ? 'block' : 'none';
        document.getElementById('divTahunAkhir').style.display = val === 'tahun' ? 'block' : 'none';
    }

    filterSelect.addEventListener('change', updateVisibility);
    updateVisibility();
});
</script>
@endsection
