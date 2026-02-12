{{-- HEADER LAPORAN --}}
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

{{-- DETAIL TRANSAKSI --}}
<table cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse; border:1px solid #000;">
    <thead>
        <tr align="center" style="font-weight:bold;">
            <th style="border:1px solid #000;" bgcolor="#acdcff">Tanggal</th>
            <th style="border:1px solid #000;" bgcolor="#acdcff">Jenis</th>
            <th style="border:1px solid #000;" bgcolor="#acdcff">Barang</th>
            <th style="border:1px solid #000;" bgcolor="#acdcff">Qty</th>
            <th style="border:1px solid #000;" bgcolor="#acdcff">Satuan</th>
            <th style="border:1px solid #000;" bgcolor="#acdcff">Cabang</th>
        </tr>
    </thead>
    <tbody>
@php
    $grouped = collect($transaksi)
        ->filter(fn($row) => $row['jumlah'] > 0)
        ->groupBy(fn($item) => $item['tanggal']);
    $grandTotal = 0;
@endphp

@forelse($grouped as $tanggal => $itemsTanggal)
    @php
        $groupJenis = collect($itemsTanggal)->groupBy('jenis');
        $tanggalRowspan = count($itemsTanggal);
        $printedTanggal = false;
    @endphp

    @foreach($groupJenis as $jenis => $itemsJenis)
        @php
            $jenisRowspan = count($itemsJenis);
            $printedJenis = false;
        @endphp

        @foreach($itemsJenis as $index => $row)
            <tr>
                {{-- TANGGAL --}}
                @if(!$printedTanggal)
                    <td align="center" style="border:1px solid #000; font-weight:bold;" rowspan="{{ $tanggalRowspan }}">
                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                    </td>
                    @php $printedTanggal = true; @endphp
                @endif

                {{-- JENIS --}}
                @if(!$printedJenis)
                    <td align="center" style="border:1px solid #000; font-weight:bold;" rowspan="{{ $jenisRowspan }}">
                        {{ strtoupper($row['jenis']) }}
                    </td>
                    @php $printedJenis = true; @endphp
                @endif

                {{-- BARANG --}}
                <td style="border:1px solid #000;">{{ $row['barang'] }}</td>
                <td align="center" style="border:1px solid #000;">{{ $row['jumlah'] }}</td>
                <td align="center" style="border:1px solid #000;">{{ $row['satuan'] }}</td>
                <td style="border:1px solid #000;">{{ $row['asal_tujuan'] }}</td>
            </tr>
            @php $grandTotal += $row['jumlah']; @endphp
        @endforeach
    @endforeach
@empty
<tr>
    <td colspan="6" align="center" style="border:1px solid #000;">Tidak ada data transaksi</td>
</tr>
@endforelse

<tr style="background:#f2f2f2;">
    <td colspan="3" align="right" style="border:1px solid #000; font-weight:bold;">
        TOTAL SELURUH BARANG
    </td>
    <td align="center" style="border:1px solid #000; font-weight:bold;">
        {{ $grandTotal }}
    </td>
    <td colspan="2" style="border:1px solid #000;"></td>
</tr>
</tbody>
</table>

<br><br>

{{-- TABEL REKAP --}}
<table cellpadding="6" cellspacing="0" width="60%" style="border-collapse:collapse; border:1px solid #000;">
    <thead>
        <tr align="center" style="font-weight:bold;">
            <th style="border:1px solid #000;" bgcolor="#f8b0c8">Nama Barang</th>
            <th style="border:1px solid #000;" bgcolor="#f8b0c8">Satuan</th>
            <th style="border:1px solid #000;" bgcolor="#f8b0c8">Total</th>
        </tr>
    </thead>
    <tbody>
@forelse($rekap as $row)
    @if($row['total'] > 0)
    <tr>
        <td style="border:1px solid #000;">{{ $row['barang'] }}</td>
        <td align="center" style="border:1px solid #000;">{{ $row['satuan'] }}</td>
        <td align="center" style="border:1px solid #000; font-weight:bold;">{{ $row['total'] }}</td>
    </tr>
    @endif
@empty
<tr>
    <td colspan="3" align="center" style="border:1px solid #000;">Tidak ada data rekap</td>
</tr>
@endforelse
    </tbody>
</table>
