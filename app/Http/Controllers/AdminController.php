<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today();
        $user = auth()->user();
        $cabangId = $user->cabang_id;
        $isPusat = $user->cabang->jenis === 'pusat';

        // Base Query Builder
        $querySpk = \App\Models\MSpk::query();
        $queryItem = \App\Models\MSubSpk::query();

        // Filter Cabang (Jika bukan pusat)
        if (!$isPusat) {
            $querySpk->where('cabang_id', $cabangId);
            $queryItem->whereHas('spk', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        // --- 1. STATISTIK HARI INI ---
        $spkRegulerToday = (clone $querySpk)->where('is_bantuan', false)->whereDate('created_at', $today)->count();
        $spkBantuanToday = (clone $querySpk)->where('is_bantuan', true)->whereDate('created_at', $today)->count();

        // --- 2. STATISTIK ANTRIAN (ITEM) ---
        // Antrian Reguler
        $antrianReguler = (clone $queryItem)->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', false))->whereDate('created_at', $today)->count();

        // Antrian Bantuan
        $antrianBantuan = (clone $queryItem)->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', true))->whereDate('created_at', $today)->count();

        // --- 3. STATISTIK SELESAI (ITEM) ---
        $selesaiReguler = (clone $queryItem)->where('status_produksi', 'done')
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', false))->whereDate('created_at', $today)->count();

        $selesaiBantuan = (clone $queryItem)->where('status_produksi', 'done')
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', true))->whereDate('created_at', $today)->count();


        // --- 4. DATA GRAFIK (30 HARI TERAKHIR) ---
        // Kita buat array tanggal dulu agar grafik tidak bolong jika ada hari tanpa order
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        // Ambil data dari DB
        $dataReguler = (clone $querySpk)->where('is_bantuan', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        $dataBantuan = (clone $querySpk)->where('is_bantuan', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        // Mapping data ke tanggal yang sudah disiapkan (agar 0 jika tidak ada data)
        $countsReguler = $dates->map(fn($date) => $dataReguler[$date] ?? 0);
        $countsBantuan = $dates->map(fn($date) => $dataBantuan[$date] ?? 0);

        return view('spk.admin.index', [
            'title' => 'Dashboard Admin - ' . $user->nama . ' ( ' . $user->cabang->nama . ' )',
            // Stats Cards
            'spkRegulerToday' => $spkRegulerToday,
            'spkBantuanToday' => $spkBantuanToday,
            'antrianReguler'  => $antrianReguler,
            'antrianBantuan'  => $antrianBantuan,
            'selesaiReguler'  => $selesaiReguler,
            'selesaiBantuan'  => $selesaiBantuan,
            // Charts
            'chartLabels'     => $dates,
            'chartReguler'    => $countsReguler,
            'chartBantuan'    => $countsBantuan,
        ]);
    }
}
