<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengiriman</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 8px; font-size: 12px; vertical-align: top; }
        .table th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="header">
        <h3><b>LAPORAN PENGIRIMAN BARANG</b></h3>
        <p>Bulan {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} Tahun {{ $tahun }}</p>
    </div>

    <table class="table">
        <thead class="thead-blue">
            <tr>
                <th style="width: 12%">Tanggal</th>
                <th>Nama Barang</th>
                <th style="width: 10%">Jumlah</th>
                <th style="width: 10%">Satuan</th>
                <th>Keterangan</th>
                <th style="width: 20%">Cabang Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengiriman as $item)
                @php
                    $detail = $item->keterangan;

                    // Pastikan keterangan adalah array
                    if (is_string($detail)) {
                        $detail = json_decode($detail, true);
                    }

                    if (!is_array($detail)) {
                        $detail = [];
                    }
                @endphp

                <tr>
                    <td style="text-align:center">
                        {{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d-m-Y') }}
                    </td>

                    <!-- Nama Barang -->
                    <td>
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['nama_barang'] ?? '-' }}
                                @if(!$loop->last) <br> @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <!-- Jumlah -->
                    <td style="text-align:center">
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['jumlah'] ?? '-' }}
                                @if(!$loop->last) <br> @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <!-- Satuan -->
                    <td style="text-align:center">
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['satuan'] ?? '-' }}
                                @if(!$loop->last) <br> @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <!-- Keterangan -->
                    <td>
                        @if(count($detail) > 0)
                            @foreach($detail as $d)
                                {{ $d['keterangan'] ?? '-' }}
                                @if(!$loop->last) <br> @endif
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <!-- Cabang Tujuan -->
                    <td>
                        {{ $item->cabangTujuan->nama ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>

    <h4 style="text-align:center">Rekap Total Pengiriman Per Barang</h4>

    <table class="table">
        <thead class="thead-pink">
            <tr style="text-align:center">
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
                <td style="text-align:center">{{ $row['satuan'] }}</td>

                @foreach($semuaCabang as $cabang)
                    <td style="text-align:center">
                        {{ $row['cabang'][$cabang->id] ?? 0 }}
                    </td>
                @endforeach

                <td style="text-align:center; font-weight:bold">
                    {{ $row['total'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
<style>
    body { font-family: Arial, sans-serif; }

    .table { width: 100%; border-collapse: collapse; }

    .table th, .table td {
        border: 1px solid #000;
        padding: 8px;
        font-size: 12px;
    }

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
