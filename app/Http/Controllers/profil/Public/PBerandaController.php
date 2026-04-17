<?php

namespace App\Http\Controllers\Profil\Public;

use App\Http\Controllers\Controller;
use App\Models\PBeranda;
use App\Models\PBerita;

class PBerandaController extends Controller
{
    public function index()
    {
        $home = PBeranda::query()->first();

        if (!$home) {
            $home = PBeranda::query()->create(PBeranda::defaults());
        }

        // ✅ FIX: ambil 3 berita terbaru dari modul berita
        $latestNews = PBerita::query()
            ->published()
            ->where('type', 'news')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        return view('profil.public.pages.beranda', compact('home', 'latestNews'));
    }
}
