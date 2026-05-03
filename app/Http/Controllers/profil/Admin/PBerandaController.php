<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profil\BerandaUpdateRequest;
use App\Models\PBeranda;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PBerandaController extends Controller
{
    public function edit()
    {
        $home = PBeranda::query()->first();
        if (!$home) $home = PBeranda::query()->create(PBeranda::defaults());

        // routes untuk dropdown
        $routes = [
            ['label' => '— Pilih Link —', 'value' => ''],
            ['label' => 'Beranda', 'value' => 'profil.beranda'],
            ['label' => 'Layanan', 'value' => 'profil.layanan'],
            ['label' => 'Tentang', 'value' => 'profil.tentang'],
            ['label' => 'Berita', 'value' => 'profil.berita'],
            ['label' => 'Kontak', 'value' => 'profil.kontak'],
        ];

        return view('profil.admin.pages.beranda.edit', compact('home','routes'));
    }

    public function update(BerandaUpdateRequest $request)
    {
        $home = PBeranda::query()->first();
        if (!$home) $home = PBeranda::query()->create(PBeranda::defaults());

        $data = $request->validated();

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('profil/beranda', 'public');
            // optional: hapus lama
            if ($home->hero_image) Storage::disk('public')->delete($home->hero_image);
            $data['hero_image'] = $path;
        } else {
            unset($data['hero_image']);
        }

        // Handle main cards images (nested)
        $mainCards = $data['main_cards'] ?? ($home->main_cards ?? []);
        if (!is_array($mainCards)) $mainCards = [];

        for ($i = 0; $i < 3; $i++) {
            $file = $request->file("main_cards.$i.image");
            if ($file) {
                $path = $file->store('profil/beranda', 'public');

                // hapus lama kalau ada
                $old = Arr::get($home->main_cards, "$i.image");
                if ($old) Storage::disk('public')->delete($old);

                $mainCards[$i] = array_merge($mainCards[$i] ?? [], ['image' => $path]);
            } else {
                // kalau tidak upload, jangan hilangkan image lama
                $existing = Arr::get($home->main_cards, "$i.image");
                if ($existing) {
                    $mainCards[$i] = array_merge($mainCards[$i] ?? [], ['image' => $existing]);
                }
            }
        }

        $data['main_cards'] = $mainCards;

        // Fill and save
        $home->fill($data);
        $home->save();

        return redirect()
            ->route('profil.admin.beranda.edit')
            ->with('success', 'Beranda berhasil disimpan.');
    }
}
