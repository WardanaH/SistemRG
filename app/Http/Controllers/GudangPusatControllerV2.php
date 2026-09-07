<?php

namespace App\Http\Controllers;

use App\Models\DetailPermintaan;
use App\Models\MGudangBarang;
use App\Models\PermintaanBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangPusatControllerV2 extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        // Jumlah permintaan masuk hari ini
        $permintaanHariIni = PermintaanBarang::whereDate('created_at', $today)->count();

        // Jumlah permintaan nang balum di-ACC (menunggu_acc)
        $belumDiproses = PermintaanBarang::where('status_permintaan', 'menunggu_acc')->count();

        // Jumlah pengiriman hari ini (dihitung dari status dikirim nang diupdate hari ini)
        $dikirimHariIni = PermintaanBarang::where('status_permintaan', 'dikirim')
            ->whereDate('updated_at', $today)
            ->count();

        return view('distribusi.gudang_pusat.dashboard', compact(
            'permintaanHariIni',
            'belumDiproses',
            'dikirimHariIni'
        ));
    }

    public function permintaan()
    {
        return view('distribusi.gudang_pusat.permintaan.index');
    }

    public function permintaanMasuk()
    {
        // Ambil data permintaan nang statusnya masih 'menunggu_acc'
        $permintaan = PermintaanBarang::with(['detailPermintaan.barangPusat', 'gudang'])
            ->where('status_permintaan', 'menunggu_acc')
            ->orderBy('created_at', 'asc') // Urutkan dari nang paling lawas masuk
            ->get();

        return view('distribusi.gudang_pusat.permintaan.masuk', compact('permintaan'));
    }

    public function prosesPermintaan(Request $request, $id)
    {
        // Amun admin manekan tombol "Tolak"
        if ($request->action == 'tolak') {
            PermintaanBarang::where('id', $id)->update(['status_permintaan' => 'ditolak']);
            return back()->with('success', 'Permintaan berhasil ditolak.');
        }

        try {
            DB::beginTransaction();

            $permintaan = PermintaanBarang::with('detailPermintaan')->findOrFail($id);

            // 1. Ubah status jadi dikirim
            $permintaan->update([
                'status_permintaan' => 'dikirim'
            ]);

            // 2. Otomatis update jumlah disetujui sama lawan jumlah diminta
            foreach ($permintaan->detailPermintaan as $detail) {
                $detail->update([
                    'jumlah_disetujui' => $detail->jumlah_diminta
                ]);
            }

            DB::commit();

            event(new \App\Events\NotifikasiCabangV2(
                $permintaan->id,
                'Permintaan barang Anda telah di-ACC dan sedang dikirim.'
            ));

            return back()->with('success', 'Permintaan berhasil di-ACC. Barang statusnya jadi dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mamproses: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        // buat query untuk filter
        $query = PermintaanBarang::with(['detailPermintaan.barangPusat', 'gudang']);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_permintaan', $request->status);
        }

        // Filter Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Pencarian Teks (Kode Permintaan)
        if ($request->filled('search')) {
            $query->where('kode_permintaan', 'like', '%' . $request->search . '%');
        }

        $riwayat = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('distribusi.gudang_pusat.permintaan.riwayat', compact('riwayat'));
    }

    public function laporan(Request $request)
    {
        $periode = $request->periode ?? '1_bulan';

        $query = PermintaanBarang::with(['detailPermintaan.barangPusat', 'gudang'])
            ->orderBy('created_at', 'desc');

        // Pencarian Kode Permintaan (Tambahan Hanyar)
        if ($request->filled('search')) {
            $query->where('kode_permintaan', 'like', '%' . $request->search . '%');
        }

        // Terapkan logika filter periode
        if ($periode === 'custom') {
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        } else {
            $now = now();
            $start = now();

            switch ($periode) {
                case '1_bulan':
                    $start = now()->subMonth();
                    break;
                case '3_bulan':
                    $start = now()->subMonths(3);
                    break;
                case '6_bulan':
                    $start = now()->subMonths(6);
                    break;
                case '1_tahun':
                    $start = now()->subYear();
                    break;
            }

            $query->whereBetween('created_at', [$start, $now]);
        }

        $permintaan = $query->paginate(10)->withQueryString();

        return view('distribusi.gudang_pusat.laporanPermintaan', compact('permintaan'));
    }

    public function laporanBarang(Request $request)
    {
        $query = DetailPermintaan::with(['permintaan.gudang', 'barangPusat']);

        if ($request->filled('search')) {
            $query->whereHas('barangPusat', function ($q) use ($request) {
                $q->where('nama_bahan', 'like', '%' . $request->search . '%');
            });
        }

        // Hitung total keseluruhan
        $totalDiminta = $query->sum('jumlah_diminta');
        $totalDikirim = $query->sum('jumlah_disetujui');
        $totalDiterima = $query->sum('jumlah_diterima');

        // Bikin rekap per cabang amun ada pencarian
        $rekapPerCabang = collect();
        if ($request->filled('search')) {
            // Ambil semua data (tanpa paginate) gasan dihitung per cabang
            $allData = (clone $query)->get();

            $rekapPerCabang = $allData->groupBy(function ($item) {
                return optional($item->permintaan->gudang)->nama ?? 'Cabang Tidak Diketahui';
            })->map(function ($group) {
                return (object) [
                    'diminta' => $group->sum('jumlah_diminta'),
                    'dikirim' => $group->sum('jumlah_disetujui'),
                    'diterima' => $group->sum('jumlah_diterima'),
                ];
            });
        }

        $laporan = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('distribusi.gudang_pusat.laporanBahan', compact(
            'laporan',
            'totalDiminta',
            'totalDikirim',
            'totalDiterima',
            'rekapPerCabang' // Passing variabel baru ke view
        ));
    }
}
