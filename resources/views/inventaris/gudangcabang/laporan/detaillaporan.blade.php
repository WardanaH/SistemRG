@extends('inventaris.layouts.app')

@section('title', 'Detail Laporan Penerimaan')
<style>
    .thead-blue th {
        background-color: #97d4ff !important;
        color: #0d47a1;
        text-align: center;
    }

    .thead-pink th {
        background-color: #fdbcd2 !important;
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

                    {{-- Judul sesuai filter --}}
<div class="text-center mb-4">
    <h4 class="mb-1"><b>LAPORAN PENERIMAAN BARANG</b></h4>
    <p class="mb-0">
        @if($filterPeriode == 'hari')
            Tanggal {{ \Carbon\Carbon::parse($tanggalAwal ?? now())->translatedFormat('d F Y') }}
            @if($tanggalAkhir && $tanggalAwal != $tanggalAkhir)
                s/d {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}
            @endif
        @elseif($filterPeriode == 'bulan')
            Bulan {{ \Carbon\Carbon::create($tahun ?? now()->year, $bulan ?? now()->month, 1)->translatedFormat('F') }}
            Tahun {{ $tahun ?? now()->year }}
        @elseif($filterPeriode == 'tahun')
            Tahun {{ $tahun ?? now()->year }}
        @else
            Semua Periode
        @endif
    </p>
</div>

<hr>

{{-- FILTER --}}
<form method="GET">
    <div class="row mb-3">

        {{-- Filter Barang --}}
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

        {{-- Tanggal --}}
        <div class="col-md-3">
            <label class="form-label">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
        </div>

        {{-- Tombol Filter & Reset --}}
        <div class="col-md-2">
            <div class="row">
                <div class="col-6">
                    <label class="form-label">Filter</label>
                    <button type="submit" class="btn bg-gradient-info btn-filter-custom w-100">
                        <i class="material-icons">search</i>
                    </button>
                </div>
                <div class="col-6">
                    <label class="form-label">Reset</label>
                    <a href="{{ route('gudangcabang.laporan.detail', request()->except('barang_id', 'tanggal_awal','tanggal_akhir')) }}"
                        class="btn btn-outline-secondary btn-filter-custom w-100">
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
                        <tbody>
@php
    $grouped = collect($transaksi)->groupBy(function($item){
        return \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d');
    });
@endphp

@forelse($grouped as $tanggal => $itemsTanggal)
    @php
        $groupJenis = collect($itemsTanggal)->groupBy('jenis');
        $tanggalRowspan = count($itemsTanggal);
        $printedTanggal = false;
    @endphp

    @foreach($groupJenis as $jenis => $itemsJenis)
        @php
            $jenisRowspan = count($itemsJenis);
            $printedJenis = false;
        @endphp

        @foreach($itemsJenis as $row)
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
            <td class="align-middle">{{ $row['cabang'] }}</td>

            {{-- BARANG --}}
            <td>{{ $row['barang'] }}</td>

            {{-- QTY --}}
            <td class="text-center">{{ $row['qty'] }}</td>

            {{-- SATUAN --}}
            <td class="text-center">{{ $row['satuan'] }}</td>

            {{-- KETERANGAN --}}
            {{-- <td>{{ $row['ket'] }}</td> --}}

        </tr>
        @endforeach
    @endforeach
@empty
<tr>
    <td colspan="7" class="text-center text-muted">Tidak ada transaksi</td>
</tr>
@endforelse
                        </tbody>
                    </table>
                    </div>


                        <hr>

                            <h5 class="mt-4"><b>Data Penerimaan Barang</b></h5>

                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-pink text-center">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Total Diterima</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekap as $row)
                                    <tr>
                                        <td>{{ $row['barang'] }}</td>
                                        <td class="text-center">{{ $row['satuan'] }}</td>
                                        <td class="text-center fw-bold">{{ $row['total'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('gudangcabang.laporan.index') }}" class="btn btn-secondary">Kembali</a>
                                <div class="d-flex gap-3">
                                    {{-- EXCEL --}}
                                    <button type="button"
                                        class="btn btn-success px-2 py-1 btn-download"
                                        data-type="excel">
                                        <i class="material-icons fs-1">table_view</i>
                                    </button>

                                    {{-- PDF --}}
                                    <button type="button"
                                        class="btn btn-danger px-2 py-1 btn-download"
                                        data-type="pdf">
                                        <i class="material-icons fs-1">picture_as_pdf</i>
                                    </button>
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
});
</script>
<script>
$(document).on('click', '.btn-download', function(e){
    e.preventDefault();

    let type = $(this).data('type');

    let basePdf  = "{{ route('gudangcabang.laporan.download') }}";
    let baseExcel = "{{ route('gudangcabang.laporan.excel') }}";

    // langsung pilih URL sesuai type
    let downloadUrl = (type === 'pdf') ? basePdf + "?mode=full" : baseExcel + "?mode=full";

    Swal.fire({
        title: 'Sedang menyiapkan laporan...',
        text: 'Klik OK untuk download laporan.',
        icon: 'info',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = downloadUrl;
        }
    });

});
</script>


@endpush
