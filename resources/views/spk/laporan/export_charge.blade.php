<!DOCTYPE html>
<html>
<head>
    <title>Laporan Charge Desain</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h3>LAPORAN PENDAPATAN CHARGE DESAIN</h3>
        <p>Periode: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">No SPK</th>
                <th width="20%">Pelanggan</th>
                <th width="15%">Designer</th>
                <th width="15%">Keterangan File</th>
                <th class="text-right" width="15%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->spk->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->spk->no_spk }}</td>
                <td>{{ $item->spk->nama_pelanggan }}</td>
                <td>{{ $item->spk->designer->nama ?? '-' }}</td>
                <td>{{ $item->nama_file }}</td>
                <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data charge pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary">
                <td colspan="6" class="text-right">TOTAL ITEM DIKERJAKAN</td>
                <td class="text-right">{{ $totalItem }} Item</td>
            </tr>
            <tr class="summary">
                <td colspan="6" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align: right; font-style: italic;">
        Dicetak pada: {{ now()->format('d M Y H:i') }} <br>
        Oleh: {{ Auth::user()->nama }}
    </p>

</body>
</html>
