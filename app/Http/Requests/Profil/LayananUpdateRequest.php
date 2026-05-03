<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class LayananUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // GLOBAL
            'wa_value' => ['nullable','string','max:255'],

            // HERO
            'hero_chip_text' => ['nullable','string','max:255'],
            'hero_chip_dot'  => ['nullable','string','max:64'],

            'hero_title_parts' => ['nullable','array','max:6'],
            'hero_title_parts.*.text'  => ['nullable','string','max:60'],
            'hero_title_parts.*.color' => ['nullable','string','max:64'],

            'hero_desc' => ['nullable','string'],
            'hero_btn1_text' => ['nullable','string','max:60'],
            'hero_btn2_text' => ['nullable','string','max:60'],
            'hero_btn2_route' => ['nullable','string','max:120'],

            // SUMMARY
            'summary_title' => ['nullable','string','max:120'],
            'summary_items' => ['nullable','array','max:8'],
            'summary_items.*.text' => ['nullable','string','max:80'],
            'summary_items.*.dot'  => ['nullable','string','max:64'],

            // WHY
            'why_title' => ['nullable','string','max:120'],
            'why_desc'  => ['nullable','string','max:255'],
            'why_cards' => ['nullable','array','max:3'],
            'why_cards.*.title'  => ['nullable','string','max:80'],
            'why_cards.*.desc'   => ['nullable','string','max:300'],
            'why_cards.*.accent' => ['nullable','string','max:64'],

            // WHY images (fixed 3)
            'why_cards.0.image' => ['nullable','image','max:4096'],
            'why_cards.1.image' => ['nullable','image','max:4096'],
            'why_cards.2.image' => ['nullable','image','max:4096'],

            // CATEGORIES (fixed 3)
            'categories' => ['nullable','array','max:3'],
            'categories.*.title' => ['nullable','string','max:80'],
            'categories.*.desc'  => ['nullable','string','max:300'],

            'categories.*.items' => ['nullable','array','max:12'],
            'categories.*.items.*.title' => ['nullable','string','max:80'],
            'categories.*.items.*.desc'  => ['nullable','string','max:300'],

            // item images (dynamic index -> validate as image if exists)
            'categories.*.items.*.image' => ['nullable','image','max:4096'],

            // CTA
            'cta_title' => ['nullable','string','max:160'],
            'cta_desc'  => ['nullable','string'],
            'cta_btn1_text' => ['nullable','string','max:60'],
            'cta_btn2_text' => ['nullable','string','max:60'],
            'cta_btn2_route' => ['nullable','string','max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // helper bersihin list array
        $cleanParts = function($arr, $textKey = 'text') {
            if (!is_array($arr)) return null;
            $arr = array_values(array_filter($arr, function($row) use ($textKey){
                if (!is_array($row)) return false;
                $t = trim((string)($row[$textKey] ?? ''));
                return $t !== '';
            }));
            return $arr;
        };

        $this->merge([
            'hero_title_parts' => $cleanParts($this->input('hero_title_parts'), 'text'),
            'summary_items'    => $cleanParts($this->input('summary_items'), 'text'),
        ]);

        // categories: rapihin item kosong
        $cats = $this->input('categories');
        if (is_array($cats)) {
            foreach ($cats as $ci => $cat) {
                $items = $cat['items'] ?? [];
                if (!is_array($items)) $items = [];

                $items = array_values(array_filter($items, function($it){
                    $t = trim((string)($it['title'] ?? ''));
                    $d = trim((string)($it['desc'] ?? ''));
                    $cur = trim((string)($it['image_current'] ?? ''));
                    return ($t !== '' || $d !== '' || $cur !== '');
                }));

                $cats[$ci]['items'] = $items;
            }
            $this->merge(['categories' => array_values($cats)]);
        }
    }
}
