<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK_{{ $spk->no_spk }}</title>
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
            color: #a90000;
            font-size: 11px;
            -webkit-print-color-adjust: exact;
        }

        .spk-container {
            border: 2px solid #a90000;
            width: 100%;
            height: 96vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* HEADER */
        .header {
            display: flex;
            border-bottom: 2px solid #a90000;
            height: 85px;
        }

        .header-left {
            width: 50%;
            padding: 5px 10px;
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
            width: 70px;
            padding: 5px;
            font-weight: bold;
            border-right: 1px solid #a90000;
            display: flex;
            align-items: center;
            font-size: 10px;
        }

        .header-value {
            flex: 1;
            padding: 5px;
            display: flex;
            align-items: center;
            color: #000;
            font-weight: bold;
        }

        .spk-title-bar {
            background-color: #a90000;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            padding: 4px 0;
            letter-spacing: 1px;
        }

        /* TABEL KONTEN */
        .table-wrapper {
            flex: 1;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table th {
            border-bottom: 1px solid #a90000;
            border-right: 1px solid #a90000;
            padding: 4px;
            text-align: center;
            font-size: 10px;
            background: #f8f8f8;
        }

        .content-table td {
            border-bottom: 1px solid #eee;
            border-right: 1px solid #a90000;
            padding: 4px 6px;
            color: #000;
            vertical-align: top;
            font-size: 11px;
        }

        .content-table th:last-child,
        .content-table td:last-child {
            border-right: none;
        }

        .content-table tbody tr:last-child td {
            border-bottom: 1px solid #a90000;
        }

        /* FOOTER AREA */
        .footer-area {
            display: flex;
            height: 95px;
            border-top: 1px solid #a90000;
        }

        .footer-left {
            width: 25%;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #a90000;
        }

        .staff-box {
            flex: 1;
            border-bottom: 1px solid #a90000;
            padding: 4px;
            font-size: 9px;
        }

        .staff-box:last-child {
            border-bottom: none;
        }

        .staff-label {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }

        .staff-value {
            color: #000;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            margin-top: 2px;
        }

        .footer-center {
            width: 55%;
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
            width: 60px;
            font-weight: bold;
            background: #a90000;
            color: white;
            padding: 2px 4px;
            margin-right: 10px;
            text-align: center;
            font-size: 10px;
            border-radius: 2px;
        }

        .cust-value {
            color: #000;
            font-weight: bold;
            font-size: 12px;
        }

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
            font-size: 9px;
        }

        .ttd-space {
            height: 40px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .spk-container {
                height: 100vh;
                border: 2px solid #a90000;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="spk-container">

        {{-- HEADER --}}
        <div class="header">
            <div class="header-left">
                <div class="logo-text">
                    <div style="font-size: 30px; float: left; margin-right: 10px;">
                        <svg width="35" height="35" viewBox="0 0 24 24" fill="#a90000">
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
                    <div class="header-label">DESIGNER :</div>
                    <div class="header-value">
                        {{ strtoupper($spk->designer->nama ?? '-') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="spk-title-bar">
            SURAT PERINTAH KERJA <span style="font-weight: normal; font-size: 10px; margin-left: 10px;">NO SPK :</span> <span style="color: yellow; font-size: 14px;">{{ $spk->no_spk }}</span>
        </div>

        {{-- TABEL ITEM (JENIS & OPERATOR ADA DI SINI) --}}
        <div class="table-wrapper">
            <table class="content-table">
                <thead>
                    <tr>
                        <th width="25%">NAMA FILE</th>
                        <th width="13%">UKURAN</th>
                        <th width="12%">BAHAN</th>
                        <th width="5%">QTY</th>
                        <th width="10%">FINISHING</th>
                        <th width="10%">KET</th>
                        <th width="10%">JENIS</th>
                        <th width="15%">OPERATOR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spk->items as $item)
                    <tr>

                        <td>
                            <strong>{{ $item->nama_file }}</strong>
                        </td>
                        <td align="center">{{ $item->p }} x {{ $item->l }} cm</td>
                        <td align="center">{{ $item->bahan->nama_bahan ?? '-' }}</td>
                        <td align="center" style="font-weight: bold; font-size: 12px;">{{ $item->qty }}</td>
                        <td align="center">{{ $item->finishing ?? '-' }}</td>
                        <td align="center">{{ $item->catatan ?? '-' }}</td>
                        <td align="center" style="font-weight: bold; font-size: 10px;">
                            {{ strtoupper($item->jenis_order) }}
                        </td>
                        <td align="center" style="font-size: 10px; font-weight: bold;">
                            {{ strtoupper($item->operator->nama ?? '-') }}
                        </td>
                    </tr>
                    @endforeach

                    {{-- Baris kosong pelengkap --}}
                    @for($i = 0; $i < max(0, 6 - count($spk->items)); $i++)
                        <tr>
                            <td style="color:transparent;">.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="footer-area">
            <div class="footer-left">
                <div class="staff-box" style="border-bottom: none; height: 100%;">
                    <span class="staff-label" style="text-align: center; margin-top: 5px;">DESIGNER</span>
                    <div class="staff-value" style="margin-top: 15px;">
                        {{ strtoupper($spk->designer->nama ?? '-') }}
                    </div>
                </div>
            </div>

            <div class="footer-center">
                <div class="cust-box">
                    <div class="cust-label">NAMA</div>
                    <div class="cust-value">{{ strtoupper($spk->nama_pelanggan) }}</div>
                </div>
                <div class="cust-box">
                    <div class="cust-label">NO TELP.</div>
                    <div class="cust-value">{{ $spk->no_telepon ?? '-' }}</div>
                </div>
            </div>

            <div class="footer-right">
                <div class="ttd-title">TTD ACC CETAK</div>
                <div class="ttd-space"></div>
            </div>
        </div>

    </div>

</body>

</html>
