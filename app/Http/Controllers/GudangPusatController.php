<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\LaporanPengirimanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MGudangBarang;
use App\Models\MPengiriman;
use App\Models\MCabang;
use App\Models\MPermintaanPengiriman;
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
            'satuan'      => 'required|string|max:50',
            'stok' => 'required',
            'batas_stok' => 'required',
            'keterangan'  => 'nullable|string',
        ]);

        MGudangBarang::create($request->only([
            'kategori_id',
            'nama_bahan',
            'satuan',
            'stok',
            'batas_stok',
            'keterangan'
        ]));

        return redirect()
            ->route('barang.pusat')
            ->with('tambah', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $barang = MGudangBarang::findOrFail($id);

        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:gudang_barangs,nama_bahan,' . $barang->id,
            'satuan'     => 'required|string|max:50',
            'stok'       => 'required|numeric|min:0',
            'batas_stok' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update($request->only([
            'nama_bahan',
            'satuan',
            'stok',
            'batas_stok',
            'keterangan'
        ]));

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
            'permintaan' => MPermintaanPengiriman::with('cabang')
                                ->orderByDesc('created_at')
                                ->get(),

            'pengiriman' => MPengiriman::with(['cabang','permintaan'])
                                ->orderByDesc('id')
                                ->paginate(10),
        ]);
    }

