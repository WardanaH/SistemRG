<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PBeritaPage extends Model
{
    protected $table = 'p_berita_pages';

    protected $fillable = [
        'hero_chip','hero_title_1','hero_title_2','hero_title_3','hero_lead',
        'search_placeholder','search_button','tab_news','tab_edu',
        'stat1_k','stat1_v','stat2_k','stat2_v','stat3_k','stat3_v',
        'news_heading','news_desc','edu_heading','edu_desc',
        'btn_kontak','btn_layanan',
        'cta_title','cta_desc','cta_btn_wa','cta_btn_kontak',
        'article_help_text',
    ];
}
