<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPK ADV - {{ $spk->no_spk }}</title>
    <style>
        @page {
            size: 210mm 297mm; /* Ukuran A5 Landscape Presisi */
            margin: 0; /* Hilangkan margin browser */
        }

        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 10mm 15mm; /* Padding konten manual */
            color: #333;
            background: white;
            -webkit-print-color-adjust: exact; /* Paksa warna background ter-print */
            print-color-adjust: exact;
        }

        /* 2. WARNA TEMA (HIJAU MUDA SEGAR) */
        :root {
            --primary: #2E7D32; /* Hijau Utama */
            --light: #E8F5E9;   /* Hijau Pudar */
            --dark: #1B5E20;    /* Hijau Gelap */
        }

        /* 3. HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .brand h1 {
            margin: 0;
            font-size: 16pt;
            color: var(--primary);
            text-transform: uppercase;
        }
        .brand p {
            margin: 0;
            font-size: 9pt;
            color: #666;
        }
        .judul-surat {
            background-color: var(--primary);
            color: white;
            padding: 5px 15px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 12pt;
        }

        /* 4. INFO BARIS ATAS */
        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 9pt;
            background-color: var(--light);
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid var(--primary);
        }
        .info-group span {
            font-weight: bold;
            color: var(--dark);
            margin-right: 5px;
        }

        /* 5. TABEL UTAMA (STRUKTUR MANUAL) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid var(--primary);
            padding: 4px 6px;
            font-size: 9pt;
            vertical-align: middle;
        }
        th {
            background-color: var(--light);
            color: var(--dark);
            text-align: center;
            font-weight: bold;
            height: 25px;
        }

        /* Kolom Khusus */
        .col-center { text-align: center; }
        .col-right { text-align: right; }
        .col-dim { width: 40px; text-align: center; } /* Lebar kolom P dan L */

        /* 6. FOOTER TANDA TANGAN */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        .sign-box {
            width: 30%;
            text-align: center;
            border: 1px solid var(--primary);
            border-radius: 4px;
            overflow: hidden;
        }
        .sign-title {
            background-color: var(--primary);
            color: white;
            font-size: 8pt;
            padding: 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sign-space {
            height: 50px; /* Ruang tanda tangan */
        }
        .sign-name {
            font-size: 9pt;
            font-weight: bold;
            padding-bottom: 5px;
            color: var(--dark);
        }

        /* Utility helper */
        .text-muted { color: #777; font-style: italic; font-size: 8pt; }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div class="brand">
            <h1>{{ $spk->cabang->nama }}</h1>
            <p>{{ $spk->cabang->alamat }} | Tel: {{ $spk->cabang->telepon }}</p>
        </div>
        <div class="judul-surat">SPK ADVERTISING</div>
    </div>

    <div class="info-bar">
        <div><span>No. SPK:</span> {{ $spk->no_spk }}</div>
        <div><span>Tanggal:</span> {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d/m/Y H:i') }}</div>
        <div><span>Pelanggan:</span> {{ Str::limit($spk->nama_pelanggan, 20) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2">Nama File / Item</th>
                <th colspan="2">Ukuran (cm)</th>
                <th rowspan="2">Bahan</th>
                <th rowspan="2" width="8%">Qty</th>
                <th rowspan="2">Finishing</th>
                <th rowspan="2">Operator</th>
            </tr>
            <tr>
                <th>P</th>
                <th>L</th>
            </tr>
        </thead>
        <tbody>
            @foreach($spk->items as $i => $item)
            <tr>
                <td class="col-center">{{ $i + 1 }}</td>
                <td>
                    <b>{{ $item->nama_file }}</b>
                    @if($item->catatan && $item->catatan != '-')
                        <br><span class="text-muted">Ket: {{ $item->catatan }}</span>
                    @endif
                </td>
                <td class="col-dim">{{ $item->p + 0 }}</td> {{-- +0 unk menghilangkan desimal .00 jika bulat --}}
                <td class="col-dim">{{ $item->l + 0 }}</td>
                <td class="col-center">{{ $item->bahan->nama_bahan ?? '-' }}</td>
                <td class="col-center" style="font-weight:bold; font-size:11pt;">{{ $item->qty }}</td>
                <td class="col-center">{{ $item->finishing }}</td>
                <td class="col-center" style="font-size:8pt;">{{ $item->operator->nama ?? '-' }}</td>
            </tr>
            @endforeach

            {{-- Baris kosong pelengkap jika item sedikit --}}
            @for($j = 0; $j < (5 - count($spk->items)); $j++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-box">
            <div class="sign-title">Folder File</div>
            <div class="sign-space" style="display:flex; align-items:center; justify-content:center;">
                <span style="font-size:12pt; font-weight:bold;">{{ $spk->folder }}</span>
            </div>
            <div class="sign-name">&nbsp;</div>
        </div>

        <div class="sign-box">
            <div class="sign-title">Admin / Designer</div>
            <div class="sign-space"></div>
            <div class="sign-name">{{ $spk->designer->nama }}</div>
        </div>

        <div class="sign-box">
            <div class="sign-title">Penerima / Produksi</div>
            <div class="sign-space"></div>
            <div class="sign-name">( ............................ )</div>
        </div>
    </div>

</body>
</html>
