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

    {{-- ==============================
        MEMO PEMBELIAN / PENGAMBILAN
    ============================== --}}
    <h4>Memo Pembelian / Pengambilan Bahan / Peralatan</h4>
    <table class="table">
        <thead class="thead-blue">
            <tr>
                <th style="width:12%">Tanggal</th>
                <th style="width:12%">Jenis</th>
                <th>Cabang</th>
                <th>Barang</th>
                <th style="width:8%">Qty</th>
                <th style="width:10%">Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengiriman as $row)
            <tr>
                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}</td>
                <td class="text-center align-middle">{{ $row['jenis'] }}</td>
                <td class="align-middle">{{ $row['cabang'] }}</td>
                <td>{{ $row['barang'] }}</td>
                <td class="text-center">{{ $row['qty'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ==============================
        REKAP TOTAL PER BARANG
    ============================== --}}
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
            @forelse($rekap as $row)
            <tr>
                <td>{{ $row['barang'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                @foreach($semuaCabang as $cabang)
                    <td class="text-center">{{ $row['cabang'][$cabang->id] ?? 0 }}</td>
                @endforeach
                <td class="text-center bold">{{ $row['total'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 2 + count($semuaCabang) + 1 }}" class="text-center text-muted">Tidak ada rekap</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
