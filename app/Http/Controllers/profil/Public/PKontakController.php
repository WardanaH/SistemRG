<?php

namespace App\Http\Controllers\Profil\Public;

use App\Http\Controllers\Controller;
use App\Models\PBeranda;
use App\Models\PKontakBranche;
use App\Models\PKontakPage;

class PKontakController extends Controller
{
    public function index()
    {
        // beranda dipakai untuk waLink (konsisten)
        $home = PBeranda::query()->first();
        if (!$home) {
            $home = PBeranda::create(PBeranda::defaults());
        }
        $waLink = $home->waLink();

        // page setting kontak
        $page = PKontakPage::query()->first();
        if (!$page) {
            $page = PKontakPage::create(PKontakPage::defaults());
        }

        // branches (aktif, urut)
        $branches = PKontakBranche::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('profil.public.pages.kontak', compact('home','waLink','page','branches'));
    }
}
