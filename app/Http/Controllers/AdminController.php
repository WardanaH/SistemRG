<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use App\Models\MSubSpk;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user = auth()->user();
        $cabangId = $user->cabang_id;
        $isPusat = $user->cabang->jenis === 'pusat';

        // Base Query Builder
        $querySpk = MSpk::query();
        $queryItem = MSubSpk::query();

        // Filter Cabang (Jika bukan pusat)
        if (!$isPusat) {
            $querySpk->where('cabang_id', $cabangId);
            $queryItem->whereHas('spk', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        // --- 1. STATISTIK HARI INI (SPK MASUK) ---
        // Reguler: Bukan Bantuan DAN Bukan Lembur
        $spkRegulerToday = (clone $querySpk)->where('is_bantuan', false)->where('is_lembur', false)->whereDate('created_at', $today)->count();
        $spkBantuanToday = (clone $querySpk)->where('is_bantuan', true)->whereDate('created_at', $today)->count();
        $spkLemburToday  = (clone $querySpk)->where('is_lembur', true)->whereDate('created_at', $today)->count(); // BARU

        // --- 2. STATISTIK ANTRIAN (ITEM SEDANG DIPROSES) ---
        // Status: pending, ripping, ongoing, finishing
        $statusAktif = ['pending', 'ripping', 'ongoing', 'finishing'];

        // Antrian Reguler
        $antrianReguler = (clone $queryItem)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', false)->where('is_lembur', false))
            ->whereDate('created_at', $today)->count();

        // Antrian Bantuan
        $antrianBantuan = (clone $queryItem)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', true))
            ->whereDate('created_at', $today)->count();

        // Antrian Lembur (BARU)
        $antrianLembur = (clone $queryItem)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', fn($q) => $q->where('is_lembur', true))
            ->whereDate('created_at', $today)->count();


        // --- 3. STATISTIK SELESAI (ITEM DONE) ---
        // Selesai Reguler
        $selesaiReguler = (clone $queryItem)->where('status_produksi', 'done')
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', false)->where('is_lembur', false))
            ->whereDate('created_at', $today)->count();

        // Selesai Bantuan
        $selesaiBantuan = (clone $queryItem)->where('status_produksi', 'done')
            ->whereHas('spk', fn($q) => $q->where('is_bantuan', true))
            ->whereDate('created_at', $today)->count();

        // Selesai Lembur (BARU)
        $selesaiLembur = (clone $queryItem)->where('status_produksi', 'done')
            ->whereHas('spk', fn($q) => $q->where('is_lembur', true))
            ->whereDate('created_at', $today)->count();


        // --- 4. DATA GRAFIK (30 HARI TERAKHIR) ---
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        // Grafik Reguler
        $dataReguler = (clone $querySpk)->where('is_bantuan', false)->where('is_lembur', false)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        // Grafik Bantuan
        $dataBantuan = (clone $querySpk)->where('is_bantuan', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        // Grafik Lembur (BARU)
        $dataLembur = (clone $querySpk)->where('is_lembur', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')->pluck('count', 'date');

        // Mapping Data (Isi 0 jika kosong)
        $countsReguler = $dates->map(fn($date) => $dataReguler[$date] ?? 0);
        $countsBantuan = $dates->map(fn($date) => $dataBantuan[$date] ?? 0);
        $countsLembur  = $dates->map(fn($date) => $dataLembur[$date] ?? 0);

        return view('spk.admin.index', [
            'title' => 'Dashboard Admin - ' . $user->nama . ' ( ' . $user->cabang->nama . ' )',

            // Stats Hari Ini
            'spkRegulerToday' => $spkRegulerToday,
            'spkBantuanToday' => $spkBantuanToday,
            'spkLemburToday'  => $spkLemburToday,

            // Stats Antrian
            'antrianReguler'  => $antrianReguler,
            'antrianBantuan'  => $antrianBantuan,
            'antrianLembur'   => $antrianLembur,

            // Stats Selesai
            'selesaiReguler'  => $selesaiReguler,
            'selesaiBantuan'  => $selesaiBantuan,
            'selesaiLembur'   => $selesaiLembur,

            // Charts
            'chartLabels'     => $dates,
            'chartReguler'    => $countsReguler,
            'chartBantuan'    => $countsBantuan,
            'chartLembur'     => $countsLembur,
        ]);
    }
}
