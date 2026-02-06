<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GudangCabangLaporanExport;
use App\Models\MCabangBarang;
use App\Models\MGudangBarang;
use App\Models\MCabang;
use App\Models\MPengiriman;
use App\Models\MPermintaanPengiriman;
use App\Models\MInventarisCabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Events\NotifikasiInventaris;
use Carbon\Carbon;
use PDF;

class GudangCabangController extends Controller
{

// 1. BARANG
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
            ->paginate(10);

        return view('inventaris.gudangcabang.barang', [
            'title'   => 'Data Barang - ' . $cabang->nama,
            'cabang'  => $cabang,
            'datas'   => $datas
        ]);
    }

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

    public function barangDestroy($id)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        MCabangBarang::where('cabang_id', $cabang->id)
            ->where('gudang_barang_id', $id)
            ->delete();

        return back()->with('success', 'Stok barang cabang berhasil dihapus.');
    }

//2. PENERIMAAN BARANG
    public function penerimaan()
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $datas = MPengiriman::with('permintaan')
            ->where('cabang_tujuan_id', $cabang->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('inventaris.gudangcabang.penerimaan', [
            'title' => 'Penerimaan Barang - ' . $cabang->nama,
            'cabang' => $cabang,
            'riwayat' => $datas
        ]);
    }

    public function terimaPengiriman(Request $request, $id)
    {
        $pengiriman = MPengiriman::with('permintaan')->findOrFail($id);

        $request->validate([
            'barang' => 'required|array',
            'foto'   => 'required|image|max:2048',
        ]);

        DB::beginTransaction();
        try {

            $barangPermintaan = collect(
                is_string($pengiriman->permintaan->detail_barang)
                    ? json_decode($pengiriman->permintaan->detail_barang, true)
                    : $pengiriman->permintaan->detail_barang
            );

            $barangDiterima = collect($request->barang);
            $statusKelengkapan = 'Lengkap';

            foreach ($barangPermintaan as $item) {

                $diterima = $barangDiterima->firstWhere(
                    'gudang_barang_id',
                    $item['gudang_barang_id']
                );

                // ❌ tidak diterima / jumlah kurang
                if (
                    !$diterima ||
                    empty($diterima['checked']) ||
                    (float) $diterima['jumlah'] < (float) $item['jumlah']
                ) {
                    $statusKelengkapan = 'Tidak Lengkap';
                    continue;
                }

                // ✅ KURANGI STOK PUSAT DI SINI
                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if ($barang) {
                    $barang->stok -= (float) $diterima['jumlah'];
                    $barang->save();
                }
            }

            $fotoPath = $request->file('foto')
                ->store('penerimaan', 'public');

            $pengiriman->update([
                'status_pengiriman'  => 'Diterima',
                'status_kelengkapan' => $statusKelengkapan,
                'tanggal_diterima'   => now(),
                'foto_penerimaan'    => $fotoPath,
                'keterangan_terima'  => $request->keterangan_terima,
            ]);

            $pengiriman->permintaan->update([
                'status' => 'Selesai'
            ]);

            DB::commit();
            return back()->with('success', 'Pengiriman berhasil diterima');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

// 3. PERMINTAAN PENGIRIMAN KE GUDANG
    public function permintaan()
    {
        $user = Auth::user();

        return view('inventaris.gudangcabang.permintaanpengiriman', [
            'barangs' => MGudangBarang::orderBy('nama_bahan')->get(),
            'datas'   => MPermintaanPengiriman::where('cabang_id', $user->cabang_id)
                            ->whereDoesntHave('pengirimans', function ($q) {
                                $q->where('status_pengiriman', 'Diterima');
                            })
                            ->latest()
                            ->paginate(10)
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

        $permintaan = MPermintaanPengiriman::latest()->first();

        event(new NotifikasiInventaris(
            $permintaan->id,
            'Permintaan pengiriman dari ' . $permintaan->cabang->nama,
            'inventory utama',
            'permintaan'
        ));

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

//4. LAPORAN
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
            ->paginate(10);

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

// 5. NOTIFIKASI
    public function getHeaderNotifications()
    {
        $user = Auth::user();

        return MPengiriman::where('cabang_tujuan_id', $user->cabang_id)
            ->where('status_pengiriman', 'Dikirim')
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    public function markNotifRead($id)
    {
        MPengiriman::where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

// 6. INVENTARIS KANTOR CABANG
    public function inventarisIndex()
    {
        $cabangId = Auth::user()->cabang_id;

        $data = MInventarisCabang::where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);

        return view('inventaris.gudangcabang.inventaris.index', compact('data'));
    }

    // FORM TAMBAH
    public function inventarisCreate()
    {
        return view('inventaris.gudangcabang.inventaris.create');
    }

    // SIMPAN + QR
    public function inventarisStore(Request $req)
    {
        $req->validate([
            'kode_barang'   => 'required|unique:inventaris_cabangs',
            'nama_barang'   => 'required',
            'jumlah'        => 'required|numeric|min:1',
            'kondisi'       => 'required',
            'tanggal_input' => 'required|date',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // upload foto
        $fotoPath = null;
        if ($req->hasFile('foto')) {
            $fotoPath = $req->file('foto')->store('inventaris', 'public');
        }

        $inventaris = MInventarisCabang::create([
            'cabang_id'     => Auth::user()->cabang_id,
            'kode_barang'   => $req->kode_barang,
            'nama_barang'   => $req->nama_barang,
            'jumlah'        => $req->jumlah,
            'kondisi'       => $req->kondisi,
            'lokasi'        => $req->lokasi,
            'tanggal_input' => $req->tanggal_input,
            'foto'          => $fotoPath, // ✅ SIMPAN FOTO
        ]);

        // QR SVG
        $qrUrl = route('inventaris.qr.public', $inventaris->kode_barang);

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($qrUrl);

        $path = 'qr_inventaris/qr_'.$inventaris->id.'.svg';
        Storage::disk('public')->put($path, $svg);

        $inventaris->update(['qr_code' => $path]);

        return redirect()
            ->route('gudangcabang.inventaris.index')
            ->with('success','Inventaris berhasil ditambahkan');
    }

    // AMBIL DATA UNTUK MODAL EDIT (AJAX)
    public function inventarisEdit($id)
    {
        return MInventarisCabang::findOrFail($id);
    }

    // UPDATE
    public function inventarisUpdate(Request $req, $id)
    {
        $req->validate([
            'nama_barang' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'kondisi' => 'required',
        ]);

        MInventarisCabang::findOrFail($id)->update($req->all());

        return response()->json(['success' => true]);
    }

    // QR PUBLIC
    public function inventarisQr($kode)
    {
        $inventaris = MInventarisCabang::with('cabang')
            ->where('kode_barang', $kode)
            ->firstOrFail();

        return view(
            'inventaris.gudangcabang.inventaris.show_qr',
            compact('inventaris')
        );
    }


// 7. DASHBOARD
public function dashboard()
{
    $today = Carbon::today();
    $cabangId = Auth::user()->cabang_id;

    // =========================
    // KOTAK ATAS
    // =========================

    $pengirimanMasukHariIni = MPengiriman::where('cabang_tujuan_id', $cabangId)
        ->where('status_pengiriman', 'Dikirim')
        ->whereDate('created_at', $today)
        ->count();

    $pengirimanDiterimaHariIni = MPengiriman::where('cabang_tujuan_id', $cabangId)
        ->where('status_pengiriman', 'Diterima')
        ->whereDate('tanggal_diterima', $today)
        ->count();

    $totalBarangMasukHariIni = 0;

    $pengirimanHariIni = MPengiriman::where('cabang_tujuan_id', $cabangId)
        ->where('status_pengiriman', 'Diterima')
        ->whereDate('tanggal_diterima', $today)
        ->get();

    foreach ($pengirimanHariIni as $p) {
        foreach ($p->keterangan ?? [] as $item) {
            $totalBarangMasukHariIni += (int) ($item['jumlah'] ?? 0);
        }
    }

    $totalJenisBarang = MCabangBarang::where('cabang_id', $cabangId)->count();

    // =========================
    // GRAFIK 7 HARI (SAMA KAYAK PUSAT)
    // =========================

    $labels7Hari = [];
    $grafikPengirimanMasuk = [];
    $grafikPengirimanDiterima = [];
    $grafikBarangMasuk = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::today()->subDays($i);

        // label (contoh: 29 Jan)
        $labels7Hari[] = $date->format('d M');

        // grafik 1: pengiriman dikirim
        $grafikPengirimanMasuk[] = MPengiriman::where('cabang_tujuan_id', $cabangId)
            ->where('status_pengiriman', 'Dikirim')
            ->whereDate('created_at', $date)
            ->count();

        // grafik 2: pengiriman diterima
        $grafikPengirimanDiterima[] = MPengiriman::where('cabang_tujuan_id', $cabangId)
            ->where('status_pengiriman', 'Diterima')
            ->whereDate('tanggal_diterima', $date)
            ->count();

        // grafik 3: total barang masuk
        $totalPerHari = 0;

        $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabangId)
            ->where('status_pengiriman', 'Diterima')
            ->whereDate('tanggal_diterima', $date)
            ->get();

        foreach ($pengiriman as $p) {
            foreach ($p->keterangan ?? [] as $item) {
                $totalPerHari += (int) ($item['jumlah'] ?? 0);
            }
        }

        $grafikBarangMasuk[] = $totalPerHari;
    }

    // =========================
    // WAKTU TERAKHIR UPDATE
    // =========================

    $lastPengirimanUpdate = MPengiriman::where('cabang_tujuan_id', $cabangId)
        ->where('status_pengiriman', 'Dikirim')
        ->latest('created_at')
        ->first();

    $lastPenerimaanUpdate = MPengiriman::where('cabang_tujuan_id', $cabangId)
        ->where('status_pengiriman', 'Diterima')
        ->latest('tanggal_diterima')
        ->first();

    $lastBarangUpdate = $lastPenerimaanUpdate;

    return view('inventaris.gudangcabang.dashboard', compact(
        'pengirimanMasukHariIni',
        'pengirimanDiterimaHariIni',
        'totalBarangMasukHariIni',
        'totalJenisBarang',
        'labels7Hari',
        'grafikPengirimanMasuk',
        'grafikPengirimanDiterima',
        'grafikBarangMasuk',
        'lastPengirimanUpdate',
        'lastPenerimaanUpdate',
        'lastBarangUpdate'
    ));
}

}
