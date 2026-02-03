<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GudangCabangLaporanExport;
use App\Models\MCabangBarang;
use App\Models\MGudangBarang;
use App\Models\MCabang;
use App\Models\MPengiriman;
use App\Models\MPermintaanPengiriman;
use Illuminate\Support\Facades\Auth;
use PDF;

class GudangCabangController extends Controller
{
    /**
     * 🔹 Daftar Barang Cabang
     */
    public function barang()
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $datas = MGudangBarang::leftJoin('cabang_barangs', function ($join) use ($cabang) {
                $join->on('gudang_barangs.id', '=', 'cabang_barangs.gudang_barang_id')
                    ->where('cabang_barangs.cabang_id', $cabang->id);
            })
            ->select(
                'gudang_barangs.*',
                \DB::raw('COALESCE(cabang_barangs.stok, 0) as stok_cabang')
            )
            ->orderBy('gudang_barangs.nama_bahan', 'ASC')
            ->get();

        return view('inventaris.gudangcabang.barang', [
            'title'   => 'Data Barang - ' . $cabang->nama,
            'cabang'  => $cabang,
            'datas'   => $datas
        ]);
    }

    /**
     * 🔹 Update Stok Barang Cabang
     */
    public function barangUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $request->validate([
            'stok' => 'required'
        ]);

        $stok = (float) str_replace(',', '.', $request->stok);

        $gudangBarang = MGudangBarang::findOrFail($id);

        MCabangBarang::updateOrCreate(
            [
                'cabang_id'       => $cabang->id,
                'gudang_barang_id'=> $gudangBarang->id
            ],
            [
                'stok' => $stok
            ]
        );

        return back()->with('success', 'Stok barang cabang berhasil diperbarui.');
    }

    /**
     * 🔹 Delete Stok Barang Cabang (opsional)
     */
    public function barangDestroy($id)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        MCabangBarang::where('cabang_id', $cabang->id)
            ->where('gudang_barang_id', $id)
            ->delete();

        return back()->with('success', 'Stok barang cabang berhasil dihapus.');
    }


    /**
     * ==============================
     * 🔹 PENERIMAAN BARANG (PENGIRIMAN)
     * ==============================
     */

    /**
     * 🔹 Tampilkan daftar pengiriman yang ditujukan ke cabang
     */
    public function penerimaan()
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        // Hanya pengiriman yang menuju cabang ini dan statusnya Dikirim
        $datas = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            // ->where('status_pengiriman', 'Dikirim')
            ->orderByDesc('id')
            ->get();

        return view('inventaris.gudangcabang.penerimaan', [
            'title' => 'Penerimaan Barang - ' . $cabang->nama,
            'cabang' => $cabang,
            'riwayat' => $datas
        ]);
    }


    /**
     * 🔹 Terima barang dari pengiriman
     */
    public function terimaPengiriman(Request $request, $id)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $pengiriman = MPengiriman::findOrFail($id);

        if ($pengiriman->cabang_tujuan_id != $cabang->id) {
            return back()->with('error', 'Pengiriman tidak ditujukan ke cabang Anda.');
        }

        if ($pengiriman->status_pengiriman != 'Dikirim') {
            return back()->with('error', 'Pengiriman hanya bisa diterima jika statusnya Dikirim.');
        }

        // ✔️ Aman untuk array maupun json string
        $items = $pengiriman->keterangan;

        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        if (!is_array($items)) {
            $items = [];
        }

        foreach ($items as $item) {

            // ✔️ Pastikan ada gudang_barang_id dan jumlah
            if (!isset($item['gudang_barang_id']) || !isset($item['jumlah'])) {
                continue;
            }

            $jumlah = (float) str_replace(',', '.', $item['jumlah']);

            MCabangBarang::updateOrCreate(
                [
                    'cabang_id'        => $cabang->id,
                    'gudang_barang_id' => $item['gudang_barang_id']
                ],
                [
                    'stok' => \DB::raw("COALESCE(stok,0) + {$jumlah}")
                ]
            );

        }

        $pengiriman->status_pengiriman = 'Diterima';
        $pengiriman->tanggal_diterima = now();
        $pengiriman->save();

        return back()->with('success', 'Pengiriman berhasil diterima dan stok cabang terupdate.');
    }

    public function laporanIndex()
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $laporan = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            ->where('status_pengiriman', 'Diterima')
            ->selectRaw('
                MONTH(tanggal_diterima) as bulan,
                YEAR(tanggal_diterima) as tahun,
                COUNT(*) as total
            ')
            ->groupBy('bulan', 'tahun')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('inventaris.gudangcabang.laporan.laporan', [
            'title' => 'Laporan Penerimaan Barang - ' . $cabang->nama,
            'laporan' => $laporan,
            'cabang' => $cabang
        ]);
    }

