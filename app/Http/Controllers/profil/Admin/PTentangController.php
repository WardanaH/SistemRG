<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profil\TentangUpdateRequest;
use App\Models\PTentang;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PTentangController extends Controller
{
    public function edit()
    {
        $about = PTentang::query()->first();
        if (!$about) $about = PTentang::query()->create([]);

        $routes = [
            ['label' => '— Pilih Link —', 'value' => ''],
            ['label' => 'Beranda', 'value' => 'profil.beranda'],
            ['label' => 'Layanan', 'value' => 'profil.layanan'],
            ['label' => 'Tentang', 'value' => 'profil.tentang'],
            ['label' => 'Berita',  'value' => 'profil.berita'],
            ['label' => 'Kontak',  'value' => 'profil.kontak'],
        ];

        return view('profil.admin.pages.tentang.edit', compact('about', 'routes'));
    }

    public function update(TentangUpdateRequest $request)
    {
        $about = PTentang::query()->first();
        if (!$about) $about = PTentang::query()->create([]);

        $data = $request->validated();

        // =========================
        // OWNER PHOTO (replace)
        // =========================
        if ($request->hasFile('owner_photo')) {
            $path = $request->file('owner_photo')->store('profil/tentang/owner', 'public');

            if (!empty($about->owner_photo)) {
                Storage::disk('public')->delete($about->owner_photo);
            }

            $data['owner_photo'] = $path;
        } else {
            // jangan overwrite jika tidak upload
            unset($data['owner_photo']);
        }

        // =========================
        // LEADERS (fixed 3)
        // keep current / replace by upload
        // =========================
        $leaders = $request->input('leaders', $about->leaders ?? []);
        if (!is_array($leaders)) $leaders = [];

        for ($i = 0; $i < 3; $i++) {
            $row = $leaders[$i] ?? [];
            $current = $row['photo_current'] ?? Arr::get($about->leaders, "$i.photo");

            $leaders[$i] = [
                'name' => $row['name'] ?? '',
                'role' => $row['role'] ?? '',
                'photo' => $current,
            ];

            if ($request->hasFile("leaders.$i.photo_upload")) {
                $newPath = $request->file("leaders.$i.photo_upload")->store('profil/tentang/leaders', 'public');

                // hapus foto lama kalau ada
                if (!empty($current)) {
                    Storage::disk('public')->delete($current);
                }

                $leaders[$i]['photo'] = $newPath;
            }
        }

        $data['leaders'] = $leaders;

        // =========================
        // CLIENTS (append, multiple)
        // =========================
        $clients = $about->clients ?? [];
        if (!is_array($clients)) $clients = [];

        if ($request->hasFile('clients')) {
            foreach ($request->file('clients') as $file) {
                if (!$file) continue;
                $clients[] = $file->store('profil/tentang/clients', 'public');
            }
        }

        $data['clients'] = $clients;

        // =========================
        // SAVE
        // =========================
        $about->fill($data);
        $about->save();

        return redirect()
            ->route('profil.admin.tentang.edit')
            ->with('success', 'Halaman Tentang berhasil disimpan.');
    }

    /**
     * Hapus 1 logo client berdasarkan index array
     * - file di storage ikut dihapus
     * - item di DB array clients dihapus
     */
    public function deleteClient(int $index)
    {
        $about = PTentang::query()->first();
        if (!$about) {
            return redirect()->route('profil.admin.tentang.edit')
                ->with('success', 'Tidak ada data Tentang.');
        }

        $clients = $about->clients ?? [];
        if (!is_array($clients)) $clients = [];

        if (!array_key_exists($index, $clients)) {
            return redirect()->route('profil.admin.tentang.edit')
                ->with('success', 'Logo client tidak ditemukan.');
        }

        $path = $clients[$index] ?? null;

        // hapus file
        if (!empty($path)) {
            Storage::disk('public')->delete($path);
        }

        // hapus dari array
        unset($clients[$index]);
        $clients = array_values($clients);

        $about->clients = $clients;
        $about->save();

        return redirect()
            ->route('profil.admin.tentang.edit')
            ->with('success', 'Logo client berhasil dihapus.');
    }
}
