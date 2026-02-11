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

                    <div class="text-center mb-4">
                        <h4 class="mb-1"><b>LAPORAN PENERIMAAN BARANG</b></h4>
                        <p class="mb-0">
                            Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}
                            Tahun {{ $tahun }}
                        </p>
                    </div>

                    <hr>
                    <form method="GET">
                        <div class="row mb-3">

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
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control"
                                    value="{{ request('tanggal_awal') }}">
                            </div>

                            {{-- TANGGAL AKHIR --}}
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control"
                                    value="{{ request('tanggal_akhir') }}">
                            </div>

                            {{-- TOMBOL --}}
                            <div class="col-md-2">
                                <div class="row">

                                    <div class="col-6">
                                        <label class="form-label">Filter</label>
                                        <button type="submit"
                                            class="btn bg-gradient-info btn-filter-custom w-100">
                                            <i class="material-icons">search</i>
                                        </button>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label">Reset</label>
                                        <a href="{{ route('gudangcabang.laporan.detail', [$bulan,$tahun]) }}"
                                            class="btn btn-outline-secondary btn-filter-custom w-100">
                                            <i class="material-icons">restart_alt</i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>

                    <h5 class="mt-4"><b>Rekap Total Penerimaan Barang dari Gudang</b></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-blue">
                            <tr class="text-center">
                                <th style="width: 12%">Tanggal Diterima</th>
                                <th>Nama Barang</th>
                                <th style="width: 10%">Jumlah</th>
                                <th style="width: 10%">Satuan</th>
                                <th>Keterangan</th>
                                <th style="width: 20%">Dari Cabang / Gudang</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($pengiriman as $item)
                                @php
                                    $detail = $item->keterangan_terima;
                                    if (is_string($detail)) $detail = json_decode($detail, true);
                                    if (!is_array($detail)) $detail = [];
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->tanggal_diterima)->format('d-m-Y') }}
                                    </td>

                                    {{-- Nama Barang --}}
                                    <td>
                                        @if(count($detail) > 0)
                                            @foreach($detail as $d)
                                                <div>{{ $d['nama_barang'] ?? '-' }}</div>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Jumlah --}}
                                    <td class="text-center">
                                        @if(count($detail) > 0)
                                            @foreach($detail as $d)
                                                <div>{{ $d['jumlah'] ?? '-' }}</div>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Satuan --}}
                                    <td class="text-center">
                                        @if(count($detail) > 0)
                                            @foreach($detail as $d)
                                                <div>{{ $d['satuan'] ?? '-' }}</div>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Keterangan --}}
                                    <td>
                                        @if(count($detail) > 0)
                                            @foreach($detail as $d)
                                                <div>{{ $d['keterangan'] ?? '-' }}</div>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Dari Cabang / Gudang --}}
                                    <td>
                                        {{ $item->cabangAsal->nama ?? 'Gudang Pusat' }}
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada data penerimaan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                        <hr>
                        <h5 class="mt-4"><b>Rekap Pengambilan Barang</b></h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-pink text-center">
                                    <tr>
                                        <th style="width:12%">Tanggal</th>
                                        <th>Nama Barang</th>
                                        <th style="width:10%">Qty</th>
                                        <th style="width:12%">Satuan</th>
                                        <th>Atas Nama</th>
                                        <th style="width:15%">Ambil Ke</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengambilan ?? [] as $item)
                                        @php
                                            $detail = $item->list_barang;
                                            if (is_string($detail)) $detail = json_decode($detail, true);
                                            if (!is_array($detail)) $detail = [];
                                        @endphp

                                        <tr>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                            </td>

                                            <td>
                                                @foreach($detail as $d)
                                                    <div>{{ $d['nama_barang'] ?? '-' }}</div>
                                                @endforeach
                                            </td>

                                            <td class="text-center">
                                                @foreach($detail as $d)
                                                    <div>{{ $d['jumlah'] ?? '-' }}</div>
                                                @endforeach
                                            </td>

                                            <td class="text-center">
                                                @foreach($detail as $d)
                                                    <div>{{ $d['satuan'] ?? '-' }}</div>
                                                @endforeach
                                            </td>

                                            <td>
                                                @foreach($detail as $d)
                                                    <div>{{ $d['atas_nama'] ?? '-' }}</div>
                                                @endforeach
                                            </td>

                                            <td class="text-center">
                                                {{ $item->ambil_ke }}
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                Tidak ada data pengambilan
                                            </td>
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
                                    <a href="{{ route('gudangcabang.laporan.excel', [$bulan, $tahun]) }}"
                                    class="btn btn-success px-2 py-1"
                                    title="Download Excel">
                                        <i class="material-icons fs-1">table_view</i>
                                    </a>

                                    {{-- PDF --}}
                                    <a href="{{ route('gudangcabang.laporan.download', [$bulan, $tahun]) }}"
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
});
</script>
@endpush
