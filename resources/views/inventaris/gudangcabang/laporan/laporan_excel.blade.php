{{-- =========================
HEADER LAPORAN
========================= --}}
<table width="100%" cellpadding="0" cellspacing="0" style="border:none;">
    <tr>
        <td colspan="10" style="text-align:center; font-weight:bold; font-size:16px;">
            LAPORAN PENERIMAAN BARANG
        </td>
    </tr>
    <tr>
        <td colspan="10" style="text-align:center;">
            Bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}
            Tahun {{ $tahun }}
        </td>
    </tr>
</table>

<br>

{{-- =========================
TABEL DETAIL PENERIMAAN
========================= --}}
<table cellpadding="6" cellspacing="0"
       style="border-collapse:collapse; width:100%; border:1px solid #000;">
    <thead>
        <tr style="background:#eaeaea; text-align:center;">
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Tanggal Diterima</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Nama Barang</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Jumlah</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Satuan</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Keterangan</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#acdcff; color:#000000;">Dari Cabang / Gudang</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pengiriman as $item)
            @php
                $detail = is_string($item->keterangan)
                    ? json_decode($item->keterangan, true)
                    : $item->keterangan;

                $detail = is_array($detail) ? $detail : [];
            @endphp

            @foreach($detail ?: [[]] as $d)
            <tr>
                <td style="border:1px solid #000;" align="center">
                    {{ \Carbon\Carbon::parse($item->tanggal_diterima)->format('d-m-Y') }}
                </td>
                <td style="border:1px solid #000;">
                    {{ $d['nama_barang'] ?? '-' }}
                </td>
                <td style="border:1px solid #000;" align="center">
                    {{ $d['jumlah'] ?? 0 }}
                </td>
                <td style="border:1px solid #000;" align="center">
                    {{ $d['satuan'] ?? '-' }}
                </td>
                <td style="border:1px solid #000;">
                    {{ $d['keterangan'] ?? '-' }}
                </td>
                <td style="border:1px solid #000;">
                    {{ $item->cabangAsal->nama ?? 'Gudang Pusat' }}
                </td>
            </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" align="center" style="border:1px solid #000;">
                    Tidak ada data penerimaan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<br><br>

{{-- =========================
TABEL REKAP PENERIMAAN
========================= --}}
<table cellpadding="6" cellspacing="0"
       style="border-collapse:collapse; width:100%; border:1px solid #000;">
    <thead>
        <tr style="background:#eaeaea; text-align:center;">
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8; color:#000000;">Nama Barang</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8; color:#000000;">Satuan</th>
            <th style="border:1px solid #000; font-weight:bold; background-color:#f8b0c8; color:#000000;">Total Diterima</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekap as $row)
        <tr>
            <td style="border:1px solid #000;">
                {{ $row['barang'] }}
            </td>
            <td style="border:1px solid #000;" align="center">
                {{ $row['satuan'] }}
            </td>
            <td style="border:1px solid #000; font-weight:bold;" align="center">
                {{ $row['total'] }}
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
