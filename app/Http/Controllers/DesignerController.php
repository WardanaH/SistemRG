<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();

        // 1. Total SPK Reguler yang dibuat hari ini
        $spkRegulerHariIni = \App\Models\MSpk::where('designer_id', $user->id)
            ->where('is_bantuan', false)
            ->whereDate('created_at', $today)
            ->count();

        // 2. Total SPK Bantuan yang dibuat hari ini
        $spkBantuanHariIni = \App\Models\MSpk::where('designer_id', $user->id)
            ->where('is_bantuan', true)
            ->whereDate('created_at', $today)
            ->count();

        // 3. Total Keseluruhan SPK yang pernah dibuat oleh designer ini (Opsional untuk motivasi)
        $totalSemuaSpk = \App\Models\MSpk::where('designer_id', $user->id)->count();

        return view('spk.designer.index', [
            'user' => $user,
            'title' => 'Dashboard Designer',
            'spkRegulerHariIni' => $spkRegulerHariIni,
            'spkBantuanHariIni' => $spkBantuanHariIni,
            'totalSemuaSpk' => $totalSemuaSpk,
        ]);
    }
}
