<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profil\BeritaStoreRequest;
use App\Http\Requests\Profil\BeritaUpdateRequest;
use App\Http\Requests\Profil\BeritaPageUpdateRequest;
use App\Models\PBerita;
use App\Models\PBeritaPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PBeritaController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type'); // optional
        $q    = trim((string)$request->get('q', ''));

        $rows = PBerita::query()
            ->when($type, fn($qq) => $qq->where('type', $type))
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

        return view('profil.admin.pages.berita.index', compact('rows','type','q'));
    }

    public function create()
    {
        return view('profil.admin.pages.berita.create');
    }

    public function store(BeritaStoreRequest $request)
    {
        $data = $request->validated();

        // slug unique (aman)
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'] ?? '');

        // cover
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('profil/berita', 'public');
        }

        // published_at fallback
        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        PBerita::create($data);

        return redirect()->route('profil.admin.berita.index')->with('success', 'Berita berhasil dibuat.');
    }

    public function edit(PBerita $beritum)
    {
        $berita = $beritum;
        return view('profil.admin.pages.berita.edit', compact('berita'));
    }

    public function update(BeritaUpdateRequest $request, PBerita $beritum)
    {
        $berita = $beritum;
        $data = $request->validated();

        // slug unique (exclude current)
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'] ?? '', $berita->id);

        // cover remove
        if (!empty($data['remove_cover'])) {
            if ($berita->cover) Storage::disk('public')->delete($berita->cover);
            $data['cover'] = null;
        } else {
            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->store('profil/berita', 'public');
                if ($berita->cover) Storage::disk('public')->delete($berita->cover);
                $data['cover'] = $path;
            } else {
                unset($data['cover']);
            }
        }

        unset($data['cover_current'], $data['remove_cover']);

        // published_at fallback
        if (empty($data['published_at'])) {
            $data['published_at'] = $berita->published_at ?? now();
        }

        $berita->update($data);

        return redirect()->route('profil.admin.berita.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function destroy(PBerita $beritum)
    {
        $berita = $beritum;
        if ($berita->cover) Storage::disk('public')->delete($berita->cover);
        $berita->delete();

        return redirect()->route('profil.admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }

    // ==========================================
    // ✅ NEW: EDIT HALAMAN BERITA (SETTING PAGE)
    // ==========================================
    public function editHalaman()
    {
        $page = PBeritaPage::query()->first();

        if (!$page) {
            $page = PBeritaPage::create([]); // pakai default dari migration
        }

        return view('profil.admin.pages.berita.halaman', compact('page'));
    }

    public function updateHalaman(BeritaPageUpdateRequest $request)
    {
        $page = PBeritaPage::query()->first();

        if (!$page) {
            $page = PBeritaPage::create([]);
        }

        $page->update($request->validated());

        return redirect()->route('profil.admin.berita.halaman.edit')->with('success', 'Halaman Berita berhasil disimpan.');
    }

    private function uniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = trim((string)$slug);
        $base = $base !== '' ? Str::slug($base) : Str::slug($title);

        if ($base === '') $base = 'berita';

        $try = $base;
        $i = 2;

        while (
            PBerita::query()
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $try)
                ->exists()
        ) {
            $try = $base . '-' . $i;
            $i++;
        }

        return $try;
    }
}
