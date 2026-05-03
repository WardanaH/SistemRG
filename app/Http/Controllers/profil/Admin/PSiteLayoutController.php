<?php

namespace App\Http\Controllers\Profil\Admin;

use App\Http\Controllers\Controller;
use App\Models\PSiteLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PSiteLayoutController extends Controller
{
    private function layout(): PSiteLayout
    {
        return PSiteLayout::firstOrCreate(
            ['id' => 1],
            ['navbar' => [], 'footer' => []]
        );
    }

    public function editNavbar()
    {
        $layout = $this->layout();
        return view('profil.admin.pages.site_layout.navbar_edit', compact('layout'));
    }

    public function updateNavbar(Request $request)
    {
        $layout = $this->layout();

        $navbar = $request->input('navbar', []);
        if (!is_array($navbar)) $navbar = [];

        // upload logo (optional)
        if ($request->hasFile('navbar_logo')) {
            $file = $request->file('navbar_logo');

            $path = $file->store('profil/site/navbar', 'public');

            // hapus lama kalau ada
            $old = $layout->navbar['logo_path'] ?? null;
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $navbar['logo_path'] = $path;
        } else {
            // keep existing logo if not uploading
            $navbar['logo_path'] = $navbar['logo_path'] ?? ($layout->navbar['logo_path'] ?? null);
        }

        // normalize arrays
        $navbar['menu'] = isset($navbar['menu']) && is_array($navbar['menu']) ? array_values($navbar['menu']) : [];
        $navbar['brand_parts'] = isset($navbar['brand_parts']) && is_array($navbar['brand_parts']) ? array_values($navbar['brand_parts']) : [];

        $layout->update(['navbar' => $navbar]);

        return back()->with('success', 'Navbar berhasil disimpan.');
    }

    public function editFooter()
    {
        $layout = $this->layout();
        return view('profil.admin.pages.site_layout.footer_edit', compact('layout'));
    }

    public function updateFooter(Request $request)
    {
        $layout = $this->layout();

        $footer = $request->input('footer', []);
        if (!is_array($footer)) $footer = [];

        $footer['services'] = isset($footer['services']) && is_array($footer['services']) ? array_values($footer['services']) : [];
        $footer['branches'] = isset($footer['branches']) && is_array($footer['branches']) ? array_values($footer['branches']) : [];
        $footer['socials']  = isset($footer['socials'])  && is_array($footer['socials'])  ? array_values($footer['socials'])  : [];
        $footer['brand_parts'] = isset($footer['brand_parts']) && is_array($footer['brand_parts']) ? array_values($footer['brand_parts']) : [];

        $layout->update(['footer' => $footer]);

        return back()->with('success', 'Footer berhasil disimpan.');
    }
}
