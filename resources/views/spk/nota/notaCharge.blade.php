<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: A5 landscape; margin: 10mm; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px; }
        .content { margin-bottom: 20px; }
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { vertical-align: top; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; text-align: left; }
        .data-table th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 150px; }
        .stamp-box { height: 60px; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0;">NOTA CHARGE DESAIN (CHARGE)</h2>
        <p style="margin:5px 0;">{{ $spk->cabang->nama }}</p>
        <small>{{ $spk->cabang->alamat }} | Tel: {{ $spk->cabang->telepon }}</small>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">No. SPK</td><td width="35%">: <b>{{ $spk->no_spk }}</b></td>
            <td width="15%">Pelanggan</td><td width="35%">: {{ $spk->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td>Tarikh</td><td>: {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y H:i') }}</td>
            <td>Designer</td><td>: {{ $spk->designer->nama }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Keterangan Item / Nama Fail</th>
                <th class="text-right" width="10%">Qty</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($spk->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_file }} ({{ ucfirst($item->jenis_order) }})</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td>{{ $item->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <p><b>Status Pembayaran:</b> {{ strtoupper($spk->status_spk) }}</p>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Designer</p>
            <div class="stamp-box"></div>
            <p>( {{ $spk->designer->nama }} )</p>
        </div>
        <div class="signature">
            <p>Admin/Kasir</p>
            <div class="stamp-box"></div>
            <p>( ................. )</p>
        </div>
        <div class="signature">
            <p>Pelanggan</p>
            <div class="stamp-box"></div>
            <p>( {{ Str::limit($spk->nama_pelanggan, 15) }} )</p>
        </div>
    </div>

    <div style="margin-top: 20px; font-style: italic; font-size: 10px;">
        * Nota ini adalah bukti sah caj reka bentuk. Sila simpan untuk rujukan urusan cetakan.
    </div>
</body>
</html>
