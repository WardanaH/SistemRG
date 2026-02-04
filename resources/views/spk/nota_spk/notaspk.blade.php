<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK_{{ $spk->no_spk }}</title>
    <style>
        @page {
            size: A5 landscape;
            /* Menyesuaikan ukuran kertas fisik */
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: #fff;
            color: #a90000;
            /* Warna Merah Khas SPK */
            font-size: 12px;
            -webkit-print-color-adjust: exact;
            /* Agar warna background tercetak */
        }

        /* Container Utama */
        .spk-container {
            border: 2px solid #a90000;
            width: 100%;
            height: 95vh;
            /* Full height kertas */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* HEADER */
        .header {
            display: flex;
            border-bottom: 2px solid #a90000;
            height: 100px;
        }

        /* Logo & Nama (Kiri) */
        .header-left {
            width: 50%;
            padding: 10px;
            display: flex;
            align-items: center;
            border-right: 1px solid #a90000;
        }

        .logo-text h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1;
        }

        .logo-text p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #000;
        }

        /* Info SPK (Kanan) */
        .header-right {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        .header-row {
            display: flex;
            border-bottom: 1px solid #a90000;
            flex: 1;
        }

        .header-row:last-child {
            border-bottom: none;
        }

        .header-label {
            width: 80px;
            padding: 5px;
            font-weight: bold;
            border-right: 1px solid #a90000;
            display: flex;
            align-items: center;
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
            color: #a90000;
            font-weight: bold;
        }

        .box {
            width: 12px;
            height: 12px;
            border: 1px solid #a90000;
            margin-right: 3px;
            display: inline-block;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
        }

        /* JUDUL BESAR DI SAMPING KANAN (Vertical Text style di fisik asli biasanya, tapi kita buat horizontal agar rapi di web) */
        .spk-title-bar {
            background-color: #a90000;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 5px 0;
            letter-spacing: 2px;
        }

        /* TABEL KONTEN */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            flex: 1;
        }

        .content-table th {
            border-bottom: 1px solid #a90000;
            border-right: 1px solid #a90000;
            padding: 5px;
            text-align: center;
            font-size: 11px;
        }

        .content-table td {
            border-bottom: 1px solid #a90000;
            border-right: 1px solid #a90000;
            padding: 5px;
            color: #000;
            vertical-align: top;
            height: 150px;
            /* Tinggi minimum baris */
        }

        .content-table th:last-child,
        .content-table td:last-child {
            border-right: none;
        }

        /* FOOTER AREA */
        .footer-area {
            display: flex;
            height: 120px;
            border-top: 2px solid #a90000;
        }

        /* Kotak Kiri (Designer/Operator) */
        .footer-left {
            width: 20%;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #a90000;
        }

        .staff-box {
            flex: 1;
            border-bottom: 1px solid #a90000;
            padding: 5px;
            font-size: 10px;
            position: relative;
        }

        .staff-box:last-child {
            border-bottom: none;
        }

        .staff-label {
            font-weight: bold;
            display: block;
            margin-bottom: 15px;
        }

        .staff-value {
            color: #000;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
        }

        /* Kotak Tengah (Nama/Telp) */
        .footer-center {
            width: 60%;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #a90000;
        }

        .cust-box {
            flex: 1;
            border-bottom: 1px solid #a90000;
            padding: 5px;
            display: flex;
            align-items: center;
        }

        .cust-box:last-child {
            border-bottom: none;
        }

        .cust-label {
            width: 80px;
            font-weight: bold;
            background: #a90000;
            color: white;
            padding: 2px 5px;
            margin-right: 10px;
            text-align: center;
        }

        .cust-value {
            color: #000;
            font-weight: bold;
            font-size: 14px;
        }

        /* Kotak Kanan (TTD) */
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

        .ttd-note {
            font-size: 9px;
            color: #000;
        }

        /* Print Settings */
        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .spk-container {
                border: 2px solid #a90000 !important;
                height: 100vh;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="spk-container">

        <div class="header">
            <div class="header-left">
                <div class="logo-text">
                    <div style="font-size: 30px; float: left; margin-right: 10px;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="#a90000">
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
                <div class="header-row">
                    <div class="header-label">ORDER :</div>
                    <div class="header-value">
                        <div class="checkbox-group">
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'outdoor' ? '✔' : '' }}</div> OUTDOOR
                            </div>
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'indoor' ? '✔' : '' }}</div> INDOOR
                            </div>
                            <div class="cb-item">
                                <div class="box">{{ $spk->jenis_order_spk == 'multi' ? '✔' : '' }}</div> MULTI
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="spk-title-bar">
            SURAT PERINTAH KERJA <span style="font-weight: normal; font-size: 12px; margin-left: 20px;">DIGITAL PRINTING BANJARBARU</span> &nbsp;&nbsp;&nbsp; NO SPK : <span style="color: yellow;">{{ $spk->no_spk }}</span>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <th width="30%">NAMA FILE</th>
                    <th width="15%">UKURAN (PxL)</th>
                    <th width="15%">BAHAN</th>
                    <th width="5%">QTY</th>
                    <th width="15%">FINISHING</th>
                    <th width="20%">CATATAN / KET</th>
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
                <div class="staff-box">
                    <span class="staff-label">DESIGNER</span>
                    <div class="staff-value">{{ strtoupper($spk->designer->nama ?? '-') }}</div>
                </div>
                <div class="staff-box">
                    <span class="staff-label">OPERATOR</span>
                    <div class="staff-value">{{ strtoupper($spk->operator->nama ?? '-') }}</div>
                </div>
            </div>

            <div class="footer-center">
                <div class="cust-box">
                    <div class="cust-label">NAMA</div>
                    <div class="cust-value">{{ strtoupper($spk->nama_pelanggan) }}</div>
                </div>
                <div class="cust-box">
                    <div class="cust-label">NO TELP.</div>
                    <div class="cust-value">{{ $spk->no_telepon }}</div>
                </div>
            </div>

            <div class="footer-right">
                <div class="ttd-title">TTD ACC CETAK</div>
                <div class="ttd-space"></div>
                <div class="ttd-note"></div>
            </div>
        </div>

    </div>

</body>

</html>
