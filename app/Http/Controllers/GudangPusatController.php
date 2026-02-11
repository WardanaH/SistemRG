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
use App\Models\MCabangBarang;
use App\Models\MPengambilan;
use App\Models\MPermintaanPengiriman;
use Illuminate\Support\Facades\Auth;
use App\Events\NotifikasiInventarisCabang;
use PDF;

class GudangPusatController extends Controller
{

// 1. BARANG
    public function index(Request $request)
    {
        $query = MGudangBarang::query();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('nama_bahan', 'like', '%' . $search . '%')
                ->orWhere('satuan', 'like', '%' . $search . '%')
                ->orWhere('keterangan', 'like', '%' . $search . '%');

            // hasil pencarian tampil paling atas
            $query->orderByRaw("CASE
                WHEN nama_bahan LIKE '%$search%' THEN 0
                ELSE 1
            END");
        }

        $datas = $query->orderByDesc('created_at')->paginate(10)
                    ->withQueryString();

        return view('inventaris.gudangpusat.barang', [
            'title' => 'Data Barang Gudang Pusat',
            'datas' => $datas,
        ]);
    }

    private function toDecimal($value)
    {
        if ($value === null || $value === '') return null;

        // hapus pemisah ribuan
        $value = str_replace('.', '', $value);

        // ubah koma ke titik
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'nullable|integer',
            'nama_bahan'  => 'required|string|max:255|unique:gudang_barangs,nama_bahan',
            'satuan'      => 'required|string|max:50',
            'stok'        => 'required',
            'batas_stok'  => 'required',
            'keterangan'  => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {

            // ✅ WAJIB disimpan ke variabel
            $barang = MGudangBarang::create([
                'kategori_id' => $request->kategori_id,
                'nama_bahan'  => $request->nama_bahan,
                'satuan'      => $request->satuan,
                'stok'        => $this->toDecimal($request->stok),
                'batas_stok'  => $this->toDecimal($request->batas_stok),
                'keterangan'  => $request->keterangan,
            ]);

            // 🔥 AUTO INSERT KE SEMUA CABANG
            $cabangs = MCabang::all();

            foreach ($cabangs as $cabang) {
                MCabangBarang::create([
                    'cabang_id'        => $cabang->id,
                    'gudang_barang_id' => $barang->id,
                    'stok'             => 0
                ]);
            }

            DB::commit();

            return redirect()
                ->route('barang.pusat')
                ->with('tambah', 'Barang berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
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
//2. UPDATA STOK
    public function updateStokIndex(Request $request)
    {
        $barangs = MGudangBarang::orderBy('nama_bahan')->get();

        $query = MGudangBarang::query();

        // 🔎 SEARCH NAMA BARANG
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('nama_bahan', 'like', '%' . $search . '%');

            // hasil paling cocok muncul di atas
            $query->orderByRaw("CASE
                WHEN nama_bahan LIKE '%$search%' THEN 0
                ELSE 1
            END");
        }

        $datas = $query->orderByDesc('updated_at')
                    ->paginate(10)
                    ->withQueryString();

        return view('inventaris.gudangpusat.updatestok', [
            'barangs' => $barangs,
            'datas'   => $datas,
            'title'   => 'Update Stok Gudang Pusat'
        ]);
    }

    public function updateStokStore(Request $request)
    {
        $request->validate([
            'barang_id'    => 'required|exists:gudang_barangs,id',
            'tambah_stok'  => 'nullable',
            'kurangi_stok' => 'nullable',
        ]);

        if (!$request->tambah_stok && !$request->kurangi_stok) {
            return back()->with('error', 'Isi tambah atau kurangi stok');
        }

        if ($request->tambah_stok && $request->kurangi_stok) {
            return back()->with('error', 'Hanya boleh isi salah satu');
        }

        $barang = MGudangBarang::findOrFail($request->barang_id);

        $tambah  = $this->toDecimal($request->tambah_stok);
        $kurangi = $this->toDecimal($request->kurangi_stok);

        $stokBaru = (float) $barang->stok;

        if ($tambah !== null) {
            $stokBaru += $tambah;
        }

        if ($kurangi !== null) {
            if ($stokBaru < $kurangi) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
            $stokBaru -= $kurangi;
        }

        $barang->update([
            'stok' => $stokBaru
        ]);

        return redirect()
            ->route('barang.pusat.updatestok')
            ->with('success', 'Stok berhasil diperbarui');
    }

//3. PENGIRIMAN
    public function pengirimanIndex()
    {
        return view('inventaris.gudangpusat.pengiriman', [

            'permintaan' => MPermintaanPengiriman::with('cabang')
                                ->where('status', '!=', 'Selesai')
                                ->orderByDesc('created_at')
                                ->paginate(10),

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

                if (
                    !isset($item['gudang_barang_id']) ||
                    !isset($item['jumlah']) ||
                    $item['jumlah'] == 0
                ) {
                    continue;
                }

                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if (!$barang) continue;

                $jumlah = (float) str_replace(',', '.', $item['jumlah']);

                if ($barang->stok < $jumlah) {
                    throw new \Exception(
                        'Stok ' . $barang->nama_bahan . ' tidak mencukupi'
                    );
                }

                // $barang->stok -= $jumlah;
                // $barang->save();

                $detailBarang[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'     => $barang->nama_bahan,
                    'jumlah'          => $jumlah,
                    'satuan'          => $barang->satuan,
                    'keterangan'      => $item['keterangan'] ?? null,
                ];

                $jumlahDiproses++;
            }

            if ($jumlahDiproses === 0) {
                return back()->with('error', 'Tidak ada barang yang dikirim');
            }

            MPengiriman::create([
                'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'permintaan_id'       => $request->permintaan_id,
                'cabang_tujuan_id'    => $request->cabang_tujuan_id,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                // 'status_pengiriman'  => null,
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
            'status_pengiriman' => 'required|in:Dikemas,Dikirim,Diterima'
        ]);

        $pengiriman = MPengiriman::findOrFail($id);

        if (
            ($pengiriman->status_pengiriman === 'Dikemas' && $request->status_pengiriman !== 'Dikirim') ||
            ($pengiriman->status_pengiriman === 'Dikirim' && $request->status_pengiriman !== 'Diterima') ||
            ($pengiriman->status_pengiriman === 'Diterima')
        ) {
            // 🔥 kalau AJAX → balikin JSON error
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Perubahan status tidak valid'
                ], 422);
            }

            return back()->with('error', 'Perubahan status tidak valid.');
        }

        $pengiriman->status_pengiriman = $request->status_pengiriman;
        $pengiriman->save();

    if ($request->status_pengiriman === 'Dikirim') {
        try {
            $permintaan = MPermintaanPengiriman::with('cabang')
                ->findOrFail($pengiriman->permintaan_id);

            event(new NotifikasiInventarisCabang(
                $permintaan->id,
                'Permintaan pengiriman baru',
                'inventory utama',
                'permintaan',
            ));
        } catch (\Throwable $e) {
            \Log::error('Notifikasi pengiriman gagal', [
                'pengiriman_id' => $pengiriman->id,
                'error' => $e->getMessage()
            ]);
        }

        if ($request->status_pengiriman === 'Diterima') {
            MPermintaanPengiriman::where('id', $pengiriman->permintaan_id)
                ->update(['status' => 'Selesai']);
        } else {
            $this->updateStatusPermintaan($pengiriman->permintaan_id);
        }

        // 🔥 INI KUNCI UTAMANYA
        if ($request->ajax()) {
            return response()->json([
                'status' => 'ok'
            ]);
        }

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }
    }

    private function updateStatusPermintaan($permintaanId)
    {
        if (!$permintaanId) return;

        $permintaan = MPermintaanPengiriman::with('cabang')
            ->findOrFail($permintaanId);

        $totalPengiriman = MPengiriman::where('permintaan_id', $permintaan->id)->count();
        $totalDiterima = MPengiriman::where('permintaan_id', $permintaan->id)
            ->where('status_pengiriman', 'Diterima')
            ->count();

        if ($totalPengiriman === 0) {
            $permintaan->status = 'Menunggu';
        } elseif ($totalPengiriman === $totalDiterima) {
            $permintaan->status = 'Selesai';
        } else {
            $permintaan->status = 'Diproses';
        }

        $permintaan->save();
    }

    public function pengirimanEditData($id)
    {
        $pengiriman = MPengiriman::findOrFail($id);

        if ($pengiriman->status_pengiriman !== 'Dikemas') {
            return response()->json([
                'message' => 'Pengiriman sudah dikirim'
            ], 403);
        }

        $detail = is_string($pengiriman->keterangan)
            ? json_decode($pengiriman->keterangan, true)
            : $pengiriman->keterangan;

        $result = [];

        foreach ($detail ?? [] as $item) {

            $barang = MGudangBarang::find($item['gudang_barang_id']);

            $result[] = [
                'gudang_barang_id' => $item['gudang_barang_id'],
                'nama_barang'      => $item['nama_barang'],
                'jumlah'           => $item['jumlah'],
                'satuan'           => $item['satuan'],
                'keterangan'       => $item['keterangan'] ?? '',
                'stok'             => $barang->stok ?? 0,
            ];
        }

        return response()->json([
            'id'     => $pengiriman->id,
            'kode'   => $pengiriman->kode_pengiriman,
            'cabang'             => $pengiriman->cabang->nama ?? '-',
            'catatan_permintaan' => $pengiriman->permintaan->catatan ?? null,
            'detail' => $result
        ]);
    }

    public function pengirimanUpdate(Request $request, $id)
    {
        $pengiriman = MPengiriman::findOrFail($id);

        if ($pengiriman->status_pengiriman !== 'Dikemas') {
            return back()->with('error', 'Pengiriman sudah dikirim, tidak bisa diedit.');
        }

        $request->validate([
            'catatan_gudang' => 'nullable|string',
        ]);

        $pengiriman->update([
            'catatan_gudang' => $request->catatan_gudang
        ]);

        return back()->with('success', 'Catatan pengiriman berhasil diperbarui.');
    }

    public function pengirimanDestroy($id)
    {
        DB::beginTransaction();
        try {
            $pengiriman = MPengiriman::findOrFail($id);

            // kembalikan stok barang
            $items = is_string($pengiriman->keterangan) ? json_decode($pengiriman->keterangan, true) : $pengiriman->keterangan;
            foreach ($items ?? [] as $item) {
                if (!isset($item['gudang_barang_id'], $item['jumlah'])) continue;
                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if (!$barang) continue;
                $barang->stok += (float) $item['jumlah'];
                $barang->save();
            }

            $permintaanId = $pengiriman->permintaan_id;
            $pengiriman->delete();
            $this->updateStatusPermintaan($permintaanId);
            // $this->updateStatusPermintaan($permintaanId);

            DB::commit();
            return back()->with('success', 'Pengiriman berhasil dibatalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    //PENGIRIMAN BAGIAN PERMINTAAN DARI CABANG
    public function permintaanIndex()
    {
        $permintaan = MPermintaanPengiriman::with('cabang')
                        ->orderByDesc('created_at')
                        ->get();

        $pengiriman = MPengiriman::with(['cabang', 'permintaan'])
                        ->orderByDesc('id')
                        ->paginate(10);

        return view('inventaris.gudangpusat.pengiriman', compact('permintaan','pengiriman'));
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

                // $barang->stok -= $jumlah;
                // $barang->save();

                $detailBarang[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'      => $barang->nama_bahan,
                    'jumlah'           => $item['jumlah'],
                    'satuan'           => $barang->satuan,
                    'keterangan'       => $item['keterangan'] ?? null,
                ];

                $jumlahDiproses++;
            }

            if ($jumlahDiproses === 0) {
                return back()->with('error', 'Tidak ada barang yang diproses');
            }

            $totalDicentang = collect($request->barang)
                ->filter(fn ($item) => isset($item['checked']))
                ->count();

            // $statusKelengkapan = (
            //     $jumlahDiproses === $totalDicentang
            // ) ? 'Lengkap' : 'Tidak Lengkap';

            MPengiriman::create([
                'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'permintaan_id'       => $permintaan->id,
                'cabang_tujuan_id'    => $permintaan->cabang_id,
                'tanggal_pengiriman' => now(),
                'status_pengiriman'  => 'Dikirim',
                'status_kelengkapan' => null,
                'keterangan'         => $detailBarang,
                'catatan_gudang'     => $request->catatan
            ]);

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

        $detail = is_string($permintaan->detail_barang)
            ? json_decode($permintaan->detail_barang, true)
            : $permintaan->detail_barang;

        $result = [];

        foreach ($detail ?? [] as $item) {

            $barang = MGudangBarang::find($item['gudang_barang_id'] ?? null);
            if (!$barang) continue;

            $result[] = [
                'gudang_barang_id' => $barang->id,
                'nama_barang'      => $barang->nama_bahan,
                'jumlah'           => $item['jumlah'],
                'satuan'           => $barang->satuan,
                'keterangan'       => $item['keterangan'] ?? '',
                'stok'             => $barang->stok,
            ];
        }

        return response()->json([
            'detail'  => $result,
            'catatan' => $permintaan->catatan
        ]);
    }

    public function permintaanProses(Request $request)
    {
        $request->validate([
            'permintaan_id' => 'required|exists:permintaan_pengirimans,id',
            'barang'        => 'required|array',
            'barang.*.gudang_barang_id' => 'required',
            'barang.*.jumlah' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $permintaan = MPermintaanPengiriman::findOrFail($request->permintaan_id);

            $barangDikirim = [];
            $jumlahDiproses = 0;

            foreach ($permintaan->detail_barang as $item) {

                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if (!$barang) continue;

                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$barang->nama_bahan} tidak cukup");
                }

                // $barang->stok -= $item['jumlah'];
                // $barang->save();

                $barangDikirim[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'      => $barang->nama_bahan,
                    'jumlah'           => $item['jumlah'],
                    'satuan'           => $barang->satuan,
                    'keterangan'       => $item['keterangan'] ?? null,
                ];

                $jumlahDiproses++;
            }

            if ($jumlahDiproses === 0) {
                return back()->with('error', 'Tidak ada barang yang dikirim');
            }

            // $statusKelengkapan = $jumlahDiproses === count($permintaan->detail_barang) ? 'Lengkap' : 'Tidak Lengkap';

            MPengiriman::create([
                'kode_pengiriman'     => 'KRM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4)),
                'permintaan_id'       => $permintaan->id,
                'cabang_tujuan_id'    => $permintaan->cabang_id,
                'tanggal_pengiriman' => now(),
                'status_pengiriman'  => 'Dikemas',
                'status_kelengkapan' => null,
                'keterangan'         => $barangDikirim,
                'catatan_gudang'     => $request->catatan
            ]);

            // update status permintaan
            $this->updateStatusPermintaan($permintaan->id);

            DB::commit();
            return back()->with('success', 'Permintaan berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

//4. LAPORAN CATATAN PENGIRIMAN PERBULAN
    public function laporanIndex(Request $request)
    {
        $filterPeriode = $request->filter_periode ?? 'bulan';

        $tanggalAwal  = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $bulanAwal  = $request->bulan_awal ? explode('-', $request->bulan_awal) : [now()->year, now()->month];
        $bulanAkhir = $request->bulan_akhir ? explode('-', $request->bulan_akhir) : [now()->year, now()->month];

        $tahunAwal  = $request->tahun_awal ?? now()->year;
        $tahunAkhir = $request->tahun_akhir ?? now()->year;

        $query = MPengiriman::query();

        // Filter rentang periode
        switch ($filterPeriode) {
            case 'hari':
                if ($tanggalAwal && $tanggalAkhir) {
                    $query->whereBetween('tanggal_pengiriman', [$tanggalAwal, $tanggalAkhir]);
                } else {
                    $query->whereDate('tanggal_pengiriman', now());
                }
                break;

            case 'bulan':
                $startMonth = $bulanAwal[1];
                $startYear  = $bulanAwal[0];
                $endMonth   = $bulanAkhir[1];
                $endYear    = $bulanAkhir[0];

                $query->where(function($q) use ($startYear, $startMonth, $endYear, $endMonth) {
                    $q->whereYear('tanggal_pengiriman', '>', $startYear)
                    ->orWhere(function($q2) use ($startYear, $startMonth) {
                        $q2->whereYear('tanggal_pengiriman', $startYear)
                            ->whereMonth('tanggal_pengiriman', '>=', $startMonth);
                    });
                })->where(function($q) use ($endYear, $endMonth) {
                    $q->whereYear('tanggal_pengiriman', '<', $endYear)
                    ->orWhere(function($q2) use ($endYear, $endMonth) {
                        $q2->whereYear('tanggal_pengiriman', $endYear)
                            ->whereMonth('tanggal_pengiriman', '<=', $endMonth);
                    });
                });
                break;

            case 'tahun':
                $query->whereBetween(DB::raw('YEAR(tanggal_pengiriman)'), [$tahunAwal, $tahunAkhir]);
                break;

            case 'semua':
                // Tidak ada filter
                break;
        }

        // Tentukan SELECT, GROUP BY, ORDER BY berdasarkan filter
        switch ($filterPeriode) {
            case 'hari':
                $query->selectRaw('DATE(tanggal_pengiriman) as tanggal');
                $query->groupByRaw('DATE(tanggal_pengiriman)');
                $query->orderByRaw('tanggal DESC');
                break;

            case 'bulan':
                $query->selectRaw('MONTH(tanggal_pengiriman) as bulan, YEAR(tanggal_pengiriman) as tahun');
                $query->groupByRaw('YEAR(tanggal_pengiriman), MONTH(tanggal_pengiriman)');
                $query->orderByRaw('tahun DESC, bulan DESC');
                break;

            case 'tahun':
                $query->selectRaw('YEAR(tanggal_pengiriman) as tahun');
                $query->groupByRaw('YEAR(tanggal_pengiriman)');
                $query->orderByRaw('tahun DESC');
                break;

            case 'semua':
                $query->orderBy('tanggal_pengiriman', 'DESC');
                break;
        }

        // Ambil data dengan paginate
        $laporan = $query->paginate(10)->appends($request->all());

        return view('inventaris.gudangpusat.laporan', compact(
            'laporan', 'filterPeriode',
            'tanggalAwal', 'tanggalAkhir',
            'bulanAwal', 'bulanAkhir',
            'tahunAwal', 'tahunAkhir'
        ));
    }

    public function laporanDetail(Request $request, $bulan = null, $tahun = null)
    {
        $filterPeriode = $request->filter_periode ?? 'bulan';

        $bulan = $request->bulan ?? $bulan;
        $tahun = $request->tahun ?? $tahun;

        $query = MPengiriman::with('cabangTujuan');

        $barangFilter = $request->barang_id ?? [];
        if (!is_array($barangFilter)) $barangFilter = $barangFilter ? [$barangFilter] : [];

        $cabangFilter = $request->cabang_id ?? [];
        if (!is_array($cabangFilter)) {
            $cabangFilter = $cabangFilter ? [$cabangFilter] : [];
        }

        // =====================
        // FILTER PERIODE PENGIRIMAN
        // =====================
        $periodeLabel = '';

        switch ($filterPeriode) {
            case 'hari':
                $tanggalAwal  = $request->tanggal_awal ? \Carbon\Carbon::parse($request->tanggal_awal)->format('Y-m-d') : now()->format('Y-m-d');
                $tanggalAkhir = $request->tanggal_akhir ? \Carbon\Carbon::parse($request->tanggal_akhir)->format('Y-m-d') : now()->format('Y-m-d');

                $query->whereBetween('tanggal_pengiriman', [$tanggalAwal, $tanggalAkhir]);

                $periodeLabel = \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d F Y')
                                .' s/d '.
                                \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y');
                break;

            case 'bulan':
                $bulan  = $bulan ? (int)$bulan : now()->month;
                $tahun  = $tahun ? (int)$tahun : now()->year;

                $query->whereMonth('tanggal_pengiriman', $bulan)
                    ->whereYear('tanggal_pengiriman', $tahun);

                $periodeLabel = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F').' '.$tahun;
                break;

            case 'tahun':
                $tahun = $tahun ? (int)$tahun : now()->year;

                $query->whereYear('tanggal_pengiriman', $tahun);
                $periodeLabel = $tahun;
                break;

            case 'semua':
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $query->whereBetween('tanggal_pengiriman', [
                        $request->tanggal_awal,
                        $request->tanggal_akhir
                    ]);
                }
                $periodeLabel = 'Semua';
                break;

            default:
                $filterPeriode = 'bulan';
                $bulan = now()->month;
                $tahun = now()->year;

                $query->whereMonth('tanggal_pengiriman', $bulan)
                    ->whereYear('tanggal_pengiriman', $tahun);

                $periodeLabel = \Carbon\Carbon::create()
                                    ->month($bulan)
                                    ->translatedFormat('F') . ' ' . $tahun;
                break;
        }

        // filter pengiriman berdasarkan cabang tujuan
        if (!empty($cabangFilter)) {
            $query->whereIn('cabang_tujuan_id', $cabangFilter);
        }

        $pengirimanRaw = $query->orderBy('tanggal_pengiriman')->get();

        // =====================
        // PENGAMBILAN CABANG
        // =====================
        $queryPengambilan = MPengambilan::with('cabang');

        if (!empty($cabangFilter)) {
            $queryPengambilan->whereIn('cabang_id', $cabangFilter);
        }

        // 🔥 FILTER PERIODE PENGAMBILAN (disamakan dengan pengiriman)
        switch ($filterPeriode) {

            case 'hari':
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $queryPengambilan->whereBetween('tanggal', [
                        $request->tanggal_awal,
                        $request->tanggal_akhir
                    ]);
                }
                break;

            case 'bulan':
                $queryPengambilan->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
                break;

            case 'tahun':
                $queryPengambilan->whereYear('tanggal', $tahun);
                break;

            case 'semua':
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $queryPengambilan->whereBetween('tanggal', [
                        $request->tanggal_awal,
                        $request->tanggal_akhir
                    ]);
                }
                break;
        }

        $pengambilan = $queryPengambilan
            ->orderBy('tanggal')
            ->get();

        $semuaBarang = MGudangBarang::all();

        // =====================
        // FILTER PENGIRIMAN BERDASARKAN BARANG
        // =====================
        $pengiriman = collect();
        foreach ($pengirimanRaw as $kirim) {
            $detail = is_string($kirim->keterangan)
                ? json_decode($kirim->keterangan, true)
                : $kirim->keterangan;

            if (!$barangFilter) {
                $pengiriman->push($kirim);
                continue;
            }

            foreach ($detail ?? [] as $d) {
                if (in_array($d['gudang_barang_id'], $barangFilter)) {
                    $pengiriman->push($kirim);
                    break;
                }
            }
        }

        // =====================
        // CABANG
        // =====================
        if (!empty($cabangFilter)) {
            $semuaCabang = MCabang::whereIn('id', $cabangFilter)
                ->orderBy('nama')
                ->get();
        } else {
            $semuaCabang = $pengiriman->pluck('cabangTujuan')
                ->unique('id')
                ->values();
        }

        // =====================
        // REKAP
        // =====================
        $rekap = [];

        foreach ($semuaBarang as $barang) {

            if ($barangFilter && !in_array($barang->id, $barangFilter)) {
                continue;
            }

            $rekap[$barang->id] = [
                'barang' => $barang->nama_bahan,
                'satuan' => $barang->satuan,
                'cabang' => [],
                'total'  => 0
            ];

            foreach ($semuaCabang as $cabang) {
                $rekap[$barang->id]['cabang'][$cabang->id] = 0;
            }
        }

        foreach ($pengiriman as $kirim) {
            $detail = is_string($kirim->keterangan)
                ? json_decode($kirim->keterangan, true)
                : $kirim->keterangan;

            foreach ($detail ?? [] as $d) {
                $idBarang = $d['gudang_barang_id'];
                $jumlah   = (float) $d['jumlah'];

                if ($barangFilter && !in_array($idBarang, $barangFilter)) continue;
                if (!isset($rekap[$idBarang])) continue;

                $rekap[$idBarang]['cabang'][$kirim->cabang_tujuan_id] += $jumlah;
                $rekap[$idBarang]['total'] += $jumlah;
            }
        }

        $allCabang = MCabang::orderBy('nama')->get();

        // =============================
        // GABUNG DATA PENGIRIMAN + PENGAMBILAN
        // =============================
        $transaksi = collect();

        // PENGIRIMAN
        foreach ($pengiriman as $item) {

            $detail = is_string($item->keterangan)
                ? json_decode($item->keterangan, true)
                : $item->keterangan;

            foreach ($detail ?? [] as $d) {
                $transaksi->push([
                    'tanggal' => $item->tanggal_pengiriman,
                    'jenis'   => 'Pengiriman',
                    'cabang'  => $item->cabangTujuan->nama ?? '-',
                    'barang'  => $d['nama_barang'] ?? '-',
                    'qty'     => $d['jumlah'] ?? 0,
                    'satuan'  => $d['satuan'] ?? '-',
                    'ket'     => '-'
                ]);
            }
        }

        // PENGAMBILAN
        foreach ($pengambilan as $item) {

            $detail = is_string($item->list_barang)
                ? json_decode($item->list_barang, true)
                : $item->list_barang;

            foreach ($detail ?? [] as $d) {

                // 🔥 ambil field REAL dari database pengambilan
                $namaBarang = $d['barang'] ?? $d['nama_bahan'] ?? $d['nama_barang'] ?? '-';
                $atasNama   = $item->atas_nama ?? $d['atas_nama'] ?? '-';
                $ambilKe    = $item->ambil_ke ?? '-';
                $jumlah     = $d['qty'] ?? $d['jumlah'] ?? 0;
                $satuan     = $d['satuan'] ?? '-';

                $transaksi->push([
                    'tanggal' => $item->tanggal,
                    'jenis'   => 'Pengambilan',
                    'cabang'  => $item->cabang->nama ?? '-',

                    // gabung sesuai permintaanmu
                    'barang'  => $namaBarang . ' - a.n ' . $atasNama . ' Ambil ke ' . $ambilKe,

                    'qty'     => $jumlah,
                    'satuan'  => $satuan,
                    'ket'     => '-'
                ]);
            }
        }

        // =============================
        // SORT
        // =============================
        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        return view('inventaris.gudangpusat.detaillaporan', compact(
            'transaksi',
            'bulan',
            'tahun',
            'rekap',
            'semuaCabang',
            'semuaBarang',
            'periodeLabel',
            'filterPeriode',
            'allCabang'
        ));
    }

public function laporanDownload(Request $request)
{
    $transaksi = $this->getTransaksiFull($request);
    $semuaBarang = MGudangBarang::all();

    // Cabang unik dari transaksi
    $semuaCabang = $transaksi->pluck('cabang')->unique()->values()
        ->map(function($nama, $index){
            return (object)['id' => $index + 1, 'nama' => $nama];
        });

    // Rekap per barang per cabang
    $rekap = [];
    foreach ($semuaBarang as $barang) {
        $rekap[$barang->id] = [
            'barang' => $barang->nama_bahan,
            'satuan' => $barang->satuan,
            'cabang' => [],
            'total'  => 0
        ];
        foreach ($semuaCabang as $cabang) {
            $rekap[$barang->id]['cabang'][$cabang->id] = 0;
        }
    }

    foreach ($transaksi as $item) {
        foreach ($semuaBarang as $barang) {
            if (str_contains(strtolower($item['barang']), strtolower($barang->nama_bahan))) {
                $rekap[$barang->id]['total'] += $item['qty'];
                foreach ($semuaCabang as $cabang) {
                    if ($item['cabang'] == $cabang->nama) {
                        $rekap[$barang->id]['cabang'][$cabang->id] += $item['qty'];
                    }
                }
            }
        }
    }

    $pdf = PDF::loadView('inventaris.gudangpusat.laporan_pdf', [
        'pengiriman'    => $transaksi,
        'rekap'         => $rekap,
        'semuaCabang'   => $semuaCabang,
        'filterPeriode' => $request->filter_periode,
        'tanggal_awal'  => $request->tanggal_awal,
        'tanggal_akhir' => $request->tanggal_akhir,
        'bulan'         => $request->bulan,
        'tahun'         => $request->tahun,
    ]);

    return $pdf->download('laporan_pengiriman_' . now()->format('Ymd_His') . '.pdf');
}



public function laporanExcel(Request $request)
{
    $transaksi = $this->getTransaksiFull($request);
    $semuaBarang = MGudangBarang::all();

    $semuaCabang = $transaksi->pluck('cabang')->unique()->map(fn($nama) => (object)['nama' => $nama])->values();

    $rekap = [];
    foreach ($semuaBarang as $barang) {
        $rekap[$barang->id] = [
            'barang' => $barang->nama_bahan,
            'satuan' => $barang->satuan,
            'cabang' => [],
            'total'  => 0
        ];
        foreach ($semuaCabang as $cabang) {
            $rekap[$barang->id]['cabang'][$cabang->id] = 0;
        }
    }

    foreach ($transaksi as $item) {
        foreach ($semuaBarang as $barang) {
            if (str_contains(strtolower($item['barang']), strtolower($barang->nama_bahan))) {
                $rekap[$barang->id]['total'] += $item['qty'];
                foreach ($semuaCabang as $cabang) {
                    if ($item['cabang'] == $cabang->nama) {
                        $rekap[$barang->id]['cabang'][$cabang->id] += $item['qty'];
                    }
                }
            }
        }
    }

    return Excel::download(
        new LaporanPengirimanExport(
            $transaksi,
            $rekap,
            $semuaCabang,
            $request->filter_periode,
            $request->tanggal_awal,
            $request->tanggal_akhir,
            $request->bulan,
            $request->tahun
        ),
        'laporan_pengiriman_' . now()->format('Ymd_His') . '.xlsx'
    );
}


private function getTransaksiFull(Request $request)
{
    $filterPeriode = $request->filter_periode ?? 'bulan';

    // PENGIRIMAN
    $queryPengiriman = MPengiriman::with('cabangTujuan');
    if ($request->filled('cabang_id')) {
        $queryPengiriman->whereIn('cabang_tujuan_id', (array)$request->cabang_id);
    }
    switch ($filterPeriode) {
        case 'hari':
            $queryPengiriman->whereDate('tanggal_pengiriman', '>=', $request->tanggal_awal)
                             ->whereDate('tanggal_pengiriman', '<=', $request->tanggal_akhir);
            break;
        case 'bulan':
            $queryPengiriman->whereMonth('tanggal_pengiriman', $request->bulan)
                             ->whereYear('tanggal_pengiriman', $request->tahun);
            break;
        case 'tahun':
            $queryPengiriman->whereYear('tanggal_pengiriman', $request->tahun);
            break;
        case 'semua':
            if($request->tanggal_awal && $request->tanggal_akhir){
                $queryPengiriman->whereBetween('tanggal_pengiriman', [$request->tanggal_awal, $request->tanggal_akhir]);
            }
            break;
    }
    $pengiriman = $queryPengiriman->orderBy('tanggal_pengiriman')->get();

    // PENGAMBILAN
    $queryPengambilan = MPengambilan::with('cabang');
    if ($request->filled('cabang_id')) {
        $queryPengambilan->whereIn('cabang_id', (array)$request->cabang_id);
    }
    switch ($filterPeriode) {
        case 'hari':
            $queryPengambilan->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            break;
        case 'bulan':
            $queryPengambilan->whereMonth('tanggal', $request->bulan)
                             ->whereYear('tanggal', $request->tahun);
            break;
        case 'tahun':
            $queryPengambilan->whereYear('tanggal', $request->tahun);
            break;
        case 'semua':
            if($request->tanggal_awal && $request->tanggal_akhir){
                $queryPengambilan->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            }
            break;
    }
    $pengambilan = $queryPengambilan->orderBy('tanggal')->get();

    // Gabung ke format transaksi
    $transaksi = collect();

    foreach ($pengiriman as $item) {
        $detail = is_string($item->keterangan) ? json_decode($item->keterangan, true) : $item->keterangan;
        foreach ($detail ?? [] as $d) {
            $transaksi->push([
                'tanggal' => $item->tanggal_pengiriman,
                'jenis'   => 'Pengiriman',
                'cabang'  => $item->cabangTujuan->nama ?? '-',
                'barang'  => $d['nama_barang'] ?? '-',
                'qty'     => $d['jumlah'] ?? 0,
                'satuan'  => $d['satuan'] ?? '-',
                'ket'     => '-',
            ]);
        }
    }

    foreach ($pengambilan as $item) {
        $detail = is_string($item->list_barang) ? json_decode($item->list_barang, true) : $item->list_barang;
        foreach ($detail ?? [] as $d) {
            $namaBarang = $d['barang'] ?? $d['nama_bahan'] ?? $d['nama_barang'] ?? '-';
            $atasNama   = $item->atas_nama ?? $d['atas_nama'] ?? '-';
            $ambilKe    = $item->ambil_ke ?? '-';
            $jumlah     = $d['qty'] ?? $d['jumlah'] ?? 0;
            $satuan     = $d['satuan'] ?? '-';

            $transaksi->push([
                'tanggal' => $item->tanggal,
                'jenis'   => 'Pengambilan',
                'cabang'  => $item->cabang->nama ?? '-',
                'barang'  => $namaBarang . ' - a.n ' . $atasNama . ' Ambil ke ' . $ambilKe,
                'qty'     => $jumlah,
                'satuan'  => $satuan,
                'ket'     => '-',
            ]);
        }
    }

    return $transaksi->sortByDesc('tanggal')->values();
}


private function getFilteredPengiriman(Request $request)
{
    $filterPeriode = $request->filter_periode ?? 'bulan';

    $query = MPengiriman::with('cabangTujuan');

    if ($request->filled('cabang_id')) {
        $query->whereIn('cabang_tujuan_id', (array)$request->cabang_id);
    }

    switch ($filterPeriode) {
        case 'hari':
            $query->whereDate('tanggal_pengiriman', '>=', $request->tanggal_awal)
                  ->whereDate('tanggal_pengiriman', '<=', $request->tanggal_akhir);
            break;
        case 'bulan':
            $query->whereMonth('tanggal_pengiriman', $request->bulan)
                  ->whereYear('tanggal_pengiriman', $request->tahun);
            break;
        case 'tahun':
            $query->whereYear('tanggal_pengiriman', $request->tahun);
            break;
        case 'semua':
            if($request->tanggal_awal && $request->tanggal_akhir){
                $query->whereBetween('tanggal_pengiriman', [$request->tanggal_awal, $request->tanggal_akhir]);
            }
            break;
    }

    return $query->orderBy('tanggal_pengiriman')->get();
}


// 5. NOTIFIKASI
    public function getHeaderNotifications()
    {
        return MPermintaanPengiriman::with('cabang')
            ->where('status', 'Menunggu')
            ->where('created_at', '>=', Carbon::now()->subDays(3))
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    // MARK AS READ
    public function markAsRead($id)
    {
        MPermintaanPengiriman::where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

// 6. DASHBOARD
    public function dashboard()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 4 KOTAK ATAS

        // 1. Permintaan Menunggu
        $totalPermintaanMenunggu = MPermintaanPengiriman::where('status','Menunggu')->count();

        $permintaanHariIni = MPermintaanPengiriman::whereDate('created_at', $today)->count();
        $permintaanKemarin = MPermintaanPengiriman::whereDate('created_at', $yesterday)->count();
        $diffPermintaan = $permintaanHariIni - $permintaanKemarin;

        // 2. Barang dikirim
        $totalBarangDikirim = MPengiriman::where('status_pengiriman','Dikirim')->count();

        $dikirimHariIni = MPengiriman::whereDate('created_at', $today)->count();
        $dikirimKemarin = MPengiriman::whereDate('created_at', $yesterday)->count();
        $diffDikirim = $dikirimHariIni - $dikirimKemarin;

        // 3. Pengiriman tuntas
        $totalPengirimanTuntas = MPengiriman::where('status_pengiriman','Diterima')->count();

        $tuntasHariIni = MPengiriman::whereDate('created_at', $today)->count();
        $tuntasKemarin = MPengiriman::whereDate('created_at', $yesterday)->count();
        $diffTuntas = $tuntasHariIni - $tuntasKemarin;

        // 4. Total jenis barang
        $totalJenisBarang = MGudangBarang::count();

        $barangHariIni = MGudangBarang::whereDate('created_at', $today)->count();
        $barangKemarin = MGudangBarang::whereDate('created_at', $yesterday)->count();
        $diffBarang = $barangHariIni - $barangKemarin;

        // GRAFIK
        $days = [];
        $grafikPermintaan = [];
        $grafikStokDikirim = [];
        $grafikPengiriman = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // label hari (contoh: 29 Jan)
            $days[] = $date->format('d M');

            // grafik 1: permintaan masuk per hari
            $grafikPermintaan[] = MPermintaanPengiriman::whereDate('created_at', $date)->count();

            // grafik 2: total stok dikirim per hari (sum qty / jumlah_barang)
            $pengirimanHariIni = MPengiriman::whereDate('created_at', $date)->get();

            $totalJumlah = 0;

            foreach ($pengirimanHariIni as $kirim) {
                $detail = is_string($kirim->keterangan)
                    ? json_decode($kirim->keterangan, true)
                    : $kirim->keterangan;

                foreach ($detail ?? [] as $item) {
                    $totalJumlah += (float) $item['jumlah'];
                }
            }

            $grafikStokDikirim[] = $totalJumlah;

            // grafik 3: pengiriman dikirim per hari
            $grafikPengiriman[] = MPengiriman::whereDate('created_at', $date)
                ->where('status_pengiriman','Dikirim')
                ->count();

            // waktu terakhir update grafik
            $lastPermintaanUpdate = MPermintaanPengiriman::latest('created_at')->first();
            $lastStokDikirimUpdate = MPengiriman::latest('created_at')->first();
            $lastPengirimanUpdate = MPengiriman::where('status_pengiriman','Dikirim')
                ->latest('created_at')->first();

        }

        // top 6 barang tabel
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $pengirimanBulanIni = MPengiriman::with('cabangTujuan')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->where('status_pengiriman', 'Dikirim')
            ->get();

        $barangKeluar = [];

        foreach ($pengirimanBulanIni as $kirim) {

            if (!$kirim->cabangTujuan) continue;

            $namaCabang = $kirim->cabangTujuan->nama;

            foreach ($kirim->keterangan ?? [] as $item) {

                $idBarang = $item['gudang_barang_id'];
                $namaBarang = $item['nama_barang'];
                $jumlah = (int) $item['jumlah'];

                if (!isset($barangKeluar[$idBarang])) {
                    $barangKeluar[$idBarang] = [
                        'nama_barang' => $namaBarang,
                        'cabang' => [],
                        'total' => 0
                    ];
                }

                $barangKeluar[$idBarang]['total'] += $jumlah;

                // hitung per cabang
                if (!isset($barangKeluar[$idBarang]['cabang'][$namaCabang])) {
                    $barangKeluar[$idBarang]['cabang'][$namaCabang] = 0;
                }

                $barangKeluar[$idBarang]['cabang'][$namaCabang] += $jumlah;
            }
        }

        // tentukan cabang terbanyak
        foreach ($barangKeluar as &$barang) {
            arsort($barang['cabang']);
            $barang['cabang'] = array_key_first($barang['cabang']);
        }

        $topBarangKeluar = collect($barangKeluar)
            ->sortByDesc('total')
            ->take(6)
            ->values();

        // tabel notif terbaru
        $latestNotifications = MPermintaanPengiriman::with('cabang')
            ->where('status', 'Menunggu')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        return view('inventaris.gudangpusat.dashboard', compact(
            'totalPermintaanMenunggu',
            'totalBarangDikirim',
            'totalPengirimanTuntas',
            'totalJenisBarang',
            'diffPermintaan',
            'diffDikirim',
            'diffTuntas',
            'diffBarang',
            'days',
            'grafikPermintaan',
            'grafikStokDikirim',
            'grafikPengiriman',
            'lastPermintaanUpdate',
            'lastStokDikirimUpdate',
            'lastPengirimanUpdate',
            'topBarangKeluar',
            'latestNotifications'
        ));
    }
}
