<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PBeranda extends Model
{
    protected $table = 'p_berandas';

    protected $fillable = [
        'hero_badge_label','hero_badge_dot','hero_title_parts','hero_desc',
        'hero_btn1_label','hero_btn1_route','hero_btn2_label','hero_btn2_route',
        'hero_branches',
        'hero_labels',
        'hero_right_small_label','hero_right_title','hero_right_detail_route',
        'hero_cats',
        'hero_ask_label','hero_ask_wa_label','hero_ask_contact_label','hero_ask_contact_route',
        'main_cards',
        'why_title','why_desc','why_btn1_label','why_btn1_route','why_btn2_label','why_btn2_route',
        'why_cards',
        'about_title','about_desc','about_btn1_label','about_btn1_route','about_btn2_label','about_btn2_route',
        'about_branches','about_small_text',
        'news_title','news_desc','news_btn_label','news_btn_route',
        'cta_title','cta_desc','cta_wa_label','cta_contact_label','cta_contact_route',
        'ig_url','wa_value',
        'colors',
        'hero_image',
    ];

    protected $casts = [
        'hero_title_parts' => 'array',
        'hero_branches'    => 'array',
        'hero_labels'      => 'array',
        'hero_cats'        => 'array',
        'main_cards'       => 'array',
        'why_cards'        => 'array',
        'about_branches'   => 'array',
        'colors'           => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero_badge_label' => 'Percetakan & Advertising',
            'hero_badge_dot'   => 'var(--rg-yellow)',
            'hero_title_parts' => [
                ['text' => 'Restu', 'color' => 'var(--rg-blue)'],
                ['text' => 'Guru', 'color' => 'var(--rg-yellow)'],
                ['text' => 'Promosindo', 'color' => 'var(--rg-red)'],
            ],
            'hero_desc' => 'Outdoor • Indoor • Multi. Produksi cepat, hasil rapi, dan support tim responsif.',
            'hero_btn1_label' => 'Lihat Layanan',
            'hero_btn1_route' => 'profil.layanan',
            'hero_btn2_label' => 'Konsultasi',
            'hero_btn2_route' => 'profil.kontak',
            'hero_branches' => ['Banjarmasin','Martapura','Banjarbaru','Liang Anggang'],
            'hero_labels' => [
                ['text'=>'Outdoor','color'=>'var(--rg-blue)'],
                ['text'=>'Indoor','color'=>'var(--rg-red)'],
                ['text'=>'Multi','color'=>'var(--rg-yellow)'],
            ],
            'hero_right_small_label' => 'Kebutuhan Promosi',
            'hero_right_title' => 'Pilih kategori cepat',
            'hero_right_detail_route' => 'profil.layanan',
            'hero_cats' => [
                ['text'=>'Outdoor','color'=>'var(--rg-blue)'],
                ['text'=>'Indoor','color'=>'var(--rg-red)'],
                ['text'=>'Multi','color'=>'var(--rg-yellow)'],
            ],
            'hero_ask_label' => 'Tanya cepat sekarang:',
            'hero_ask_wa_label' => 'WhatsApp',
            'hero_ask_contact_label' => 'Kontak',
            'hero_ask_contact_route' => 'profil.kontak',

            'main_cards' => [
                ['title'=>'Outdoor Advertising','desc'=>'Baliho, billboard, spanduk, neonbox, branding.','dot'=>'var(--rg-blue)','route'=>'profil.layanan','image'=>null],
                ['title'=>'Indoor Printing','desc'=>'Poster, banner, backdrop, display indoor.','dot'=>'var(--rg-red)','route'=>'profil.layanan','image'=>null],
                ['title'=>'Multi / Sticker','desc'=>'Sticker vinyl, cutting, label produk, dll.','dot'=>'var(--rg-yellow)','route'=>'profil.layanan','image'=>null],
            ],

            'why_title' => 'Kenapa Memilih Kami',
            'why_desc'  => 'Kualitas produksi, komunikasi cepat, dan pengerjaan rapi.',
            'why_btn1_label' => 'Lihat Layanan',
            'why_btn1_route' => 'profil.layanan',
            'why_btn2_label' => 'Kontak',
            'why_btn2_route' => 'profil.kontak',
            'why_cards' => [
                ['title'=>'Kualitas Produksi','desc'=>'Material terkurasi, finishing rapi, QC sebelum kirim.','accent'=>'var(--rg-blue)'],
                ['title'=>'Cepat & Tepat','desc'=>'Timeline jelas, komunikasi cepat, pengerjaan efisien.','accent'=>'var(--rg-red)'],
                ['title'=>'Support Tim','desc'=>'Dibantu dari konsep sampai file siap produksi.','accent'=>'var(--rg-yellow)'],
            ],

            'about_title' => 'Tentang Singkat',
            'about_desc' => 'Restu Guru Promosindo bergerak di bidang percetakan dan advertising. Melayani outdoor, indoor, dan multi media.',
            'about_btn1_label' => 'Baca Selengkapnya',
            'about_btn1_route' => 'profil.tentang',
            'about_btn2_label' => 'Kontak',
            'about_btn2_route' => 'profil.kontak',
            'about_branches' => ['Banjarmasin','Martapura','Banjarbaru','Liang Anggang'],
            'about_small_text' => 'Kami melayani beberapa area untuk kebutuhan promosi Anda.',

            'news_title' => 'Berita Terbaru',
            'news_desc' => 'Update project dan informasi menarik.',
            'news_btn_label' => 'Lihat semua',
            'news_btn_route' => 'profil.berita',

            'cta_title' => 'Siap mulai promosi sekarang?',
            'cta_desc' => 'Hubungi tim kami untuk konsultasi dan penawaran terbaik.',
            'cta_wa_label' => 'WhatsApp',
            'cta_contact_label' => 'Kontak',
            'cta_contact_route' => 'profil.kontak',

            'ig_url' => null,
            'wa_value' => '6281234567890',

            'colors' => [
                'blob_blue'   => 'var(--rg-blue)',
                'blob_red'    => 'var(--rg-red)',
                'blob_yellow' => 'var(--rg-yellow)',
                'soft_bg'     => '#f6f8fb',
                'link_accent' => 'var(--rg-blue)',
                'btn_primary' => 'var(--rg-blue)',
            ],
        ];
    }

    /**
     * Resolve WA value to usable wa.me link
     */
    public function waLink(): string
    {
        $v = trim((string)($this->wa_value ?? ''));
        if ($v === '') return 'https://wa.me/6281234567890';

        if (str_starts_with($v, 'http://') || str_starts_with($v, 'https://')) {
            return $v;
        }

        // assume digits
        $digits = preg_replace('/\D+/', '', $v);
        if ($digits === '') return 'https://wa.me/6281234567890';
        return 'https://wa.me/' . $digits;
    }
}
