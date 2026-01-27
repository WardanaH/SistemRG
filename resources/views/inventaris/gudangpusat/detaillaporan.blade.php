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
                                    <th style="width: 15%">Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 10%">Jumlah</th>
                                    <th style="width: 10%">Satuan</th>
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

                                    // Kalau hasil decode gagal
                                    if (!is_array($detail)) {
                                        $detail = [];
                                    }
                                @endphp


                                    <tr>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d-m-Y') }}
                                        </td>

                                        {{-- Nama Barang --}}
                                        <td>
                                            @if(is_array($detail) && count($detail) > 0)
                                                @foreach($detail as $d)
                                                    <div>{{ $d['nama_barang'] ?? '' }}</div>
                                                @endforeach
                                            @endif
                                        </td>

                                        {{-- Jumlah --}}
                                        <td class="text-center">
                                            @if(is_array($detail) && count($detail) > 0)
                                                @foreach($detail as $d)
                                                    <div>{{ $d['jumlah'] ?? '' }}</div>
                                                @endforeach
                                            @endif
                                        </td>

                                        {{-- Satuan --}}
                                        <td class="text-center">
                                            @if(is_array($detail) && count($detail) > 0)
                                                @foreach($detail as $d)
                                                    <div>{{ $d['satuan'] ?? '' }}</div>
                                                @endforeach
                                            @endif
                                        </td>

                                        {{-- Cabang Tujuan --}}
                                        <td>
                                            {{ $item->cabangTujuan->nama ?? '' }}
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Tidak ada data pengiriman
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    {{-- TOMBOL DOWNLOAD --}}
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('laporan.pengiriman.index') }}"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                        <a href="{{ route('laporan.pengiriman.download', [$bulan, $tahun]) }}"
                        class="btn bg-gradient-success">
                            <i class="material-icons text-sm">download</i>
                            Download PDF
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
