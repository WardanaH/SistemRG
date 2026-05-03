<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class BerandaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kalau kamu punya policy, silakan ganti
    }

    public function rules(): array
    {
        return [
            'hero_badge_label' => ['nullable','string','max:120'],
            'hero_badge_dot'   => ['nullable','string','max:40'],

            'hero_title_parts' => ['nullable','array','max:6'],
            'hero_title_parts.*.text'  => ['nullable','string','max:40'],
            'hero_title_parts.*.color' => ['nullable','string','max:40'],

            'hero_desc' => ['nullable','string'],

            'hero_btn1_label' => ['nullable','string','max:40'],
            'hero_btn1_route' => ['nullable','string','max:100'],
            'hero_btn2_label' => ['nullable','string','max:40'],
            'hero_btn2_route' => ['nullable','string','max:100'],

            'hero_branches' => ['nullable','array','max:8'],
            'hero_branches.*' => ['nullable','string','max:60'],

            'hero_labels' => ['nullable','array','max:3'],
            'hero_labels.*.text' => ['nullable','string','max:40'],
            'hero_labels.*.color' => ['nullable','string','max:40'],

            'hero_right_small_label' => ['nullable','string','max:80'],
            'hero_right_title' => ['nullable','string','max:120'],
            'hero_right_detail_route' => ['nullable','string','max:100'],

            'hero_cats' => ['nullable','array','max:3'],
            'hero_cats.*.text' => ['nullable','string','max:40'],
            'hero_cats.*.color' => ['nullable','string','max:40'],

            'hero_ask_label' => ['nullable','string','max:80'],
            'hero_ask_wa_label' => ['nullable','string','max:30'],
            'hero_ask_contact_label' => ['nullable','string','max:30'],
            'hero_ask_contact_route' => ['nullable','string','max:100'],

            'main_cards' => ['nullable','array','max:3'],
            'main_cards.*.title' => ['nullable','string','max:80'],
            'main_cards.*.desc'  => ['nullable','string','max:300'],
            'main_cards.*.dot'   => ['nullable','string','max:40'],
            'main_cards.*.route' => ['nullable','string','max:100'],

            // images
            'hero_image' => ['nullable','image','max:4096'],
            'main_cards.0.image' => ['nullable','image','max:4096'],
            'main_cards.1.image' => ['nullable','image','max:4096'],
            'main_cards.2.image' => ['nullable','image','max:4096'],

            'why_title' => ['nullable','string','max:120'],
            'why_desc'  => ['nullable','string','max:200'],
            'why_btn1_label' => ['nullable','string','max:40'],
            'why_btn1_route' => ['nullable','string','max:100'],
            'why_btn2_label' => ['nullable','string','max:40'],
            'why_btn2_route' => ['nullable','string','max:100'],

            'why_cards' => ['nullable','array','max:3'],
            'why_cards.*.title' => ['nullable','string','max:80'],
            'why_cards.*.desc' => ['nullable','string','max:300'],
            'why_cards.*.accent' => ['nullable','string','max:40'],

            'about_title' => ['nullable','string','max:120'],
            'about_desc'  => ['nullable','string'],
            'about_btn1_label' => ['nullable','string','max:40'],
            'about_btn1_route' => ['nullable','string','max:100'],
            'about_btn2_label' => ['nullable','string','max:40'],
            'about_btn2_route' => ['nullable','string','max:100'],
            'about_branches' => ['nullable','array','max:8'],
            'about_branches.*' => ['nullable','string','max:60'],
            'about_small_text' => ['nullable','string','max:200'],

            'news_title' => ['nullable','string','max:120'],
            'news_desc'  => ['nullable','string','max:200'],
            'news_btn_label' => ['nullable','string','max:40'],
            'news_btn_route' => ['nullable','string','max:100'],

            'cta_title' => ['nullable','string','max:120'],
            'cta_desc'  => ['nullable','string'],
            'cta_wa_label' => ['nullable','string','max:30'],
            'cta_contact_label' => ['nullable','string','max:30'],
            'cta_contact_route' => ['nullable','string','max:100'],

            'ig_url' => ['nullable','url','max:255'],
            'wa_value' => ['nullable','string','max:255'],

            'colors' => ['nullable','array'],
            'colors.*' => ['nullable','string','max:40'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // bersihin array kosong biar tidak nyimpan null berantakan
        $cleanList = function ($arr) {
            if (!is_array($arr)) return null;
            $arr = array_values(array_filter($arr, fn($v) => trim((string)$v) !== ''));
            return $arr;
        };

        $parts = $this->input('hero_title_parts');
        if (is_array($parts)) {
            $parts = array_values(array_filter($parts, function ($p) {
                $t = trim((string)($p['text'] ?? ''));
                return $t !== '';
            }));
            $this->merge(['hero_title_parts' => $parts]);
        }

        $this->merge([
            'hero_branches'  => $cleanList($this->input('hero_branches')),
            'about_branches' => $cleanList($this->input('about_branches')),
        ]);
    }
}
