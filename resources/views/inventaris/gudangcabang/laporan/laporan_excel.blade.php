{{-- =========================
HEADER LAPORAN
========================= --}}
<table width="100%">
    <tr>
        <td colspan="6" align="center" style="font-size:18px; font-weight:bold;">
            LAPORAN TRANSAKSI BARANG
        </td>
    </tr>
    <tr>
        <td colspan="6" align="center">
            Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}
            Tahun {{ $tahun }}
        </td>
    </tr>
</table>

<br>

{{-- =========================
TABEL DETAIL TRANSAKSI
========================= --}}
<table cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse; border:2px solid #000;">
    <thead>
        <tr align="center"
            style="font-weight:bold; background-color:#1e88e5; color:white;">
            <th style="border:1px solid #000;">Tanggal</th>
            <th style="border:1px solid #000;">Jenis</th>
            <th style="border:1px solid #000;">Barang</th>
            <th style="border:1px solid #000;">Qty</th>
            <th style="border:1px solid #000;">Satuan</th>
            <th style="border:1px solid #000;">Cabang</th>
        </tr>
    </thead>
    <tbody>

@php
    $grouped = collect($transaksi)->groupBy(function($item){
        return $item['tanggal'].'_'.$item['jenis'];
    });

    $grandTotal = 0;
@endphp

@forelse($grouped as $group)

    @foreach($group as $index => $item)

        <tr>

            {{-- TANGGAL --}}
            <td align="center" style="border:1px solid #000; font-weight:bold;">
                @if($index == 0)
                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}
                @endif
            </td>

            {{-- JENIS --}}
            <td align="center" style="border:1px solid #000; font-weight:bold;">
                @if($index == 0)
                    {{ strtoupper($item['jenis']) }}
                @endif
            </td>

            {{-- BARANG --}}
            <td style="border:1px solid #000;">
                {{ $item['barang'] }}
            </td>

            {{-- QTY --}}
            <td align="center" style="border:1px solid #000;">
                {{ $item['jumlah'] }}
            </td>

            {{-- SATUAN --}}
            <td align="center" style="border:1px solid #000;">
                {{ $item['satuan'] }}
            </td>

            {{-- CABANG --}}
            <td style="border:1px solid #000;">{{ $item['asal_tujuan'] }}</td>
        </tr>

        @php
            $grandTotal += $item['jumlah'];
        @endphp

    @endforeach

@empty
<tr>
    <td colspan="6" align="center" style="border:1px solid #000;">
        Tidak ada data transaksi
    </td>
</tr>
@endforelse

<tr style="background:#f2f2f2;">
    <td colspan="3" align="right"
        style="border:1px solid #000; font-weight:bold;">
        TOTAL SELURUH BARANG
    </td>

    <td align="center"
        style="border:1px solid #000; font-weight:bold;">
        {{ $grandTotal }}
    </td>

    <td colspan="2" style="border:1px solid #000;"></td>
</tr>

</tbody>

</table>

<br><br>

{{-- =========================
TABEL REKAP
========================= --}}
<table cellpadding="6" cellspacing="0" width="60%"
       style="border-collapse:collapse; border:2px solid #000;">
    <thead>
        <tr align="center"
            style="font-weight:bold; background-color:#f8b0c8;">
            <th style="border:1px solid #000;">Nama Barang</th>
            <th style="border:1px solid #000;">Satuan</th>
            <th style="border:1px solid #000;">Total</th>
        </tr>
    </thead>
    <tbody>

@forelse($rekap as $row)
<tr>
    <td style="border:1px solid #000;">
        {{ $row['barang'] ?? '-' }}
    </td>

    <td align="center" style="border:1px solid #000;">
        {{ $row['satuan'] ?? '-' }}
    </td>

    <td align="center"
        style="border:1px solid #000; font-weight:bold;">
        {{ $row['total'] ?? 0 }}
    </td>
</tr>
@empty
<tr>
    <td colspan="3" align="center" style="border:1px solid #000;">
        Tidak ada data rekap
    </td>
</tr>
@endforelse

    </tbody>
</table>
