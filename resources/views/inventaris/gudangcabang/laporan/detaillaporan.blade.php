@extends('inventaris.layouts.app')

@section('title', 'Detail Laporan Penerimaan')

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

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
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
                                    $detail = $item->keterangan;
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
                        <hr>
                            <h5 class="mt-4"><b>Rekap Total Penerimaan Barang</b></h5>

                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light text-center">
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
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('gudangcabang.laporan.index') }}" class="btn btn-secondary">Kembali</a>
                        <a href="{{ route('gudangcabang.laporan.download', [$bulan, $tahun]) }}"
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
