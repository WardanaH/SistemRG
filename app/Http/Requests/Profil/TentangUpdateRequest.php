<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class TentangUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // HERO
            'hero_chip' => ['nullable','string','max:255'],
            'hero_desc' => ['nullable','string'],
            'hero_btn1_label' => ['nullable','string','max:255'],
            'hero_btn1_route' => ['nullable','string','max:255'],
            'hero_btn2_label' => ['nullable','string','max:255'],
            'hero_btn2_route' => ['nullable','string','max:255'],

            'hero_title_parts' => ['nullable','array','max:6'],
            'hero_title_parts.*.text' => ['nullable','string','max:60'],
            'hero_title_parts.*.color' => ['nullable','string','max:64'],

            // FOCUS (fixed 3)
            'focus_items' => ['nullable','array','max:3'],
            'focus_items.*.label' => ['nullable','string','max:80'],
            'focus_items.*.accent' => ['nullable','string','max:64'],

            // WHY + HIGHLIGHTS
            'why_title' => ['nullable','string','max:255'],
            'why_desc' => ['nullable','string'],

            'highlights' => ['nullable','array','max:6'],
            'highlights.*.text' => ['nullable','string','max:140'],
            'highlights.*.color' => ['nullable','string','max:64'],

            // FAQ
            'faq' => ['nullable','array','max:12'],
            'faq.*.q' => ['nullable','string','max:180'],
            'faq.*.a' => ['nullable','string'],

            // OWNER
            'owner_small' => ['nullable','string','max:255'],
            'owner_title' => ['nullable','string','max:255'],
            'owner_message' => ['nullable','string'],
            'owner_name' => ['nullable','string','max:255'],
            'owner_role' => ['nullable','string','max:255'],
            'owner_photo' => ['nullable','image','max:2048'],

            // HISTORY
            'history_title' => ['nullable','string','max:255'],
            'history_desc' => ['nullable','string'],
            'history_stats' => ['nullable','array','max:8'],
            'history_stats.*.k' => ['nullable','string','max:80'],
            'history_stats.*.v' => ['nullable','string','max:200'],

            // VISION + MISSION
            'vision_title' => ['nullable','string','max:255'],
            'vision_desc' => ['nullable','string'],
            'mission_title' => ['nullable','string','max:255'],
            'mission_items' => ['nullable','array','max:12'],
            'mission_items.*' => ['nullable','string','max:180'],

            // TEAM (fixed 3)
            'leaders' => ['nullable','array','max:3'],
            'leaders.*.name' => ['nullable','string','max:120'],
            'leaders.*.role' => ['nullable','string','max:120'],
            'leaders.*.photo_upload' => ['nullable','image','max:2048'],
            'leaders.*.photo_current' => ['nullable','string','max:255'],

            // CLIENTS (multiple upload append)
            'clients' => ['nullable','array','max:40'],
            'clients.*' => ['nullable','image','max:2048'],

            // COLORS
            'colors' => ['nullable','array'],
            'colors.*' => ['nullable','string','max:64'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $trim = fn($v) => trim((string)$v);

        // hero_title_parts: buang part yang text kosong
        $parts = $this->input('hero_title_parts');
        if (is_array($parts)) {
            $parts = array_values(array_filter($parts, function ($p) use ($trim) {
                return $trim($p['text'] ?? '') !== '';
            }));
            $this->merge(['hero_title_parts' => $parts]);
        }

        // highlights: buang item text kosong
        $hl = $this->input('highlights');
        if (is_array($hl)) {
            $hl = array_values(array_filter($hl, function ($p) use ($trim) {
                return $trim($p['text'] ?? '') !== '';
            }));
            $this->merge(['highlights' => $hl]);
        }

        // faq: buang row kosong total
        $faq = $this->input('faq');
        if (is_array($faq)) {
            $faq = array_values(array_filter($faq, function ($p) use ($trim) {
                return $trim($p['q'] ?? '') !== '' || $trim($p['a'] ?? '') !== '';
            }));
            $this->merge(['faq' => $faq]);
        }

        // history_stats: buang row kosong
        $hs = $this->input('history_stats');
        if (is_array($hs)) {
            $hs = array_values(array_filter($hs, function ($p) use ($trim) {
                return $trim($p['k'] ?? '') !== '' || $trim($p['v'] ?? '') !== '';
            }));
            $this->merge(['history_stats' => $hs]);
        }

        // mission_items: buang kosong
        $mi = $this->input('mission_items');
        if (is_array($mi)) {
            $mi = array_values(array_filter($mi, fn($x) => $trim($x) !== ''));
            $this->merge(['mission_items' => $mi]);
        }

        // focus_items: tetap 3 slot (biar stabil), tapi trim label
        $fi = $this->input('focus_items');
        if (is_array($fi)) {
            foreach ($fi as $i => $row) {
                $fi[$i]['label'] = $trim($row['label'] ?? '');
            }
            $this->merge(['focus_items' => $fi]);
        }
    }
}
