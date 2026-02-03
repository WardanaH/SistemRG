<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK_BANTUAN_{{ $spk->no_spk }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #fff;
            color: #000;
            /* Default hitam agar kontras */
            font-size: 12px;
            -webkit-print-color-adjust: exact;
        }

        /* Container Utama */
        .spk-container {
            border: 2px solid #333;
            /* Warna Gelap untuk Bantuan */
            width: 100%;
            height: 95vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* HEADER */
        .header {
            display: flex;
            border-bottom: 2px solid #333;
            height: 100px;
        }

        /* Logo & Nama (Kiri) */
        .header-left {
            width: 50%;
            padding: 10px;
            display: flex;
            align-items: center;
            border-right: 1px solid #333;
        }

        .logo-text h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
            color: #333;
        }

        .logo-text p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #555;
        }

        /* Info SPK (Kanan) */
        .header-right {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        .header-row {
            display: flex;
            border-bottom: 1px solid #333;
            flex: 1;
        }

        .header-row:last-child {
            border-bottom: none;
        }

        .header-label {
            width: 90px;
            padding: 5px;
            font-weight: bold;
            border-right: 1px solid #333;
            display: flex;
            align-items: center;
            background-color: #f0f0f0;
        }

        .header-value {
            flex: 1;
            padding: 5px;
            display: flex;
            align-items: center;
            color: #000;
            font-weight: bold;
        }

        /* Checkbox Style */
        .checkbox-group {
            display: flex;
            gap: 10px;
        }

        .cb-item {
            display: flex;
            align-items: center;
            font-size: 10px;
            color: #333;
            font-weight: bold;
        }

        .box {
            width: 12px;
            height: 12px;
            border: 1px solid #333;
            margin-right: 3px;
            display: inline-block;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
        }

        /* JUDUL BESAR SPK BANTUAN */
        .spk-title-bar {
            background-color: #333;
            /* Hitam/Gelap */
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            padding: 8px 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* TABEL KONTEN */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            flex: 1;
        }

        .content-table th {
            border-bottom: 1px solid #333;
            border-right: 1px solid #333;
            padding: 5px;
            text-align: center;
            font-size: 11px;
            background-color: #eee;
        }

        .content-table td {
            border-bottom: 1px solid #333;
            border-right: 1px solid #333;
            padding: 5px;
            color: #000;
            vertical-align: top;
            height: 150px;
        }

        .content-table th:last-child,
        .content-table td:last-child {
            border-right: none;
        }

        /* FOOTER AREA */
        .footer-area {
            display: flex;
            height: 120px;
            border-top: 2px solid #333;
        }

        /* Footer Kiri */
        .footer-left {
            width: 25%;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #333;
        }

        .staff-box {
            flex: 1;
            border-bottom: 1px solid #333;
            padding: 5px;
            font-size: 10px;
        }

        .staff-box:last-child {
            border-bottom: none;
        }

        .staff-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .staff-value {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
        }

        /* Footer Tengah (Nama/Telp) */
        .footer-center {
            width: 55%;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #333;
        }

        .cust-box {
            flex: 1;
            border-bottom: 1px solid #333;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .cust-box:last-child {
            border-bottom: none;
        }

        .cust-label {
            width: 100px;
            font-weight: bold;
            background: #333;
            color: white;
            padding: 2px 5px;
            margin-right: 10px;
            text-align: center;
            font-size: 11px;
        }

        .cust-value {
            font-weight: bold;
            font-size: 14px;
        }

        /* Footer Kanan (TTD) */
        .footer-right {
            width: 20%;
            text-align: center;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .ttd-title {
            font-weight: bold;
            font-size: 10px;
        }

        .ttd-space {
            height: 60px;
        }

        @media print {
            .spk-container {
                border: 2px solid #333 !important;
                height: 100vh;
            }

            .header,
            .footer-area {
                border-color: #333 !important;
            }

            /* Memaksa background tercetak */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="spk-container">

        <div class="header">
            <div class="header-left">
                <div class="logo-text">
                    {{-- LOGO HITAM / ABU --}}
                    <div style="font-size: 30px; float: left; margin-right: 10px;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="#333">
                            <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z" />
                        </svg>
                    </div>
                    <h1>RESTU GURU PROMOSINDO</h1>
                    <p>DIGITAL PRINTING / ADVERTISING</p>
                </div>
            </div>

            <div class="header-right">
                <div class="header-row">
                    <div class="header-label">TANGGAL :</div>
                    <div class="header-value">
                        {{ \Carbon\Carbon::parse($spk->tanggal_spk)->format('d - m - Y') }}
                    </div>
                </div>
                {{-- INFO CABANG PENGIRIM --}}
                <div class="header-row">
                    <div class="header-label">DARI CABANG:</div>
                    <div class="header-value" style="font-size: 14px; text-transform: uppercase;">
                        {{ $spk->cabangAsal->nama ?? 'LUAR KOTA' }}
                    </div>
                </div>
                <div class="header-row">
                    <div class="header-label">ORDER :</div>
                    <div class="header-value">
                        <div class="checkbox-group">
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'outdoor' ? '✔' : '' }}</div> OUT
                            </div>
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'indoor' ? '✔' : '' }}</div> IN
                            </div>
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'multi' ? '✔' : '' }}</div> MUL
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TITLE BAR SPK BANTUAN --}}
        <div class="spk-title-bar">
            SPK BANTUAN <span style="font-weight: normal; font-size: 12px; margin-left: 20px;">(ORDER EKSTERNAL)</span>
            &nbsp;&nbsp;&nbsp;
            NO SPK : <span style="color: yellow; text-decoration: underline;">{{ $spk->no_spk }}</span>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <th width="30%">NAMA FILE</th>
                    <th width="15%">UKURAN (PxL)</th>
                    <th width="15%">BAHAN</th>
                    <th width="5%">QTY</th>
                    <th width="15%">FINISHING</th>
                    <th width="20%">CATATAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $spk->nama_file }}</td>
                    <td align="center">{{ $spk->ukuran_panjang }} x {{ $spk->ukuran_lebar }} cm</td>
                    <td align="center">{{ $spk->bahan->nama_bahan ?? '-' }}</td>
                    <td align="center">{{ $spk->kuantitas }}</td>
                    <td align="center">{{ $spk->finishing }}</td>
                    <td>{{ $spk->keterangan }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer-area">
            <div class="footer-left">
                {{-- Designer di sini adalah orang yg input (Penerima Email) --}}
                <div class="staff-box">
                    <span class="staff-label">PENERIMA ORDER</span>
                    <div class="staff-value">{{ strtoupper($spk->designer->nama ?? '-') }}</div>
                </div>
                <div class="staff-box">
                    <span class="staff-label">OPERATOR CETAK</span>
                    <div class="staff-value">{{ strtoupper($spk->operator->nama ?? '-') }}</div>
                </div>
            </div>

            <div class="footer-center">
                <div class="cust-box">
                    <div class="cust-label">PELANGGAN</div>
                    <div class="cust-value">{{ strtoupper($spk->nama_pelanggan) }}</div>
                </div>
                <div class="cust-box">
                    <div class="cust-label">NO TELP.</div>
                    <div class="cust-value">{{ $spk->no_telepon }}</div>
                </div>
            </div>

            <div class="footer-right">
                <div class="ttd-title">SERAH TERIMA</div>
                <div class="ttd-space"></div>
                <div class="ttd-note" style="font-size: 8px;">*Cek fisik sebelum kirim</div>
            </div>
        </div>

    </div>

</body>

</html>
