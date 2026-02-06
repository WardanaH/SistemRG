<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penerimaan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 8px; font-size: 12px; }
        .table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h3><b>LAPORAN PENERIMAAN BARANG</b></h3>
        <p>Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} Tahun {{ $tahun }}</p>
    </div>

    <table class="table">
        <thead class="thead-blue">
            <tr>
                <th style="width: 12%">Tanggal Diterima</th>
                <th>Nama Barang</th>
                <th style="width: 10%">Jumlah</th>
                <th style="width: 10%">Satuan</th>
                <th>Keterangan</th>
                <th style="width: 20%">Dari Cabang / Gudang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengiriman as $item)
                @php
                    $detail = $item->keterangan_terima;
                    if (is_string($detail)) $detail = json_decode($detail, true);
                    if (!is_array($detail)) $detail = [];
                @endphp

                <tr>
                    <td style="text-align:center">{{ \Carbon\Carbon::parse($item->tanggal_diterima)->format('d-m-Y') }}</td>

                    {{-- Nama Barang --}}
                    <td>
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['nama_barang'] ?? '-' }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    {{-- Jumlah --}}
                    <td style="text-align:center">
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['jumlah'] ?? '-' }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    {{-- Satuan --}}
                    <td style="text-align:center">
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['satuan'] ?? '-' }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    {{-- Keterangan --}}
                    <td>
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['keterangan'] ?? '-' }}<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    {{-- Dari Cabang / Gudang --}}
                    <td>{{ $item->cabangAsal->nama ?? 'Gudang Pusat' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>
    <h4 style="text-align:center">Rekap Total Penerimaan Barang</h4>

    <table class="table">
        <thead class="thead-pink">
            <tr style="text-align:center">
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Total Diterima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $row)
            <tr>
                <td>{{ $row['barang'] }}</td>
                <td style="text-align:center">{{ $row['satuan'] }}</td>
                <td style="text-align:center; font-weight:bold">
                    {{ $row['total'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
<style>
.thead-blue th {
    background-color: #84d2ff;
    color: #000000;
    text-align: center;
}

.thead-pink th {
    background-color: #fcbed2;
    color: #000000;
    text-align: center;
}
</style>
</html>
