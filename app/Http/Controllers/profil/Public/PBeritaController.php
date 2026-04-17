<?php

namespace App\Http\Controllers\Profil\Public;

use App\Http\Controllers\Controller;
use App\Models\PBeranda;
use App\Models\PBerita;
use App\Models\PBeritaPage;
use Illuminate\Http\Request;

class PBeritaController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'news'); // news|education
        $q = trim((string)$request->get('q', ''));

        $rows = PBerita::query()
            ->published()
            ->where('type', $type)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%")
                      ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $home = PBeranda::query()->first();
        $waLink = $home ? $home->waLink() : 'https://wa.me/6281234567890';

        // ✅ setting halaman berita
        $page = PBeritaPage::query()->first();
        if (!$page) $page = PBeritaPage::create([]);

        return view('profil.public.pages.berita', compact('rows','type','q','waLink','page'));
    }

    public function show(string $slug)
    {
        $post = PBerita::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $home = PBeranda::query()->first();
        $waLink = $home ? $home->waLink() : 'https://wa.me/6281234567890';

        // ✅ setting halaman berita (dipakai CTA + teks bantuan)
        $page = PBeritaPage::query()->first();
        if (!$page) $page = PBeritaPage::create([]);

        return view('profil.public.pages.berita-show', compact('post','waLink','page'));
    }
}
