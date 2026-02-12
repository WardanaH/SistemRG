<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();

        // 1. Total SPK Reguler Hari Ini
        // Syarat: Bukan Bantuan DAN Bukan Lembur
        $spkRegulerHariIni = MSpk::where('designer_id', $user->id)
            ->where('is_bantuan', false)
            ->where('is_lembur', false) // Tambahkan ini agar tidak tercampur
            ->whereDate('created_at', $today)
            ->count();

        // 2. Total SPK Bantuan Hari Ini
        $spkBantuanHariIni = MSpk::where('designer_id', $user->id)
            ->where('is_bantuan', true)
            ->whereDate('created_at', $today)
            ->count();

        // 3. Total SPK Lembur Hari Ini (BARU)
        $spkLemburHariIni = MSpk::where('designer_id', $user->id)
            ->where('is_lembur', true)
            ->whereDate('created_at', $today)
            ->count();

        // 4. Total Keseluruhan SPK
        $totalSemuaSpk = MSpk::where('designer_id', $user->id)->count();

        return view('spk.designer.index', [
            'user' => $user,
            'title' => 'Dashboard Designer - ' . $user->nama . ' (' . $user->cabang->nama . ')',
            'spkRegulerHariIni' => $spkRegulerHariIni,
            'spkBantuanHariIni' => $spkBantuanHariIni,
            'spkLemburHariIni'  => $spkLemburHariIni, // Kirim variabel baru
            'totalSemuaSpk'     => $totalSemuaSpk,
        ]);
    }
}
