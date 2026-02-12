<?php

namespace App\Http\Controllers;

use App\Models\MBahanBaku;
use Illuminate\Http\Request;

class ManajemenController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today();
        $user = auth()->user();
        $cabangId = $user->cabang_id;

        // Filter Cabang: Jika bukan pusat, batasi data hanya cabangnya sendiri
        $isPusat = $user->cabang->jenis === 'pusat';

        $querySpk = \App\Models\MSpk::query();
        $queryItem = \App\Models\MSubSpk::query();

        if (!$isPusat) {
            $querySpk->where('cabang_id', $cabangId);
            $queryItem->whereHas('spk', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        // 1. Statistik SPK Masuk Hari Ini
        $spkToday = (clone $querySpk)->whereDate('created_at', $today)->count();

        // 2. Statistik Antrian (Belum Beres) & Selesai (Sudah Produksi)
        $totalAntrian = (clone $queryItem)
            ->whereDate('updated_at', $today)
            ->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])
            ->count();

        $totalSelesai = (clone $queryItem)
            ->whereDate('updated_at', $today)
            ->where('status_produksi', 'done')
            ->count();

        // 3. Data Grafik (30 Hari Terakhir)
        $grafikData = (clone $querySpk)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return view('spk.manajemen.index', [
            'title' => 'Dashboard Admin',
            'spkToday' => $spkToday,
            'totalAntrian' => $totalAntrian,
            'totalSelesai' => $totalSelesai,
            'labels' => $grafikData->pluck('date'),
            'counts' => $grafikData->pluck('count'),
        ]);
    }
}
