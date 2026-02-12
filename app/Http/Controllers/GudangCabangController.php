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
use App\Models\MAmbilAntar;
use App\Models\MPengambilan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Events\NotifikasiInventaris;
use Carbon\Carbon;
use PDF;

class GudangCabangController extends Controller
{

// 1. BARANG
    public function barang(Request $request)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $query = MGudangBarang::leftJoin('cabang_barangs', function ($join) use ($cabang) {
                $join->on('gudang_barangs.id', '=', 'cabang_barangs.gudang_barang_id')
                    ->where('cabang_barangs.cabang_id', $cabang->id);
            })
            ->select(
                'gudang_barangs.*',
                DB::raw('IFNULL(cabang_barangs.stok, 0) as stok_cabang')
            );

        // 🔎 SEARCH NAMA BARANG
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('gudang_barangs.nama_bahan', 'like', '%' . $search . '%');

            // hasil paling relevan di atas
            $query->orderByRaw("CASE
                WHEN gudang_barangs.nama_bahan LIKE '%$search%' THEN 0
                ELSE 1
            END");
        }

        $datas = $query->orderBy('gudang_barangs.nama_bahan')
                    ->paginate(10)
                    ->withQueryString();

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

            $catatan_terima = $request->keterangan_terima;

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

                    $cabangId = $pengiriman->cabang_tujuan_id;
                    $cabangBarang = MCabangBarang::firstOrCreate(
                        [
                            'cabang_id'        => $cabangId,
                            'gudang_barang_id' => $barang->id,
                        ],
                        [
                            'stok' => 0
                        ]
                    );

