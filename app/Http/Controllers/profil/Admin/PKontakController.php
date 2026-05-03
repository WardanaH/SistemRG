<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Models\PKontakBranche;
use App\Models\PKontakPage;
use Illuminate\Http\Request;

class PKontakController extends Controller
{
    public function edit()
    {
        $page = PKontakPage::query()->first();
        if (!$page) {
            $page = PKontakPage::create(PKontakPage::defaults());
        }

        $branches = PKontakBranche::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('profil.admin.pages.kontak.edit', compact('page','branches'));
    }

    public function update(Request $request)
    {
        $page = PKontakPage::query()->first();
        if (!$page) {
            $page = PKontakPage::create(PKontakPage::defaults());
        }

        $data = $request->validate([
            // HERO
            'hero_chip' => ['required','string','max:120'],
            'hero_title' => ['required','string','max:180'],
            'hero_lead' => ['nullable','string'],
            'hero_btn_wa_label' => ['required','string','max:80'],
            'hero_btn2_label' => ['required','string','max:80'],
            'hero_btn2_route' => ['required','string','max:120'],

            // PANEL
            'panel_title' => ['required','string','max:120'],
            'panel_desc'  => ['nullable','string'],

            // PILLS
            'hero_pills' => ['nullable','array'],
            'hero_pills.*.text' => ['nullable','string','max:60'],
            'hero_pills.*.color' => ['nullable','string','max:40'],

            // STATS
            'stats' => ['nullable','array'],
            'stats.*.k' => ['nullable','string','max:60'],
            'stats.*.v' => ['nullable','string','max:120'],
            'stats.*.accent' => ['nullable','string','max:40'],

            // CABANG SECTION
            'branches_heading' => ['required','string','max:120'],
            'branches_desc' => ['required','string','max:180'],
            'branch_open_label' => ['required','string','max:30'],

            // MAP
            'map_heading' => ['required','string','max:120'],
            'map_desc' => ['required','string','max:180'],
            'map_fallback' => ['required','string','max:220'],

            // HELP
            'help_title' => ['required','string','max:120'],
            'help_btn_wa' => ['required','string','max:80'],
            'help_btn2_label' => ['required','string','max:80'],
            'help_btn2_route' => ['required','string','max:120'],

            // CTA
            'cta_title' => ['required','string','max:180'],
            'cta_desc' => ['nullable','string'],
            'cta_btn_wa' => ['required','string','max:80'],
            'cta_btn_back' => ['required','string','max:80'],
            'cta_btn_back_route' => ['required','string','max:120'],

            // BRANCHES ROWS
            'branches' => ['nullable','array'],
            'branches.*.id' => ['nullable','integer'],
            'branches.*.name' => ['nullable','string','max:120'],
            'branches.*.address' => ['nullable','string','max:220'],
            'branches.*.maps_url' => ['nullable','string','max:255'],
            'branches.*.lat' => ['nullable'],
            'branches.*.lng' => ['nullable'],
            'branches.*.is_active' => ['nullable'],
            'branches.*.sort_order' => ['nullable','integer','min:0','max:9999'],
        ]);

        // normalize pills (ensure 3)
        $pills = $data['hero_pills'] ?? [];
        if (!is_array($pills)) $pills = [];
        for ($i=0; $i<3; $i++) {
            if (!isset($pills[$i]) || !is_array($pills[$i])) $pills[$i] = [];
            $pills[$i]['text']  = $pills[$i]['text']  ?? '';
            $pills[$i]['color'] = $pills[$i]['color'] ?? 'var(--rg-blue)';
        }
        $data['hero_pills'] = $pills;

        // normalize stats (ensure 3)
        $stats = $data['stats'] ?? [];
        if (!is_array($stats)) $stats = [];
        $defaultAcc = ['var(--rg-blue)','var(--rg-red)','var(--rg-yellow)'];
        for ($i=0; $i<3; $i++) {
            if (!isset($stats[$i]) || !is_array($stats[$i])) $stats[$i] = [];
            $stats[$i]['k'] = $stats[$i]['k'] ?? '';
            $stats[$i]['v'] = $stats[$i]['v'] ?? '';
            $stats[$i]['accent'] = $stats[$i]['accent'] ?? ($defaultAcc[$i] ?? 'var(--rg-blue)');
        }
        $data['stats'] = $stats;

        // update page first
        $page->update($data);

        // branches upsert
        $incoming = $request->input('branches', []);
        if (!is_array($incoming)) $incoming = [];

        $keepIds = [];

        foreach ($incoming as $row) {
            if (!is_array($row)) continue;

            $name = trim((string)($row['name'] ?? ''));
            $address = trim((string)($row['address'] ?? ''));
            $mapsUrl = trim((string)($row['maps_url'] ?? ''));

            // skip empty rows
            if ($name === '' && $address === '' && $mapsUrl === '') {
                continue;
            }

            $id = isset($row['id']) ? (int)$row['id'] : null;

            $lat = $row['lat'] ?? null;
            $lng = $row['lng'] ?? null;

            $lat = is_numeric($lat) ? (float)$lat : null;
            $lng = is_numeric($lng) ? (float)$lng : null;

            $isActive = isset($row['is_active']) ? (bool)$row['is_active'] : false;
            $sortOrder = isset($row['sort_order']) && is_numeric($row['sort_order']) ? (int)$row['sort_order'] : 0;

            if ($id) {
                $branch = PKontakBranche::query()->find($id);
                if ($branch) {
                    $branch->update([
                        'name' => $name !== '' ? $name : ($branch->name ?? ''),
                        'address' => $address,
                        'maps_url' => $mapsUrl,
                        'lat' => $lat,
                        'lng' => $lng,
                        'is_active' => $isActive,
                        'sort_order' => $sortOrder,
                    ]);
                    $keepIds[] = $branch->id;
                    continue;
                }
            }

            $branch = PKontakBranche::create([
                'name' => $name !== '' ? $name : 'Cabang',
                'address' => $address,
                'maps_url' => $mapsUrl,
                'lat' => $lat,
                'lng' => $lng,
                'is_active' => $isActive,
                'sort_order' => $sortOrder,
            ]);
            $keepIds[] = $branch->id;
        }

        // optional: hapus branch yang tidak terkirim lagi (biar sinkron)
        // kalau kamu gak mau auto-delete, hapus block ini.
        PKontakBranche::query()
            ->when(count($keepIds) > 0, fn($q) => $q->whereNotIn('id', $keepIds))
            ->when(count($keepIds) === 0, fn($q) => $q) // delete all if no keep
            ->delete();

        return redirect()->route('profil.admin.kontak.edit')->with('success', 'Kontak berhasil disimpan.');
    }
}