public function pengirimanStore(Request $request)
{
    $request->validate([
        'cabang_tujuan_id'    => 'required|exists:cabangs,id',
        'tanggal_pengiriman' => 'required|date',
        'barang'              => 'required|array|min:1',
    ]);

    DB::beginTransaction();

    try {

        $detailBarang = [];
        $jumlahDiproses = 0;

        foreach ($request->barang as $item) {

            // ✔️ validasi struktur item
            if (
                !isset($item['gudang_barang_id']) ||
                !isset($item['jumlah']) ||
                $item['jumlah'] == 0
            ) {
                continue;
            }

            $barang = MGudangBarang::find($item['gudang_barang_id']);
            if (!$barang) continue;

            // ✔️ konversi jumlah
            $jumlah = (float) str_replace(',', '.', $item['jumlah']);

            // ✔️ validasi stok
            if ($barang->stok < $jumlah) {
                throw new \Exception(
                    'Stok ' . $barang->nama_bahan . ' tidak mencukupi'
                );
            }

            // ✔️ kurangi stok gudang pusat
            $barang->stok -= $jumlah;
            $barang->save();

            // ✔️ simpan detail barang
            $detailBarang[] = [
                'gudang_barang_id' => $barang->id,
                'nama_barang'     => $barang->nama_bahan,
                'jumlah'          => $jumlah,
                'satuan'          => $barang->satuan,
                'keterangan'      => $item['keterangan'] ?? null,
            ];

            $jumlahDiproses++;
        }

        // ✔️ pastikan ada barang yang diproses
        if ($jumlahDiproses === 0) {
            return back()->with('error', 'Tidak ada barang yang dikirim');
        }

        // ✔️ simpan pengiriman
        MPengiriman::create([
            'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
            'cabang_tujuan_id'    => $request->cabang_tujuan_id,
            'tanggal_pengiriman' => $request->tanggal_pengiriman,
            'status_pengiriman'  => 'Dikemas',
            'keterangan'         => $detailBarang,
        ]);

        DB::commit();

        return back()->with('success', 'Pengiriman berhasil disimpan');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
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

        // status saja, tanggal jangan diubah
        $pengiriman->status_pengiriman = $request->status_pengiriman;

        $pengiriman->save();

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }


    public function pengirimanDestroy($id)
    {
        $items = $pengiriman->keterangan;

        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        foreach ($items as $item) {

            $barang = MGudangBarang::find($item['gudang_barang_id']);
            if (!$barang) continue;

            $jumlah = (float) str_replace(',', '.', $item['jumlah']);

            $barang->stok += $jumlah;
            $barang->save();
        }

        $pengiriman->delete();

        return back()->with('success', 'Pengiriman dibatalkan');
    }

    public function permintaanIndex()
    {
        return view('inventaris.gudangpusat.pengiriman', [
            'permintaan' => MPermintaanPengiriman::with('cabang')->get(),
            'pengiriman' => MPengiriman::with('cabang')->paginate(10),
        ]);
    }

    public function permintaanKirim(Request $request, $id)
    {
        $permintaan = MPermintaanPengiriman::findOrFail($id);

        $request->validate([
            'barang' => 'required|array'
        ]);

        DB::beginTransaction();

        try {

            $detailBarang = [];
            $jumlahDiproses = 0;

            foreach ($request->barang as $item) {

                if (!isset($item['checked'])) {
                    continue;
                }

                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if (!$barang) continue;

                // validasi stok
                $jumlah = (float) str_replace(',', '.', $item['jumlah']);

                if ($barang->stok < $jumlah) {
                    throw new \Exception('Stok '.$barang->nama_bahan.' tidak mencukupi');
                }

                // kurangi stok
                $jumlah = (float) str_replace(',', '.', $item['jumlah']);

                $barang->stok -= $jumlah;
                $barang->save();

                $detailBarang[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'      => $barang->nama_bahan,
                    'jumlah'           => $item['jumlah'],
                    'satuan'           => $barang->satuan,
                ];

                $jumlahDiproses++;
            }

            if ($jumlahDiproses === 0) {
                return back()->with('error', 'Tidak ada barang yang diproses');
            }

            $totalDicentang = collect($request->barang)
                ->filter(fn ($item) => isset($item['checked']))
                ->count();

            $statusKelengkapan = (
                $jumlahDiproses === $totalDicentang
            ) ? 'Lengkap' : 'Tidak Lengkap';

            // SIMPAN PENGIRIMAN
            MPengiriman::create([
                'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'permintaan_id'       => $permintaan->id,
                'cabang_tujuan_id'    => $permintaan->cabang_id,
                'tanggal_pengiriman' => now(),
                'status_pengiriman'  => 'Dikirim',
                'status_kelengkapan' => $statusKelengkapan,
                'keterangan'         => $detailBarang,
                'catatan_gudang'     => $request->catatan
            ]);

            // update status permintaan
            $permintaan->update([
                'status' => 'Diproses'
            ]);

            DB::commit();

            return redirect()
                ->route('permintaan.pusat.index')
                ->with('success', 'Permintaan berhasil diproses');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

public function permintaanDetail($id)
{
    $permintaan = MPermintaanPengiriman::findOrFail($id);

    $result = [];

    foreach ($permintaan->detail_barang as $item) {

        $barang = MGudangBarang::find($item['gudang_barang_id']);

        if (!$barang) continue;

        $result[] = [
            'gudang_barang_id' => $barang->id,
            'nama_barang'     => $barang->nama_bahan,
            'jumlah'          => $item['jumlah'],
            'satuan'          => $barang->satuan,
            'stok'            => $barang->stok,
        ];
    }

    return response()->json($result);
}

public function permintaanProses(Request $request)
{
    $request->validate([
        'permintaan_id' => 'required|exists:permintaan_pengirimans,id',
        'barang'        => 'required|array'
    ]);

    DB::beginTransaction();

    try {

        $permintaan = MPermintaanPengiriman::findOrFail($request->permintaan_id);

        $barangDikirim   = [];
        $jumlahDiproses  = 0;

        foreach ($request->barang as $item) {

            // hanya proses yang dicentang
            if (!isset($item['checked'])) {
                continue;
            }

            if (
                !isset($item['gudang_barang_id']) ||
                !isset($item['jumlah'])
            ) {
                continue;
            }

            $barang = MGudangBarang::find($item['gudang_barang_id']);
            if (!$barang) continue;

            // konversi koma ke titik
            $jumlahBarang = (float) str_replace(',', '.', $item['jumlah']);

            if ($jumlahBarang <= 0) continue;

            // validasi stok
            if ($barang->stok < $jumlahBarang) {
                throw new \Exception(
                    'Stok ' . $barang->nama_bahan . ' tidak mencukupi'
                );
            }

            // kurangi stok gudang pusat
            $barang->stok -= $jumlahBarang;
            $barang->save();

            // simpan detail barang dikirim
            $barangDikirim[] = [
                'gudang_barang_id' => $barang->id,
                'nama_barang'     => $barang->nama_bahan,
                'jumlah'          => $jumlahBarang,
                'satuan'          => $barang->satuan,
            ];

            $jumlahDiproses++;
        }

        // pastikan ada barang yang diproses
        if ($jumlahDiproses === 0) {
            return back()->with('error', 'Tidak ada barang yang dikirim');
        }

        // HITUNG KELENGKAPAN (INI YANG SEBELUMNYA SALAH)
        $totalPermintaan = count($permintaan->detail_barang);

        $statusKelengkapan = (
            $jumlahDiproses === $totalPermintaan
        ) ? 'Lengkap' : 'Tidak Lengkap';

        // SIMPAN PENGIRIMAN
        MPengiriman::create([
            'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
            'permintaan_id'       => $permintaan->id,
            'cabang_tujuan_id'    => $permintaan->cabang_id,
            'tanggal_pengiriman' => now(),
            'status_pengiriman'  => 'Dikemas',
            'status_kelengkapan' => $statusKelengkapan,
            'keterangan'         => $barangDikirim,
            'catatan_gudang'     => $request->catatan
        ]);

        // update status permintaan
        $permintaan->update([
            'status' => 'Diproses'
        ]);

        DB::commit();

        return back()->with('success', 'Permintaan berhasil diproses');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}

//3. LAPORAN CATATAN PENGIRIMAN PERBULAN
    public function laporanIndex()
    {
        $laporan = MPengiriman::whereNotNull('tanggal_pengiriman')
            ->selectRaw('
                MONTH(tanggal_pengiriman) as bulan,
                YEAR(tanggal_pengiriman) as tahun
            ')
            ->groupByRaw('YEAR(tanggal_pengiriman), MONTH(tanggal_pengiriman)')
            ->orderByRaw('YEAR(tanggal_pengiriman) DESC, MONTH(tanggal_pengiriman) DESC')
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

        // 🔹 ambil semua barang (biar yg nol tetap tampil)
        $semuaBarang = MGudangBarang::all();

        // 🔹 ambil semua cabang yg terlibat
        $semuaCabang = $pengiriman
            ->pluck('cabangTujuan')
            ->unique('id')
            ->values();

        // 🔹 inisialisasi rekap
        $rekap = [];

        foreach ($semuaBarang as $barang) {
            foreach ($semuaCabang as $cabang) {
                $rekap[$barang->id]['barang'] = $barang->nama_bahan;
                $rekap[$barang->id]['satuan'] = $barang->satuan;
                $rekap[$barang->id]['cabang'][$cabang->id] = 0;
            }
            $rekap[$barang->id]['total'] = 0;
        }

        // 🔹 hitung real data
        foreach ($pengiriman as $kirim) {
            $detail = is_string($kirim->keterangan)
                ? json_decode($kirim->keterangan, true)
                : $kirim->keterangan;

            foreach ($detail ?? [] as $d) {
                $idBarang = $d['gudang_barang_id'];
                $jumlah   = (float) $d['jumlah'];

                $rekap[$idBarang]['cabang'][$kirim->cabang_tujuan_id] += $jumlah;
                $rekap[$idBarang]['total'] += $jumlah;
            }
        }

        return view('inventaris.gudangpusat.detaillaporan', compact(
            'pengiriman',
            'bulan',
            'tahun',
            'rekap',
            'semuaCabang'
        ));
    }


public function laporanDownload($bulan, $tahun)
{
    $pengiriman = MPengiriman::with('cabangTujuan')
        ->whereMonth('tanggal_pengiriman', $bulan)
        ->whereYear('tanggal_pengiriman', $tahun)
        ->orderBy('tanggal_pengiriman')
        ->get();

    // 🔹 semua barang (biar yg 0 tetap muncul)
    $semuaBarang = MGudangBarang::all();

    // 🔹 semua cabang yang ada di bulan tsb
    $semuaCabang = $pengiriman
        ->pluck('cabangTujuan')
        ->unique('id')
        ->values();

    // 🔹 inisialisasi rekap
    $rekap = [];

    foreach ($semuaBarang as $barang) {
        foreach ($semuaCabang as $cabang) {
            $rekap[$barang->id]['barang'] = $barang->nama_bahan;
            $rekap[$barang->id]['satuan'] = $barang->satuan;
            $rekap[$barang->id]['cabang'][$cabang->id] = 0;
        }
        $rekap[$barang->id]['total'] = 0;
    }

    // 🔹 hitung data pengiriman
    foreach ($pengiriman as $kirim) {
        $detail = is_string($kirim->keterangan)
            ? json_decode($kirim->keterangan, true)
            : $kirim->keterangan;

        foreach ($detail ?? [] as $d) {
            $idBarang = $d['gudang_barang_id'];
            $jumlah   = (float) $d['jumlah'];

            $rekap[$idBarang]['cabang'][$kirim->cabang_tujuan_id] += $jumlah;
            $rekap[$idBarang]['total'] += $jumlah;
        }
    }

    $pdf = \PDF::loadView('inventaris.gudangpusat.laporan_pdf', [
        'pengiriman'   => $pengiriman,
        'bulan'        => $bulan,
        'tahun'        => $tahun,
        'rekap'        => $rekap,
        'semuaCabang'  => $semuaCabang
    ]);

    return $pdf->download('laporan_pengiriman_'.$bulan.'_'.$tahun.'.pdf');
}


public function laporanExcel($bulan, $tahun)
{
    $pengiriman = MPengiriman::with('cabangTujuan')
        ->whereMonth('tanggal_pengiriman', $bulan)
        ->whereYear('tanggal_pengiriman', $tahun)
        ->orderBy('tanggal_pengiriman')
        ->get();

    $semuaBarang = MGudangBarang::all();

    $semuaCabang = $pengiriman
        ->pluck('cabangTujuan')
        ->unique('id')
        ->values();

    // ===== REKAP (SAMA PERSIS DENGAN DETAIL & PDF) =====
    $rekap = [];

    foreach ($semuaBarang as $barang) {
        foreach ($semuaCabang as $cabang) {
            $rekap[$barang->id]['barang'] = $barang->nama_bahan;
            $rekap[$barang->id]['satuan'] = $barang->satuan;
            $rekap[$barang->id]['cabang'][$cabang->id] = 0;
        }
        $rekap[$barang->id]['total'] = 0;
    }

    foreach ($pengiriman as $kirim) {
        $detail = is_string($kirim->keterangan)
            ? json_decode($kirim->keterangan, true)
            : $kirim->keterangan;

        foreach ($detail ?? [] as $d) {
            $idBarang = $d['gudang_barang_id'];
            $jumlah   = (float) $d['jumlah'];

            if (!isset($rekap[$idBarang])) continue;

            $rekap[$idBarang]['cabang'][$kirim->cabang_tujuan_id] += $jumlah;
            $rekap[$idBarang]['total'] += $jumlah;
        }
    }

    return Excel::download(
        new LaporanPengirimanExport(
            $pengiriman,
            $rekap,
            $semuaCabang,
            $bulan,
            $tahun
        ),
        'laporan_pengiriman_'.$bulan.'_'.$tahun.'.xlsx'
    );
}



}