                    $cabangBarang->stok += (float) $diterima['jumlah'];
                    $cabangBarang->save();
                }
            }

            $fotoPath = $request->file('foto')
                ->store('penerimaan', 'public');

            $detailTerima = [];

            foreach ($barangDiterima as $item) {
                if (empty($item['checked'])) continue;

                $barang = MGudangBarang::find($item['gudang_barang_id']);
                if (!$barang) continue;

                $detailTerima[] = [
                    'gudang_barang_id' => $barang->id,
                    'nama_barang'     => $barang->nama_bahan,
                    'jumlah'          => (float) $item['jumlah'],
                    'satuan'          => $barang->satuan,
                    'keterangan'      => $item['keterangan'] ?? null
                ];
            }

            $pengiriman->update([
                'status_pengiriman'  => 'Diterima',
                'status_kelengkapan' => $statusKelengkapan,
                'tanggal_diterima'   => now(),
                'foto_penerimaan'    => $fotoPath,
                'keterangan_terima'  => $detailTerima,
                'catatan_terima'     => $catatan_terima
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

//4. LAPORAN
    public function laporanIndex(Request $request)
    {
        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $filterPeriode = $request->filter_periode ?? 'bulan';

        $tanggalAwal  = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $bulanAwal  = $request->bulan_awal ? explode('-', $request->bulan_awal) : [now()->year, now()->month];
        $bulanAkhir = $request->bulan_akhir ? explode('-', $request->bulan_akhir) : [now()->year, now()->month];

        $tahunAwal  = $request->tahun_awal ?? now()->year;
        $tahunAkhir = $request->tahun_akhir ?? now()->year;

        // Tentukan start & end datetime
        switch ($filterPeriode) {
            case 'hari':
                $start = $tanggalAwal ? Carbon::parse($tanggalAwal)->startOfDay() : now()->startOfDay();
                $end   = $tanggalAkhir ? Carbon::parse($tanggalAkhir)->endOfDay() : now()->endOfDay();
                break;
            case 'bulan':
                $start = Carbon::create($bulanAwal[0], $bulanAwal[1])->startOfMonth();
                $end   = Carbon::create($bulanAkhir[0], $bulanAkhir[1])->endOfMonth();
                break;
            case 'tahun':
                $start = Carbon::create($tahunAwal)->startOfYear();
                $end   = Carbon::create($tahunAkhir)->endOfYear();
                break;
            case 'semua':
                $start = null;
                $end = null;
                break;
        }

        // Ambil pengiriman & pengambilan
        $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            ->where('status_pengiriman', 'Diterima')
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal_diterima', [$start, $end]))
            ->selectRaw('DATE(tanggal_diterima) as tanggal')
            ->get();

        $pengambilan = MPengambilan::where('cabang_id', $cabang->id)
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->selectRaw('DATE(tanggal) as tanggal')
            ->get();

        // Merge kedua collection
        $laporanCollection = $pengiriman->merge($pengambilan)
            ->unique('tanggal') // <-- pastikan tiap tanggal cuma 1 row
            ->sortByDesc('tanggal')
            ->values();

        // Pagination manual
        $page = request()->get('page', 1);
        $perPage = 10;
        $laporan = new \Illuminate\Pagination\LengthAwarePaginator(
            $laporanCollection->forPage($page, $perPage),
            $laporanCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        $pengirimanQuery = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            ->where('status_pengiriman', 'Diterima')
            ->selectRaw('MONTH(tanggal_diterima) as bulan, YEAR(tanggal_diterima) as tahun');

        if ($start && $end) {
            $pengirimanQuery->whereBetween('tanggal_diterima', [$start, $end]);
        }

        $pengambilanQuery = MPengambilan::where('cabang_id', $cabang->id)
            ->selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun');

        if ($start && $end) {
            $pengambilanQuery->whereBetween('tanggal', [$start, $end]);
        }

        // union
        $laporanQuery = $pengirimanQuery->union($pengambilanQuery);

        $laporan = DB::table(DB::raw("({$laporanQuery->toSql()}) as sub"))
            ->mergeBindings($laporanQuery->getQuery())
            ->select('bulan', 'tahun')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->paginate(10)
            ->appends($request->all());


        return view('inventaris.gudangcabang.laporan.laporan', compact(
            'laporan',
            'cabang',
            'filterPeriode',
            'tanggalAwal',
            'tanggalAkhir',
            'bulanAwal',
            'bulanAkhir',
            'tahunAwal',
            'tahunAkhir'
        ));
    }

    public function laporanDetail(Request $request)
    {
        $filterPeriode = $request->filter_periode ?? 'bulan';
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        $tanggalAwal = $request->tanggal_awal ?? null;
        $tanggalAkhir = $request->tanggal_akhir ?? null;

        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        // =====================
        // PENGIRIMAN
        // =====================
        $query = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            ->where('status_pengiriman', 'Diterima');

        switch ($filterPeriode) {
            case 'hari':
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $query->whereBetween('tanggal_diterima', [
                        $request->tanggal_awal,
                        $request->tanggal_akhir
                    ]);
                }
                break;
            case 'bulan':
                $query->whereMonth('tanggal_diterima', $bulan)
                    ->whereYear('tanggal_diterima', $tahun);
                break;
            case 'tahun':
                $query->whereYear('tanggal_diterima', $tahun);
                break;
        }

        $pengiriman = $query->get();

        // =====================
        // PENGAMBILAN
        // =====================
        $pengambilanQuery = MPengambilan::where('cabang_id', $cabang->id);

        switch ($filterPeriode) {
            case 'hari':
                if ($request->tanggal_awal && $request->tanggal_akhir) {
                    $pengambilanQuery->whereBetween('tanggal', [
                        Carbon::parse($request->tanggal_awal)->startOfDay(),
                        Carbon::parse($request->tanggal_akhir)->endOfDay()
                    ]);
                }
                break;
            case 'bulan':
                $pengambilanQuery->whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun);
                break;
            case 'tahun':
                $pengambilanQuery->whereYear('tanggal', $tahun);
                break;
        }

        $pengambilan = $pengambilanQuery->get();

        // =====================
        // GABUNG SEMUA TRANSAKSI
        // =====================
        $transaksi = collect();

        // ===== PENGIRIMAN =====
        foreach ($pengiriman as $item) {

            $detail = is_string($item->keterangan_terima)
                ? json_decode($item->keterangan_terima, true)
                : $item->keterangan_terima;

            foreach ($detail ?? [] as $d) {
                $transaksi->push([
                    'tanggal' => Carbon::parse($item->tanggal_diterima)->format('Y-m-d'),
                    'jenis'   => 'Pengiriman',
                    'cabang'  => $cabang->nama,
                    'barang'  => $d['nama_barang'] ?? '-',
                    'qty'     => $d['jumlah'] ?? 0,
                    'satuan'  => $d['satuan'] ?? '-',
                    'ket'     => $d['keterangan'] ?? '-'
                ]);
            }
        }

        // ===== PENGAMBILAN =====
        foreach ($pengambilan as $item) {

            $detail = is_string($item->list_barang)
                ? json_decode($item->list_barang, true)
                : $item->list_barang;

            foreach ($detail ?? [] as $d) {

                $namaBarang = $d['nama_barang'] ?? $d['nama_bahan'] ?? '-';
                $atasNama   = $item->atas_nama ?? $d['atas_nama'] ?? '-';
                $ambilKe    = $item->ambil_ke ?? '-';
                $jumlah     = $d['jumlah'] ?? $d['qty'] ?? 0;
                $satuan     = $d['satuan'] ?? '-';

                $transaksi->push([
                    'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                    'jenis'   => 'Pengambilan',
                    'cabang'  => $cabang->nama,
                    'barang'  => $namaBarang . ' - a.n ' . $atasNama . ' Ambil ke ' . $ambilKe,
                    'qty'     => $jumlah,
                    'satuan'  => $satuan,
                    'ket'     => '-'
                ]);
            }
        }

        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        // =====================
        // REKAP TOTAL PER BARANG
        // =====================
        $semuaBarang = MGudangBarang::all();
        $rekap = [];

        foreach ($transaksi as $item) {
            $key = $item['barang'];
            if (!isset($rekap[$key])) {
                $rekap[$key] = [
                    'barang' => $item['barang'],
                    'satuan' => $item['satuan'],
                    'total'  => 0
                ];
            }
            $rekap[$key]['total'] += (float) $item['qty'];
        }

        // Ambil barang yang dipilih dari request
        $barangFilter = $request->barang_id ?? [];

        // Jika ada filter barang, filter $transaksi
        if (!empty($barangFilter)) {
            $transaksi = $transaksi->filter(function($item) use ($barangFilter) {
                // $item['barang'] bisa ada tambahan " - a.n ..." untuk pengambilan
                // kita cek apakah ID barang ada di nama barang (bisa sesuaikan)
                foreach ($barangFilter as $idBarang) {
                    // ambil nama barang dari semuaBarang untuk ID ini
                    $namaBarang = MGudangBarang::find($idBarang)->nama_bahan ?? null;
                    if ($namaBarang && str_contains($item['barang'], $namaBarang)) {
                        return true;
                    }
                }
                return false;
            })->values();
        }

        return view('inventaris.gudangcabang.laporan.detaillaporan', [
            'transaksi'   => $transaksi,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'cabang'      => $cabang,
            'semuaBarang' => $semuaBarang,
            'rekap'       => $rekap,
            'filterPeriode' => $filterPeriode,
            'tanggalAwal'   => $tanggalAwal,
            'tanggalAkhir'  => $tanggalAkhir,
        ]);
    }

    public function laporanDownload(Request $request)
    {
        $filterPeriode = $request->filter_periode ?? 'bulan';
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $user = Auth::user();
        $cabang = MCabang::findOrFail($user->cabang_id);

        $query = MPengiriman::where('cabang_tujuan_id', $cabang->id)
            ->where('status_pengiriman', 'Diterima');

        $pengambilanQuery = MPengambilan::where('cabang_id', $cabang->id);

        // FILTER BULAN
        $query->whereMonth('tanggal_diterima', $bulan)
            ->whereYear('tanggal_diterima', $tahun);

        $pengambilanQuery->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        $pengiriman = $query->get();
        $pengambilan = $pengambilanQuery->get();

        $transaksi = collect();

        /*
        |--------------------------------------------------------------------------
        | FORMAT PENGIRIMAN
        |--------------------------------------------------------------------------
        */
        foreach ($pengiriman as $item) {

            $detail = is_string($item->keterangan_terima)
                ? json_decode($item->keterangan_terima, true)
                : $item->keterangan_terima;

            foreach ($detail ?? [] as $d) {
                $transaksi->push([
                    'tanggal' => $item->tanggal_diterima,
                    'jenis'   => 'Pengiriman',
                    'barang'  => $d['nama_barang'] ?? '-',
                    'jumlah'  => $d['jumlah'] ?? 0,
                    'satuan'  => $d['satuan'] ?? '-',
                    'keterangan' => $d['keterangan'] ?? '-',
                    'asal_tujuan' => $item->cabangAsal->nama ?? 'Gudang Pusat'
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FORMAT PENGAMBILAN
        |--------------------------------------------------------------------------
        */
        foreach ($pengambilan as $item) {

            $detail = is_string($item->list_barang)
                ? json_decode($item->list_barang, true)
                : $item->list_barang;

            foreach ($detail ?? [] as $d) {
                $transaksi->push([
                    'tanggal' => $item->tanggal,
                    'jenis'   => 'Pengambilan',
                    'barang'  => $d['nama_barang'] ?? '-',
                    'jumlah'  => $d['jumlah'] ?? 0,
                    'satuan'  => $d['satuan'] ?? '-',
                    'keterangan' => $d['atas_nama'] ?? '-',
                    'asal_tujuan' => $item->ambil_ke ?? '-'
                ]);
            }
        }

        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        /*
        |--------------------------------------------------------------------------
        | REKAP
        |--------------------------------------------------------------------------
        */
        $rekap = [];

        foreach ($transaksi as $item) {

            $key = $item['barang'];

            if (!isset($rekap[$key])) {
                $rekap[$key] = [
                    'barang' => $item['barang'],
                    'satuan' => $item['satuan'],
                    'total'  => 0
                ];
            }

            $rekap[$key]['total'] += (float) $item['jumlah'];
        }

        $pdf = PDF::loadView('inventaris.gudangcabang.laporan.laporan_pdf', [
            'transaksi' => $transaksi,
            'rekap'     => $rekap,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'cabang'    => $cabang
        ]);

        return $pdf->download(
            'laporan_cabang_'.$cabang->nama.'_'.$bulan.'_'.$tahun.'.pdf'
        );
    }

    public function laporanExcel(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $cabangId = auth()->user()->cabang_id;

        $data = $this->getDataTransaksi($cabangId, $bulan, $tahun);

        return Excel::download(
            new GudangCabangLaporanExport(
                $data['transaksi'],
                $data['rekap'],
                $bulan,
                $tahun,
                auth()->user()->cabang
            ),
            'Laporan_Penerimaan.xlsx'
        );
    }

    private function getDataTransaksi($cabangId, $bulan, $tahun)
    {
        $transaksi = [];
        $rekap = [];

        // safety
        $bulan = $bulan ?: now()->month;
        $tahun = $tahun ?: now()->year;

        if (!$cabangId) {
            return [
                'transaksi' => collect(),
                'rekap' => []
            ];
        }

        /* =============================
        PENGIRIMAN (DITERIMA)
        ============================== */
        $pengiriman = MPengiriman::where('cabang_tujuan_id', $cabangId)
            ->whereMonth('tanggal_diterima', $bulan)
            ->whereYear('tanggal_diterima', $tahun)
            ->where('status_pengiriman', 'Diterima')
            ->get();

        foreach ($pengiriman as $item) {
            $detail = $item->keterangan_terima;
            if (!is_array($detail)) continue;

            foreach ($detail as $d) {
                $transaksi[] = [
                    'tanggal' => $item->tanggal_diterima,
                    'jenis' => 'Pengiriman',
                    'barang' => $d['nama_barang'] ?? '-',
                    'jumlah' => $d['jumlah'] ?? 0,
                    'satuan' => $d['satuan'] ?? '-',
                    'keterangan' => $d['keterangan'] ?? '-',
                    'asal_tujuan' => $item->cabangAsal->nama ?? 'Gudang Pusat'
                ];

                $key = ($d['nama_barang'] ?? '-') . '_' . ($d['satuan'] ?? '-');

                if (!isset($rekap[$key])) {
                    $rekap[$key] = [
                        'barang' => $d['nama_barang'] ?? '-',
                        'satuan' => $d['satuan'] ?? '-',
                        'total' => 0
                    ];
                }

                $rekap[$key]['total'] += $d['jumlah'] ?? 0;
            }
        }

        /* =============================
        PENGAMBILAN
        ============================== */
        $pengambilan = MPengambilan::where('cabang_id', $cabangId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        foreach ($pengambilan as $item) {
            $listBarang = is_array($item->list_barang) ? $item->list_barang : json_decode($item->list_barang, true);

            if (!is_array($listBarang)) continue;

            foreach ($listBarang as $barang) {
                $namaBarang = $barang['nama_barang'] ?? '-';
                $jumlah = $barang['jumlah'] ?? 0;
                $satuan = $barang['satuan'] ?? '-';

                if (!empty($barang['atas_nama'])) {
                    $namaBarang .= ' - ' . $barang['atas_nama'];
                }

                $transaksi[] = [
                    'tanggal' => $item->tanggal,
                    'jenis' => 'Pengambilan',
                    'barang' => $namaBarang,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan,
                    'keterangan' => '-', // bisa dikosongkan atau custom
                    'asal_tujuan' => $item->ambil_ke ?? '-',
                ];

                $key = $barang['nama_barang'] ?? '-';

                if (!isset($rekap[$key])) {
                    $rekap[$key] = [
                        'barang' => $key,
                        'satuan' => $satuan,
                        'total' => 0
                    ];
                }

                $rekap[$key]['total'] += $jumlah;
            }
        }

        return [
            'transaksi' => collect($transaksi)->sortByDesc('tanggal')->values(),
            'rekap' => collect($rekap)->values()->toArray()
        ];
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
    public function inventarisIndex(Request $request)
    {
        $cabangId = Auth::user()->cabang_id;

        $query = MInventarisCabang::where('cabang_id', $cabangId);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', '%' . $search . '%')
                ->orWhere('nama_barang', 'like', '%' . $search . '%');
            });

            $query->orderByRaw("CASE
                WHEN kode_barang LIKE '%$search%' THEN 0
                WHEN nama_barang LIKE '%$search%' THEN 0
                ELSE 1
            END");
        }

        $data = $query->latest()
                    ->paginate(10)
                    ->withQueryString();

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

// 8. AMBIL ANTAR (SEMENTARA GA DIPAKE)
    //ambil
    public function ambilIndex()
    {
        $cabangId = Auth::user()->cabang_id;

        $cabangs = MCabang::where('id', '!=', $cabangId)->get();

        $permintaan = MAmbilAntar::where('jenis', 'Ambil')
            ->where('cabang_pengirim_id', $cabangId)
            ->latest()
            ->get();

        return view('inventaris.gudangcabang.ambil', compact('permintaan', 'cabangs'));
    }

    public function ambilStore(Request $request)
    {
        $request->validate([
            'cabang_tujuan_id' => 'required',
            'tanggal' => 'required|date',
            'atas_nama' => 'required',
            'keterangan' => 'required|array'
        ]);

        MAmbilAntar::create([
            'kode' => 'AMB-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'cabang_pengirim_id' => Auth::user()->cabang_id,
            'cabang_tujuan_id'   => $request->cabang_tujuan_id,
            'jenis' => 'Ambil',
            'tanggal' => $request->tanggal,
            'atas_nama' => $request->atas_nama,
            'keterangan' => array_values($request->keterangan),
            'status' => 'Menunggu'
        ]);

        return back()->with('success', 'Permintaan ambil dibuat');
    }

    public function ambilDetail($id)
    {
        $data = MAmbilAntar::findOrFail($id);
        return view('inventaris.gudangcabang.ambil-detail', compact('data'));
    }

    public function ambilEdit($id)
    {
        $data = MAmbilAntar::where('status', 'Menunggu')->findOrFail($id);
        return view('inventaris.gudangcabang.ambil-edit', compact('data'));
    }

    public function ambilUpdate(Request $request, $id)
    {
        MAmbilAntar::findOrFail($id)->update([
            'tanggal' => $request->tanggal,
            'atas_nama' => $request->atas_nama,
            'keterangan' => array_values($request->keterangan),
        ]);

        return redirect()->route('gudangcabang.ambil.index')
            ->with('success', 'Data diperbarui');
    }

    public function ambilDestroy($id)
    {
        MAmbilAntar::where('status', 'Menunggu')->findOrFail($id)->delete();
        return back()->with('success', 'Data dihapus');
    }

    public function ambilTerima(Request $request, $id)
    {
        $request->validate([
            'barang_diterima' => 'required|array',
            'foto_bukti'      => 'required|image|max:2048',
        ]);

        $data = MAmbilAntar::where('status', 'Dikirim')->findOrFail($id);

        // upload foto
        $fotoPath = $request->file('foto_bukti')
            ->store('ambil-antar', 'public');

        $data->update([
            'keterangan_diterima' => array_values($request->barang_diterima),
            'bukti_foto'          => $fotoPath,
            'status'              => 'Diterima',
        ]);

        return back()->with('success', 'Barang berhasil diterima');
    }

    // antar
    public function antarIndex()
    {
        $cabangId = Auth::user()->cabang_id;

        $permintaan = MAmbilAntar::where('jenis', 'Ambil')
            ->where('cabang_tujuan_id', $cabangId)
            ->where('status', 'Menunggu')
            ->latest()
            ->get();

        return view('inventaris.gudangcabang.antar', compact('permintaan'));
    }

    public function antarKirim($id)
    {
        MAmbilAntar::findOrFail($id)->update([
            'status' => 'Dikirim'
        ]);

        return back()->with('success', 'Barang dikirim');
    }

    public function antarTerima(Request $request, $id)
    {
        $request->validate([
            'keterangan_diterima' => 'required|array',
            'bukti_foto' => 'required|image|max:2048'
        ]);

        $foto = $request->file('bukti_foto')
            ->store('ambil-antar', 'public');

        MAmbilAntar::findOrFail($id)->update([
            'keterangan_diterima' => $request->keterangan_diterima,
            'bukti_foto' => $foto,
            'status' => 'Diterima'
        ]);

        return back()->with('success', 'Barang diterima');
    }


// 9. PENGAMBILAN
    public function pengambilanIndex()
    {
        $cabangId = Auth::user()->cabang_id;
        $datas = MPengambilan::where('cabang_id', $cabangId)
            ->latest()
            ->paginate(10);

        return view('inventaris.gudangcabang.pengambilan', compact('datas'));
    }


    public function pengambilanStore(Request $request)
    {
        $request->validate([
            'ambil_ke'   => 'required|string',
            'tanggal'    => 'required|date',
            'list_barang'=> 'required|array',
            'list_barang.*.nama_barang' => 'required|string',
            'list_barang.*.jumlah'     => 'required|numeric|min:1',
            'list_barang.*.atas_nama'  => 'required|string',
            'foto'       => 'nullable|image|max:2048',
        ]);

        $fotoPath = $request->file('foto')?->store('pengambilan', 'public');

        MPengambilan::create([
            'cabang_id'    => Auth::user()->cabang_id,
            'ambil_ke'     => $request->ambil_ke,
            'tanggal'      => $request->tanggal,
            'list_barang'  => $request->list_barang,
            'foto'         => $fotoPath,
        ]);

        return back()->with('success', 'Data pengambilan berhasil disimpan');
    }

    public function pengambilanEdit($id)
    {
        $data = MPengambilan::findOrFail($id);
        return response()->json($data);
    }

    public function pengambilanUpdate(Request $request, $id)
    {
        $request->validate([
            'ambil_ke'   => 'required|string',
            'tanggal'    => 'required|date',
            // 'atas_nama'  => 'required|string',
            'list_barang'=> 'required|array',
            'list_barang.*.nama_barang' => 'required|string',
            'list_barang.*.jumlah'     => 'required|numeric|min:1',
            'foto'       => 'nullable|image|max:2048',
        ]);

        $data = MPengambilan::findOrFail($id);

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('pengambilan', 'public');
            $data->foto = $fotoPath;
        }

        $data->update([
            'ambil_ke'    => $request->ambil_ke,
            'tanggal'     => $request->tanggal,
            // 'atas_nama'   => $request->atas_nama,
            'list_barang' => $request->list_barang,
        ]);

        return response()->json(['success' => true]);
    }

    public function pengambilanDestroy($id)
    {
        MPengambilan::findOrFail($id)->delete();
        return back()->with('success', 'Data pengambilan berhasil dihapus');
    }

}
