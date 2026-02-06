@extends('inventaris.layouts.app')

@section('title', 'Detail Laporan Pengiriman')

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
                            Bulan
                            {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }}
                            Tahun {{ $tahun }}
                        </p>
                    </div>

                    <hr>

                    {{-- TABEL LAPORAN --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 12%">Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 10%">Jumlah</th>
                                    <th style="width: 10%">Satuan</th>
                                    <th>Keterangan</th>
                                    <th style="width: 20%">Cabang Tujuan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pengiriman as $item)
                                    @php
                                        $detail = $item->keterangan;

                                        // Kalau bentuknya string (JSON)
                                        if (is_string($detail)) {
                                            $detail = json_decode($detail, true);
                                        }

                                        if (!is_array($detail)) {
                                            $detail = [];
                                        }
                                    @endphp

                                    <tr>
                                        {{-- Tanggal --}}
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d-m-Y') }}
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

                                        {{-- Cabang Tujuan --}}
                                        <td>
                                            {{ $item->cabangTujuan->nama ?? '-' }}
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Tidak ada data pengiriman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                    <hr>
                    <h5 class="mt-4"><b>Rekap Total Pengiriman Per Barang</b></h5>

                    <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light text-center">
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
                            {{-- EXCEL --}}
                            <a href="{{ route('laporan.pengiriman.excel', [$bulan, $tahun]) }}"
                            class="btn btn-success px-2 py-1"
                            title="Download Excel">
                                <i class="material-icons fs-1">table_view</i>
                            </a>

                            {{-- PDF --}}
                            <a href="{{ route('laporan.pengiriman.download', [$bulan, $tahun]) }}"
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