public function laporanDetail($bulan, $tahun)
{
    $user = Auth::user();
    $cabang = MCabang::findOrFail($user->cabang_id);

    $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabang->id)
        ->where('status_pengiriman', 'Diterima')
        ->whereMonth('tanggal_diterima', $bulan)
        ->whereYear('tanggal_diterima', $tahun)
        ->orderBy('tanggal_diterima')
        ->get();

    // 🔹 semua barang (biar yg nol tetap tampil)
    $semuaBarang = MGudangBarang::all();

    // 🔹 inisialisasi rekap
    $rekap = [];
    foreach ($semuaBarang as $barang) {
        $rekap[$barang->id] = [
            'barang' => $barang->nama_bahan,
            'satuan' => $barang->satuan,
            'total'  => 0
        ];
    }

    // 🔹 hitung total diterima
    foreach ($pengiriman as $item) {
        $detail = is_string($item->keterangan)
            ? json_decode($item->keterangan, true)
            : $item->keterangan;

        foreach ($detail ?? [] as $d) {
            $rekap[$d['gudang_barang_id']]['total']
                += (float) $d['jumlah'];
        }
    }

    return view('inventaris.gudangcabang.laporan.detaillaporan', [
        'title'      => 'Detail Laporan Penerimaan - ' . $cabang->nama,
        'pengiriman' => $pengiriman,
        'bulan'      => $bulan,
        'tahun'      => $tahun,
        'rekap'      => $rekap,
        'cabang'     => $cabang
    ]);
}

public function laporanDownload($bulan, $tahun)
{
    $user = Auth::user();
    $cabang = MCabang::findOrFail($user->cabang_id);

    $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabang->id)
        ->where('status_pengiriman', 'Diterima')
        ->whereMonth('tanggal_diterima', $bulan)
        ->whereYear('tanggal_diterima', $tahun)
        ->orderBy('tanggal_diterima')
        ->get();

    $semuaBarang = MGudangBarang::all();

    $rekap = [];
    foreach ($semuaBarang as $barang) {
        $rekap[$barang->id] = [
            'barang' => $barang->nama_bahan,
            'satuan' => $barang->satuan,
            'total'  => 0
        ];
    }

    foreach ($pengiriman as $item) {
        $detail = is_string($item->keterangan)
            ? json_decode($item->keterangan, true)
            : $item->keterangan;

        foreach ($detail ?? [] as $d) {
            $rekap[$d['gudang_barang_id']]['total']
                += (float) $d['jumlah'];
        }
    }

    $pdf = PDF::loadView('inventaris.gudangcabang.laporan.laporan_pdf', [
        'pengiriman' => $pengiriman,
        'bulan'      => $bulan,
        'tahun'      => $tahun,
        'cabang'     => $cabang,
        'rekap'      => $rekap
    ]);

    return $pdf->download(
        'laporan_penerimaan_'.$cabang->nama.'_'.$bulan.'_'.$tahun.'.pdf'
    );
}


    public function permintaan()
    {
        $user = Auth::user();

        return view('inventaris.gudangcabang.permintaanpengiriman', [
            'barangs' => MGudangBarang::orderBy('nama_bahan')->get(),
            'datas'   => MPermintaanPengiriman::where('cabang_id', $user->cabang_id)
                            ->latest()
                            ->get()
        ]);
    }

    public function permintaanStore(Request $request)
    {
        $request->validate([
            'tanggal_permintaan' => 'required|date',
            'barang'             => 'required|array'
        ]);

        $detailBarang = [];

        foreach ($request->barang as $item) {

            if (!isset($item['gudang_barang_id']) || !isset($item['jumlah'])) {
                continue;
            }

            $barang = MGudangBarang::find($item['gudang_barang_id']);

            if (!$barang) continue;

            $jumlah = (float) str_replace(',', '.', $item['jumlah']);

            $detailBarang[] = [
                'gudang_barang_id' => $barang->id,
                'nama_barang'     => $barang->nama_bahan,
                'jumlah'          => $jumlah,
                'satuan'          => $barang->satuan,
                'keterangan'      => $item['keterangan'] ?? null
            ];

        }

        MPermintaanPengiriman::create([
            'kode_permintaan'    => 'REQ-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'cabang_id'          => Auth::user()->cabang_id,
            'tanggal_permintaan' => $request->tanggal_permintaan,
            'status'             => 'Menunggu',
            'detail_barang'      => $detailBarang,
            'catatan'            => $request->catatan
        ]);

        return back()->with('success', 'Permintaan pengiriman berhasil dibuat');
    }
public function laporanExcel($bulan, $tahun)
{
    $user = Auth::user();
    $cabang = MCabang::findOrFail($user->cabang_id);

    $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabang->id)
        ->where('status_pengiriman', 'Diterima')
        ->whereMonth('tanggal_diterima', $bulan)
        ->whereYear('tanggal_diterima', $tahun)
        ->orderBy('tanggal_diterima')
        ->get();

    $semuaBarang = MGudangBarang::all();

    $rekap = [];
    foreach ($semuaBarang as $barang) {
        $rekap[$barang->id] = [
            'barang' => $barang->nama_bahan,
            'satuan' => $barang->satuan,
            'total'  => 0
        ];
    }

    foreach ($pengiriman as $item) {
        $detail = is_string($item->keterangan)
            ? json_decode($item->keterangan, true)
            : $item->keterangan;

        foreach ($detail ?? [] as $d) {
            $rekap[$d['gudang_barang_id']]['total']
                += (float) $d['jumlah'];
        }
    }

    return Excel::download(
        new GudangCabangLaporanExport(
            $pengiriman,
            $rekap,
            $bulan,
            $tahun,
            $cabang
        ),
        'laporan_penerimaan_'.$cabang->nama.'_'.$bulan.'_'.$tahun.'.xlsx'
    );
}

}
