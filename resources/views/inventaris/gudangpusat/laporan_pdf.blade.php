<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengiriman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .table th { background-color: #97d4ff; color: #0d47a1; text-align: center; }
        .thead-pink th { background-color: #fdbcd2; color: #880e4f; text-align: center; }
        .text-center { text-align: center; }
        .align-middle { vertical-align: middle; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h3><b>LAPORAN PENGIRIMAN BARANG</b></h3>
    <p>
        @switch($filterPeriode)
            @case('hari')
                Periode {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d F Y') }}
                s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d F Y') }}
            @break
            @case('bulan')
                Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} Tahun {{ $tahun }}
            @break
            @case('tahun')
                Tahun {{ $tahun }}
            @break
            @default
                Semua Periode
        @endswitch
    </p>
</div>

{{-- MEMO PENGIRIMAN --}}
<h4>Memo Pembelian / Pengambilan Bahan / Peralatan</h4>
<table class="table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Jenis</th>
            <th>Cabang</th>
            <th>Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
        </tr>
    </thead>
    <tbody>
    @php
        $grouped = collect($pengiriman)
            ->filter(fn($row) => $row['qty'] > 0) // hanya yang dikirim
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

                        {{-- CABANG --}}
                        @if(!$printedCabang)
                            <td class="align-middle" rowspan="{{ $cabangRowspan }}">
                                {{ $cabang }}
                            </td>
                            @php $printedCabang = true; @endphp
                        @endif

                        {{-- BARANG --}}
                        <td>{{ $row['barang'] }}</td>
                        <td class="text-center">{{ $row['qty'] }}</td>
                        <td class="text-center">{{ $row['satuan'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted">Tidak ada transaksi</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- REKAP --}}
<h4>Data Jumlah Barang yang dikirim (Per Barang)</h4>
<table class="table">
    <thead class="thead-pink">
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
    @php $hasData = false; @endphp
    @foreach($rekap as $row)
        @if($row['total'] > 0)
            @php $hasData = true; @endphp
        <tr>
            <td>{{ $row['barang'] }}</td>
            <td class="text-center">{{ $row['satuan'] }}</td>
            @foreach($semuaCabang as $cabang)
                @php $qtyCabang = $row['cabang'][$cabang->id] ?? 0; @endphp
                <td class="text-center">{{ $qtyCabang > 0 ? $qtyCabang : '' }}</td>
            @endforeach
            <td class="text-center bold">{{ $row['total'] }}</td>
        </tr>
        @endif
    @endforeach
    @if(!$hasData)
        <tr>
            <td colspan="{{ 2 + count($semuaCabang) + 1 }}" class="text-center text-muted">Tidak ada rekap</td>
        </tr>
    @endif
    </tbody>
</table>

</body>
</html>
