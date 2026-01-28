<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MCabangBarang;
use App\Models\MGudangBarang;
use App\Models\MCabang;
use App\Models\MPengiriman;
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
            'stok' => 'required|numeric|min:0'
        ]);

        $gudangBarang = MGudangBarang::findOrFail($id);

        MCabangBarang::updateOrCreate(
            [
                'cabang_id'       => $cabang->id,
                'gudang_barang_id'=> $gudangBarang->id
            ],
            [
                'stok' => $request->stok
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

            MCabangBarang::updateOrCreate(
                [
                    'cabang_id'       => $cabang->id,
                    'gudang_barang_id'=> $item['gudang_barang_id']
                ],
                [
                    'stok' => \DB::raw("COALESCE(stok,0) + {$item['jumlah']}")
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

        return view('inventaris.gudangcabang.laporan.detaillaporan', [
            'title' => 'Detail Laporan Penerimaan - ' . $cabang->nama,
            'pengiriman' => $pengiriman,
            'bulan' => $bulan,
            'tahun' => $tahun
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

        $pdf = PDF::loadView('inventaris.gudangcabang.laporan.laporan_pdf', [
            'pengiriman' => $pengiriman,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'cabang' => $cabang
        ]);

        return $pdf->download('laporan_penerimaan_'.$cabang->nama.'_'.$bulan.'_'.$tahun.'.pdf');
    }

}
