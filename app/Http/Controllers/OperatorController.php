<?php

namespace App\Http\Controllers;

use App\Models\MSubSpk;
use App\Models\User;
use Illuminate\Http\Request;

class OperatorController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;
        $cabangId = $user->cabang_id;

        // Base Query: Hanya item yang ditugaskan ke operator yang sedang login
        $baseQuery = MSubSpk::where('operator_id', $userId)
            ->whereHas('spk', function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId)
                    ->where('status_spk', 'acc'); // Hanya yang sudah disetujui manajemen
            });

        // 1. Total Produksi Masuk (Semua yang pernah ditugaskan ke saya)
        $totalMasuk = (clone $baseQuery)->count();

        // 2. Total Sudah Beres (Selesai oleh saya)
        $totalSelesai = (clone $baseQuery)->where('status_produksi', 'done')->count();

        // 3. Total Belum Beres (Tugas aktif saya)
        $totalProses = (clone $baseQuery)->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])->count();

        // Statistik Detail: Ongoing Reguler vs Bantuan (Khusus jatah saya)
        $ongoingReguler = (clone $baseQuery)->where('status_produksi', '!=', 'done')
            ->whereHas('spk', function ($q) {
                $q->where('is_bantuan', false);
            })->count();

        $ongoingBantuan = (clone $baseQuery)->where('status_produksi', '!=', 'done')
            ->whereHas('spk', function ($q) {
                $q->where('is_bantuan', true);
            })->count();

        return view('spk.operator.index', [
            'title' => 'Dashboard Saya',
            'totalMasuk' => $totalMasuk,
            'totalSelesai' => $totalSelesai,
            'totalProses' => $totalProses,
            'ongoingReguler' => $ongoingReguler,
            'ongoingBantuan' => $ongoingBantuan,
        ]);
    }
}
