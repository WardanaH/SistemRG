<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Barang</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 6px; font-size: 11px; vertical-align: top; }
        .table th { background: #f0f0f0; text-align: center; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .thead-blue th { background-color: #84d2ff; color: #000; text-align: center; }
        .thead-pink th { background-color: #fcbed2; color: #000; text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <h3><b>LAPORAN TRANSAKSI BARANG</b></h3>
    <p>
        Cabang {{ $cabang->nama }} <br>
        Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}
        Tahun {{ $tahun }}
    </p>
</div>

{{-- DETAIL TRANSAKSI --}}
<table class="table">
    <thead class="thead-blue">
        <tr>
            <th style="width: 12%">Tanggal</th>
            <th style="width: 12%">Jenis</th>
            <th>Nama Barang</th>
            <th style="width: 8%">Jumlah</th>
            <th style="width: 8%">Satuan</th>
            <th>Keterangan</th>
            <th style="width: 18%">Asal / Tujuan</th>
        </tr>
    </thead>
    <tbody>
    @php
        $grouped = collect($transaksi)
            ->filter(fn($row) => $row['jumlah'] > 0)
            ->groupBy(fn($item) => \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d'));
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

            @foreach($itemsJenis as $index => $row)
                <tr>
                    {{-- TANGGAL --}}
                    @if(!$printedTanggal)
                        <td class="text-center align-middle" rowspan="{{ $tanggalRowspan }}">
                            {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                        </td>
                        @php $printedTanggal = true; @endphp
                    @endif

                    {{-- JENIS --}}
                    @if(!$printedJenis)
                        <td class="text-center align-middle" rowspan="{{ $jenisRowspan }}">
                            {{ $row['jenis'] }}
                        </td>
                        @php $printedJenis = true; @endphp
                    @endif

                    {{-- BARANG --}}
                    <td>{{ $row['barang'] }}</td>
                    <td class="text-center">{{ $row['jumlah'] }}</td>
                    <td class="text-center">{{ $row['satuan'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td>{{ $row['asal_tujuan'] }}</td>
                </tr>
            @endforeach
        @endforeach
    @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data pada periode ini</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- REKAP TOTAL --}}
<h4 style="text-align:center">Rekap Total Barang</h4>
<table class="table">
    <thead class="thead-pink">
        <tr>
            <th>Nama Barang</th>
            <th style="width: 15%">Satuan</th>
            <th style="width: 20%">Total</th>
        </tr>
    </thead>
    <tbody>
    @forelse($rekap as $row)
        @if($row['total'] > 0)
            <tr>
                <td>{{ $row['barang'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                <td class="text-center bold">{{ $row['total'] }}</td>
            </tr>
        @endif
    @empty
        <tr>
            <td colspan="3" class="text-center">Tidak ada rekap</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
