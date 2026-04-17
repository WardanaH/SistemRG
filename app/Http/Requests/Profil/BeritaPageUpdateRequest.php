<?php

namespace App\Http\Requests\Profil;

use Illuminate\Foundation\Http\FormRequest;

class BeritaPageUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'hero_chip' => ['required','string','max:80'],
            'hero_title_1' => ['required','string','max:80'],
            'hero_title_2' => ['required','string','max:80'],
            'hero_title_3' => ['required','string','max:80'],
            'hero_lead' => ['required','string','max:220'],

            'search_placeholder' => ['required','string','max:120'],
            'search_button' => ['required','string','max:30'],
            'tab_news' => ['required','string','max:30'],
            'tab_edu' => ['required','string','max:30'],

            'stat1_k' => ['required','string','max:40'],
            'stat1_v' => ['required','string','max:120'],
            'stat2_k' => ['required','string','max:40'],
            'stat2_v' => ['required','string','max:120'],
            'stat3_k' => ['required','string','max:40'],
            'stat3_v' => ['required','string','max:120'],

            'news_heading' => ['required','string','max:80'],
            'news_desc' => ['required','string','max:220'],
            'edu_heading' => ['required','string','max:80'],
            'edu_desc' => ['required','string','max:240'],

            'btn_kontak' => ['required','string','max:40'],
            'btn_layanan' => ['required','string','max:40'],

            'cta_title' => ['required','string','max:120'],
            'cta_desc' => ['required','string','max:240'],
            'cta_btn_wa' => ['required','string','max:40'],
            'cta_btn_kontak' => ['required','string','max:40'],

            'article_help_text' => ['required','string','max:120'],
        ];
    }
}
