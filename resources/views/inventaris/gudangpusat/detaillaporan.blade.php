@extends('inventaris.layouts.app')

@section('title', 'Detail Laporan Pengiriman')

<style>
    .thead-blue th {
        background-color: #97d4ff !important; /* biru muda */
        color: #0d47a1;
        text-align: center;
    }

    .thead-pink th {
        background-color: #fdbcd2 !important; /* pink muda */
        color: #880e4f;
        text-align: center;
    }

    .btn-filter-custom {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

</style>

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card">
                <div class="card-body">

                    {{-- HEADER LAPORAN --}}
                    <div class="text-center mb-4">
                        <h4 class="mb-1"><b>LAPORAN PENGIRIMAN BARANG</b></h4>
                            <p class="mb-0">
                                @switch($filterPeriode)
                                    @case('hari')
                                        Periode {{ \Carbon\Carbon::parse(request('tanggal_awal'))->translatedFormat('d F Y') }}
                                        s/d {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->translatedFormat('d F Y') }}
                                    @break
                                    @case('bulan')
                                        Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} Tahun {{ (int)$tahun }}
                                    @break
                                    @case('tahun')
                                        Tahun {{ $tahun }}
                                    @break
                                    @case('semua')
                                        Semua Periode
                                    @break
                                @endswitch
                            </p>
                    </div>

                    <hr>

                    <form method="GET">
                        <input type="hidden" name="filter_periode" value="{{ $filterPeriode }}">
                        @if($filterPeriode == 'bulan')
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                        @elseif($filterPeriode == 'tahun')
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                        @endif

                        <div class="row mb-4">

                            <div class="col-md-4">
                                <label class="form-label">Filter Cabang</label>
                                <select name="cabang_id[]" id="filterCabang" class="form-control" multiple>
                                    @foreach($allCabang as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            {{ collect(request('cabang_id'))->contains($cabang->id) ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FILTER BARANG --}}
                            <div class="col-md-4">
                                <label class="form-label">Filter Barang</label>
                                <select name="barang_id[]" id="filterBarang" class="form-control" multiple>
                                    @foreach($semuaBarang as $barang)
                                        <option value="{{ $barang->id }}"
                                            {{ collect(request('barang_id'))->contains($barang->id) ? 'selected' : '' }}>
                                            {{ $barang->nama_bahan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- TANGGAL AWAL --}}
                            {{-- <div class="col-md-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal"
                                    value="{{ request('tanggal_awal') }}"
                                    class="form-control">
                            </div> --}}

                            {{-- TANGGAL AKHIR --}}
                            {{-- <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir"
                                    value="{{ request('tanggal_akhir') }}"
                                    class="form-control">
                            </div> --}}

                            {{-- BUTTON --}}
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

                                    <div class="col-6">
                                        <label class="form-label">Reset</label>
                                        <a href="{{ route('laporan.pengiriman.detail', [$bulan ?? '', $tahun ?? '']) }}?filter_periode={{ $filterPeriode }}"
                                            class="btn btn-outline-secondary btn-filter-custom w-100"
                                            title="Reset Filter">
                                            <i class="material-icons">restart_alt</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <h5 class="mt-4"><b>Memo Pembelian/Pengambilan Bahan/Peralatan</b></h5>

                    <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-blue text-center">
                            <tr>
                                <th style="width:12%">Tanggal</th>
                                <th style="width:12%">Jenis</th>
                                <th>Cabang</th>
                                <th>Barang</th>
                                <th style="width:8%">Qty</th>
                                <th style="width:10%">Satuan</th>
                                {{-- <th>Keterangan</th> --}}
                            </tr>
                        </thead>
                        @php
                    $grouped = collect($transaksi)
                        ->groupBy(function($item){
                            return \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d');
                        });
                    @endphp

                        <tbody>
                    @forelse($grouped as $tanggal => $itemsTanggal)

                        @php
                            $groupJenis = collect($itemsTanggal)->groupBy('jenis');
                            $tanggalRowspan = count($itemsTanggal);
                            $printedTanggal = false;
                        @endphp

                        @foreach($groupJenis as $jenis => $itemsJenis)

                            @php
                                $groupCabang = collect($itemsJenis)->groupBy('cabang');
                                $jenisRowspan = count($itemsJenis);
                                $printedJenis = false;
                            @endphp

                            @foreach($groupCabang as $cabang => $itemsCabang)

                                @php
                                    $cabangRowspan = count($itemsCabang);
                                    $printedCabang = false;
                                @endphp

                                @foreach($itemsCabang as $row)
                                <tr>

                                    {{-- TANGGAL --}}
                                    @if(!$printedTanggal)
                                        <td rowspan="{{ $tanggalRowspan }}" class="text-center align-middle">
                                            {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                                        </td>
                                        @php $printedTanggal = true; @endphp
                                    @endif

                                    {{-- JENIS --}}
                                    @if(!$printedJenis)
                                        <td rowspan="{{ $jenisRowspan }}" class="text-center align-middle">
                    @php $jenisFix = strtolower(trim($jenis)); @endphp

                    @if($jenisFix === 'pengiriman')
                        <span class="badge bg-info">Pengiriman</span>
                    @elseif($jenisFix === 'Pengambilan')
                        <span class="badge bg-pink">Pengambilan</span>
                    @else
                        <span class="badge bg-secondary">{{ $jenis }}</span>
                    @endif

                                        </td>
                                        @php $printedJenis = true; @endphp
                                    @endif

                                    {{-- CABANG --}}
                                    @if(!$printedCabang)
                                        <td rowspan="{{ $cabangRowspan }}" class="align-middle">
                                            {{ $cabang }}
                                        </td>
                                        @php $printedCabang = true; @endphp
                                    @endif

                                    {{-- BARANG --}}
                                    <td>{{ $row['barang'] }}</td>

                                    {{-- QTY --}}
                                    <td class="text-center">{{ $row['qty'] }}</td>

                                    {{-- SATUAN --}}
                                    <td class="text-center">{{ $row['satuan'] }}</td>

                                </tr>
                                @endforeach

                            @endforeach

                        @endforeach

                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Tidak ada transaksi
                        </td>
                    </tr>
                    @endforelse
                    </tbody>

                    </table>
                    </div>
                    <hr>
                    <h5 class="mt-4"><b>Data Jumlah Barang yang dikirim (Per Barang)</b></h5>

                    <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-pink text-center">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            @foreach($semuaCabang as $cabang)
                                <th>{{ $cabang->nama }}</th>
                            @endforeach
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $row)
                            <tr>
                                <td>{{ $row['barang'] }}</td>
                                <td class="text-center">{{ $row['satuan'] }}</td>

                                @foreach($semuaCabang as $cabang)
                                    <td class="text-center">
                                        {{ $row['cabang'][$cabang->id] ?? 0 }}
                                    </td>
                                @endforeach

                                <td class="text-center fw-bold">
                                    {{ $row['total'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>


                {{-- TOMBOL DOWNLOAD --}}
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <a href="{{ route('laporan.pengiriman.index') }}"
                    class="btn btn-secondary">
                        Kembali
                    </a>

                    <div class="d-flex gap-3">
                        @php
                            $query = http_build_query(request()->all());

                            $excelRoute = route('laporan.pengiriman.excel') . '?' . $query;
                            $pdfRoute   = route('laporan.pengiriman.download') . '?' . $query;
                        @endphp
                        {{-- EXCEL --}}
                        <a href="{{ $excelRoute }}"
                        class="btn btn-success px-2 py-1"
                        title="Download Excel">
                            <i class="material-icons fs-1">table_view</i>
                        </a>

                        {{-- PDF --}}
                        <a href="{{ $pdfRoute }}"
                        class="btn btn-danger px-2 py-1"
                        title="Download PDF">
                            <i class="material-icons fs-1">picture_as_pdf</i>
                        </a>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    $('#filterBarang').select2({
        placeholder: "Pilih barang...",
        allowClear: true,
        width: '100%'
    });

    $('#filterCabang').select2({
    placeholder: "Pilih cabang...",
    allowClear: true,
    width: '100%'
});

});
</script>
@endpush

