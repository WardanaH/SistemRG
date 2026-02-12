@extends('inventaris.layouts.app')

@section('title', 'Laporan Penerimaan Barang')

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

.table-modern thead th{
    border:none;
    font-size:11px;
    letter-spacing:.6px;
    text-transform:uppercase;
    color:#94a3b8;
    font-weight:700;
}

.btn-detail{
    border-radius:10px;
    padding:6px 14px;
    font-weight:600;
    transition:.2s;
}

.btn-detail:hover{
    transform:scale(1.05);
}

.card-body.border-bottom{
    background:#fbfbfc;
}
</style>

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12">
            <div class="card my-4">

                {{-- HEADER --}}
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Laporan Penerimaan Barang - {{ $cabang->nama }}
                        </h6>
                    </div>
                </div>

                {{-- FILTER --}}
                <div class="card-body px-3 pt-2 pb-2 border-bottom">
                    <form method="GET" class="mb-3">
                        <div class="row g-2 align-items-end">

                            {{-- Filter Periode --}}
                            <div class="col-md-3">
                                <label class="form-label">Filter Periode</label>
                                <select name="filter_periode" id="filterPeriodeSelect" class="form-control">
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
                                <input type="month" name="bulan_awal" class="form-control"
                                    value="{{ ($bulanAwal[0] ?? now()->year) }}-{{ str_pad($bulanAwal[1] ?? now()->month, 2, '0', STR_PAD_LEFT) }}">
                            </div>
                            <div class="col-md-3" id="divBulanAkhir">
                                <label class="form-label">Bulan Akhir</label>
                                <input type="month" name="bulan_akhir" class="form-control"
                                    value="{{ ($bulanAkhir[0] ?? now()->year) }}-{{ str_pad($bulanAkhir[1] ?? now()->month, 2, '0', STR_PAD_LEFT) }}">
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

                {{-- TABLE --}}
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table table-modern align-items-center">
                            <thead>
                                <tr>
                                    @if($filterPeriode == 'hari')
                                        <th>Tanggal</th>
                                    @elseif($filterPeriode == 'bulan')
                                        <th>Bulan</th>
                                        <th>Tahun</th>
                                    @elseif($filterPeriode == 'tahun')
                                        <th>Tahun</th>
                                    @else
                                        <th>Tanggal Diterima</th>
                                    @endif
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $row)
                                    <tr>
                                        @if($filterPeriode == 'hari')
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">event</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ \Carbon\Carbon::parse($row->tanggal ?? now())->translatedFormat('d F Y') }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">Tanggal Laporan</p>
                                                    </div>
                                                </div>
                                            </td>
                                        @elseif($filterPeriode == 'bulan')
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">calendar_month</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ \Carbon\Carbon::create($row->tahun, $row->bulan, 1)->translatedFormat('F') }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">Bulan Laporan</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">calendar_today</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ $row->tahun }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">Tahun Laporan</p>
                                                    </div>
                                                </div>
                                            </td>
                                        @elseif($filterPeriode == 'tahun')
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">calendar_today</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ $row->tahun }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">Tahun Laporan</p>
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="avatar avatar-sm me-3 border-radius-md bg-gradient-success d-flex align-items-center justify-content-center">
                                                        <i class="material-icons text-white text-sm">event</i>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ \Carbon\Carbon::parse($row->tanggal_diterima ?? now())->translatedFormat('d F Y') }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">Tanggal Diterima</p>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif

                                        <td class="text-center">
                                            @php
                                                $query = [];
                                                switch($filterPeriode){
                                                    case 'hari':
                                                        $awal  = $row->tanggal ?? $tanggalAwal ?? now()->format('Y-m-d');
                                                        $akhir = $row->tanggal ?? $tanggalAkhir ?? $awal;
                                                        $query = [
                                                            'filter_periode'=>'hari',
                                                            'tanggal_awal'=>$awal,
                                                            'tanggal_akhir'=>$akhir
                                                        ];
                                                    break;
                                                    case 'bulan':
                                                        $query = [
                                                            'filter_periode'=>'bulan',
                                                            'bulan'=>$row->bulan,
                                                            'tahun'=>$row->tahun
                                                        ];
                                                    break;
                                                    case 'tahun':
                                                        $query = [
                                                            'filter_periode'=>'tahun',
                                                            'tahun'=>$row->tahun
                                                        ];
                                                    break;
                                                    default:
                                                        $query = ['filter_periode'=>'semua'];
                                                }
                                            @endphp
                                            <a href="{{ route('gudangcabang.laporan.detail', $query) }}"
                                               class="btn btn-sm bg-gradient-primary btn-detail">
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

                        {{-- PAGINATION --}}
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

{{-- SCRIPT FILTER --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filter = document.getElementById('filterPeriodeSelect');
    const divTanggal = document.getElementById('divTanggal');
    const divTanggalAkhir = document.getElementById('divTanggalAkhir');
    const divBulan = document.getElementById('divBulan');
    const divBulanAkhir = document.getElementById('divBulanAkhir');
    const divTahun = document.getElementById('divTahun');
    const divTahunAkhir = document.getElementById('divTahunAkhir');

    function toggle(){
        let v = filter.value;
        divTanggal.style.display = v==='hari'?'block':'none';
        divTanggalAkhir.style.display = v==='hari'?'block':'none';
        divBulan.style.display = v==='bulan'?'block':'none';
        divBulanAkhir.style.display = v==='bulan'?'block':'none';
        divTahun.style.display = v==='tahun'?'block':'none';
        divTahunAkhir.style.display = v==='tahun'?'block':'none';
    }

    filter.addEventListener('change',toggle);
    toggle();
});
</script>

@endsection
