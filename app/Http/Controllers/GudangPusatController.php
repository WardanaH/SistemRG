<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MGudangBarang;
use App\Models\MPengiriman;
use App\Models\MCabang;
use PDF;

class GudangPusatController extends Controller
{

    // 1. BARANG
    public function index()
    {
        $datas = MGudangBarang::orderByDesc('stok')->get();

        return view('inventaris.gudangpusat.barang', [
            'title' => 'Data Barang Gudang Pusat',
            'datas' => $datas,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'nullable|integer',
            'nama_bahan'  => 'required|string|max:255|unique:gudang_barangs,nama_bahan',
            'harga'       => 'required|numeric|min:0',
            'satuan'      => 'required|string|max:50',
            'stok'        => 'required|numeric|min:0',
            'batas_stok'  => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
        ]);

        MGudangBarang::create($request->all());

            return redirect()
            ->route('barang.pusat')
            ->with('tambah', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barang = MGudangBarang::findOrFail($id);

        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:gudang_barangs,nama_bahan,' . $barang->id,
            'harga'      => 'required|numeric|min:0',
            'satuan'     => 'required|string|max:50',
            'stok'       => 'required|numeric|min:0',
            'batas_stok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update($request->all());

        return redirect()
        ->route('barang.pusat')
        ->with('edit', 'Barang berhasil diperbarui');

    }

    public function destroy($id)
    {
        MGudangBarang::findOrFail($id)->delete();

        return back()->with('success', 'Barang berhasil dihapus');
    }

    //2. PENGIRIMAN
    public function pengirimanIndex()
    {
        return view('inventaris.gudangpusat.pengiriman', [
            'barangs'     => MGudangBarang::orderBy('nama_bahan')->get(),
            'pengiriman'  => MPengiriman::with('barang')->latest()->paginate(10),
            'cabangs'     => MCabang::orderBy('nama')->get(),
        ]);
    }

    public function pengirimanStore(Request $request)
    {
        $request->validate([
            'cabang_tujuan_id' => 'required',
            'tanggal_pengiriman' => 'required|date',
            'barang' => 'required|array'
        ]);

        $detailBarang = [];

        foreach ($request->barang as $item) {

            if (!isset($item['gudang_barang_id']) || !isset($item['jumlah'])) {
                continue;
            }

            $barang = MGudangBarang::find($item['gudang_barang_id']);

            if ($barang) {
                $detailBarang[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'      => $barang->nama_bahan,
                    'jumlah'           => $item['jumlah'],
                    'satuan'           => $barang->satuan ?? '-',
                    'keterangan'       => $item['keterangan'] ?? null
                ];

                $barang->decrement('stok', $item['jumlah']);
            }
        }

        MPengiriman::create([
            'kode_pengiriman' => 'KRM-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'cabang_tujuan_id' => $request->cabang_tujuan_id,
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'status_pengiriman' => 'Dikemas',
            'keterangan' => $detailBarang
        ]);

        return redirect()->back()->with('success', 'Pengiriman berhasil disimpan');
    }


    public function pengirimanUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pengiriman' => 'required|in:Dikemas,Dikirim'
        ]);

        $pengiriman = MPengiriman::findOrFail($id);

        if (in_array($pengiriman->status_pengiriman, ['Dikirim', 'Diterima'])) {
            return back()->with('error', 'Status pengiriman tidak dapat diubah lagi.');
        }

        $pengiriman->status_pengiriman = $request->status_pengiriman;

        if ($request->status_pengiriman === 'Dikirim') {
            $pengiriman->tanggal_pengiriman = now();
        }

        $pengiriman->save();

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }


    public function pengirimanDestroy($id)
    {
        $pengiriman = MPengiriman::findOrFail($id);

        if ($pengiriman->status_pengiriman !== 'Dikemas') {
            return back()->with('error', 'Hanya bisa dihapus saat Dikemas');
        }

        $pengiriman->barang->increment('stok', $pengiriman->jumlah);
        $pengiriman->delete();

        return back()->with('success', 'Pengiriman dibatalkan');
    }

    //LAPORAN CATATAN PENGIRIMAN PERBULAN
    public function laporanIndex()
    {
        $laporan = MPengiriman::selectRaw('
                MONTH(tanggal_pengiriman) as bulan,
                YEAR(tanggal_pengiriman) as tahun,
                COUNT(*) as total
            ')
            ->groupBy('bulan', 'tahun')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('inventaris.gudangpusat.laporan', compact('laporan'));
    }

    public function laporanDetail($bulan, $tahun)
    {
        $pengiriman = MPengiriman::with('cabangTujuan')
            ->whereMonth('tanggal_pengiriman', $bulan)
            ->whereYear('tanggal_pengiriman', $tahun)
            ->orderBy('tanggal_pengiriman')
            ->get();

        return view('inventaris.gudangpusat.detaillaporan', [
            'pengiriman' => $pengiriman,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

public function laporanDownload($bulan, $tahun)
{
    $pengiriman = MPengiriman::with('cabangTujuan')
        ->whereMonth('tanggal_pengiriman', $bulan)
        ->whereYear('tanggal_pengiriman', $tahun)
        ->orderBy('tanggal_pengiriman')
        ->get();

    $pdf = \PDF::loadView('inventaris.gudangpusat.laporan_pdf', [
        'pengiriman' => $pengiriman,
        'bulan' => $bulan,
        'tahun' => $tahun
    ]);

    return $pdf->download('laporan_pengiriman_'.$bulan.'_'.$tahun.'.pdf');
}


}
