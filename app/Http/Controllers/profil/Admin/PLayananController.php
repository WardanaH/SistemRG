<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profil\LayananUpdateRequest;
use App\Models\PLayanan;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PLayananController extends Controller
{
    public function edit()
    {
        $s = PLayanan::query()->first();
        if (!$s) $s = PLayanan::query()->create(PLayanan::defaults());

        $routes = [
            ['label' => '— Pilih Link —', 'value' => ''],
            ['label' => 'Beranda', 'value' => 'profil.beranda'],
            ['label' => 'Layanan', 'value' => 'profil.layanan'],
            ['label' => 'Tentang', 'value' => 'profil.tentang'],
            ['label' => 'Berita',  'value' => 'profil.berita'],
            ['label' => 'Kontak',  'value' => 'profil.kontak'],
        ];

        return view('profil.admin.pages.layanan.edit', compact('s','routes'));
    }

    public function update(LayananUpdateRequest $request)
    {
        $s = PLayanan::query()->first();
        if (!$s) $s = PLayanan::query()->create(PLayanan::defaults());

        $data = $request->validated();

        // =========================
        // WHY cards images (3 fixed)
        // =========================
        $whyCards = $data['why_cards'] ?? ($s->why_cards ?? []);
        if (!is_array($whyCards)) $whyCards = [];

        for ($i=0; $i<3; $i++) {
            $existing = Arr::get($s->why_cards, "$i.image");
            $whyCards[$i] = array_merge($whyCards[$i] ?? [], ['image' => $existing]);

            $file = $request->file("why_cards.$i.image");
            if ($file) {
                $path = $file->store('profil/layanan/why', 'public');

                if ($existing) Storage::disk('public')->delete($existing);
                $whyCards[$i]['image'] = $path;
            }
        }

        $data['why_cards'] = $whyCards;

        // =========================
        // Categories items images
        // fixed 3 category, items repeatable
        // =========================
        $cats = $data['categories'] ?? ($s->categories ?? []);
        if (!is_array($cats)) $cats = [];

        // pastikan minimal 3 slot (biar tidak error)
        for ($ci=0; $ci<3; $ci++) {
            if (!isset($cats[$ci]) || !is_array($cats[$ci])) $cats[$ci] = [];
            if (!isset($cats[$ci]['items']) || !is_array($cats[$ci]['items'])) $cats[$ci]['items'] = [];

            foreach ($cats[$ci]['items'] as $ii => $item) {
                $current = Arr::get($item, 'image_current');
                $existingDb = Arr::get($s->categories, "$ci.items.$ii.image");

                // prioritas: current hidden > existing DB
                $keep = $current ?: $existingDb;

                $cats[$ci]['items'][$ii] = array_merge($item ?? [], ['image' => $keep]);

                $file = $request->file("categories.$ci.items.$ii.image");
                if ($file) {
                    $path = $file->store("profil/layanan/categories/$ci", 'public');

                    if ($keep) Storage::disk('public')->delete($keep);
                    $cats[$ci]['items'][$ii]['image'] = $path;
                }

                // bersihin field form-only
                unset($cats[$ci]['items'][$ii]['image_current']);
            }
        }

        // drop kategori di luar 3 (kalau ada)
        $cats = array_slice(array_values($cats), 0, 3);
        $data['categories'] = $cats;

        // =========================
        // Simpan
        // =========================
        $s->fill($data);
        $s->save();

        return redirect()
            ->route('profil.admin.layanan.edit')
            ->with('success', 'Layanan berhasil disimpan.');
    }
}
