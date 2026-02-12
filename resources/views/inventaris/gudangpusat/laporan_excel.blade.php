<table width="100%" cellpadding="0" cellspacing="0" style="border:none;">
    <tr>
        <td colspan="20" style="text-align:center; font-weight:bold; font-size:16px;">
            LAPORAN PENGIRIMAN BARANG
        </td>
    </tr>
    <tr>
        <td colspan="20" style="text-align:center;">
            @switch($filterPeriode)
                @case('hari')
                    Periode {{ \Carbon\Carbon::parse($tanggal_awal)->translatedFormat('d F Y') }}
                    s/d {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('d F Y') }}
                @break
                @case('bulan')
                    Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} Tahun {{ $tahun }}
                @break
                @case('tahun')
                    Tahun {{ $tahun }}
                @break
                @default
                    Semua Periode
            @endswitch
        </td>
    </tr>
</table>

<br>

{{-- DETAIL PENGIRIMAN --}}
<table cellpadding="6" cellspacing="0"
       style="border-collapse:collapse; width:100%; border:1px solid #000;">
    <thead>
        <tr>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Tanggal</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Jenis</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Cabang Tujuan</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Barang</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Jumlah</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff;">Satuan</th>
        </tr>
    </thead>
    <tbody>
    @php
        $grouped = collect($transaksi)
            ->filter(fn($row) => $row['qty'] > 0)
            ->groupBy(fn($item) => \Carbon\Carbon::parse($item['tanggal'])->format('Y-m-d'));
    @endphp

    @forelse($grouped as $tanggal => $itemsTanggal)
        @php
            $groupJenis = collect($itemsTanggal)->groupBy('jenis');
            $tanggalRowspan = count($itemsTanggal);
            $printedTanggal = false;
        @endphp

        @foreach($groupJenis as $jenis => $itemsJenis)
            @php
                $groupCabang = collect($itemsJenis)->groupBy('cabang');
                $jenisRowspan = count($itemsJenis);
                $printedJenis = false;
            @endphp

            @foreach($groupCabang as $cabang => $itemsCabang)
                @php
                    $cabangRowspan = count($itemsCabang);
                    $printedCabang = false;
                @endphp

                @foreach($itemsCabang as $row)
                    <tr>
                        {{-- TANGGAL --}}
                        @if(!$printedTanggal)
                            <td style="border:1px solid #000; text-align:center;" rowspan="{{ $tanggalRowspan }}">
                                {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                            </td>
                            @php $printedTanggal = true; @endphp
                        @endif

                        {{-- JENIS --}}
                        @if(!$printedJenis)
                            <td style="border:1px solid #000; text-align:center;" rowspan="{{ $jenisRowspan }}">
                                {{ $row['jenis'] }}
                            </td>
                            @php $printedJenis = true; @endphp
                        @endif

                        {{-- CABANG --}}
                        @if(!$printedCabang)
                            <td style="border:1px solid #000;" rowspan="{{ $cabangRowspan }}">
                                {{ $cabang }}
                            </td>
                            @php $printedCabang = true; @endphp
                        @endif

                        {{-- BARANG --}}
                        <td style="border:1px solid #000;">{{ $row['barang'] }}</td>
                        <td style="border:1px solid #000; text-align:center;">{{ $row['qty'] }}</td>
                        <td style="border:1px solid #000; text-align:center;">{{ $row['satuan'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    @empty
        <tr>
            <td colspan="6" align="center" style="border:1px solid #000;">Tidak ada data</td>
        </tr>
    @endforelse
    </tbody>
</table>

{{-- REKAP --}}
<table cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%; border:1px solid #000; margin-top:20px;">
    <thead>
        <tr>
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8;">Nama Barang</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8;">Satuan</th>
            @foreach($semuaCabang as $cabang)
                <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8;">{{ $cabang->nama }}</th>
            @endforeach
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8;">Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekap as $row)
        @if($row['total'] > 0)
        <tr>
            <td style="border:1px solid #000;">{{ $row['barang'] }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $row['satuan'] }}</td>
            @foreach($semuaCabang as $cabang)
                @php $qtyCabang = $row['cabang'][$cabang->id] ?? 0; @endphp
                <td style="border:1px solid #000; text-align:center;">{{ $qtyCabang > 0 ? $qtyCabang : '' }}</td>
            @endforeach
            <td style="border:1px solid #000; font-weight:bold; text-align:center;">{{ $row['total'] }}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>
