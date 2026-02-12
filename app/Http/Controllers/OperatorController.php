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

        // Base Query: Hanya item yang ditugaskan ke operator yang sedang login
        // PENTING: Untuk Operator, filter utama adalah 'operator_id', bukan 'cabang_id'
        // Karena operator bisa mengerjakan tugas lembur dari cabang lain.
        $baseQuery = MSubSpk::where('operator_id', $userId)
            ->whereHas('spk', function ($query) {
                // Hanya yang sudah disetujui manajemen (atau pending jika kebijakan boleh pending)
                // Kita ambil 'acc' dan 'pending' agar operator bisa lihat tugas yg baru masuk
                $query->whereIn('status_spk', ['acc']);
            });

        // 1. Total Produksi Masuk (Semua yang pernah ditugaskan ke saya)
        $totalMasuk = (clone $baseQuery)->count();

        // 2. Total Sudah Beres (Selesai oleh saya)
        $totalSelesai = (clone $baseQuery)->where('status_produksi', 'done')->count();

        // 3. Total Belum Beres (Tugas aktif saya)
        $statusAktif = ['pending', 'ripping', 'ongoing', 'finishing'];
        $totalProses = (clone $baseQuery)->whereIn('status_produksi', $statusAktif)->count();

        // --- STATISTIK DETAIL (ONGOING / BELUM SELESAI) ---

        // A. Ongoing Reguler (Bukan Bantuan, Bukan Lembur)
        $ongoingReguler = (clone $baseQuery)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', function ($q) {
                $q->where('is_bantuan', false)->where('is_lembur', false);
            })->count();

        // B. Ongoing Bantuan (Is Bantuan = True)
        $ongoingBantuan = (clone $baseQuery)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', function ($q) {
                $q->where('is_bantuan', true);
            })->count();

        // C. Ongoing Lembur (Is Lembur = True) - BARU
        $ongoingLembur = (clone $baseQuery)->whereIn('status_produksi', $statusAktif)
            ->whereHas('spk', function ($q) {
                $q->where('is_lembur', true);
            })->count();

        return view('spk.operator.index', [
            'title' => 'Dashboard Operator - ' . $user->nama . ' (' . $user->cabang->nama . ')',
            'totalMasuk'     => $totalMasuk,
            'totalSelesai'   => $totalSelesai,
            'totalProses'    => $totalProses,
            'ongoingReguler' => $ongoingReguler,
            'ongoingBantuan' => $ongoingBantuan,
            'ongoingLembur'  => $ongoingLembur, // Kirim variabel baru
        ]);
    }
}
