<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
        }

        .table th {
            background: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
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

    {{-- ================================
        TABEL DETAIL TRANSAKSI
    ================================= --}}
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
            @forelse($transaksi as $row)
                <tr>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                    </td>

                    <td class="text-center bold">
                        {{ $row['jenis'] }}
                    </td>

                    <td>
                        {{ $row['barang'] }}
                    </td>

                    <td class="text-center">
                        {{ $row['jumlah'] }}
                    </td>

                    <td class="text-center">
                        {{ $row['satuan'] }}
                    </td>

                    <td>
                        {{ $row['keterangan'] }}
                    </td>

                    <td>
                        {{ $row['asal_tujuan'] }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Tidak ada data pada periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br><br>

    {{-- ================================
        REKAP TOTAL
    ================================= --}}
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
            <tr>
                <td>{{ $row['barang'] }}</td>
                <td class="text-center">{{ $row['satuan'] }}</td>
                <td class="text-center bold">
                    {{ $row['total'] }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">
                    Tidak ada rekap
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
