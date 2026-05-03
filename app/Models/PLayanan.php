<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PLayanan extends Model
{
    protected $table = 'p_layanan';

    protected $fillable = [
        'hero_chip_text','hero_chip_dot','hero_title_parts','hero_desc',
        'hero_btn1_text','hero_btn2_text','hero_btn2_route',

        'summary_title','summary_items',

        'why_title','why_desc','why_cards',

        'categories',

        'cta_title','cta_desc','cta_btn1_text','cta_btn2_text','cta_btn2_route',

        'wa_value',
    ];

    protected $casts = [
        'hero_title_parts' => 'array',
        'summary_items'    => 'array',
        'why_cards'        => 'array',
        'categories'       => 'array',
    ];

    public static function defaults(): array
    {
        return [
            // HERO
            'hero_chip_text' => 'Layanan',
            'hero_chip_dot'  => 'var(--rg-blue)',
            'hero_title_parts' => [
                ['text' => 'Layanan',        'color' => 'var(--rg-blue)'],
                ['text' => 'Restu Guru',     'color' => 'var(--rg-yellow)'],
                ['text' => 'Promosindo',     'color' => 'var(--rg-red)'],
            ],
            'hero_desc' => 'Outdoor • Indoor • Multi. Produksi cepat, hasil rapi, dan support tim responsif.',
            'hero_btn1_text' => 'WhatsApp',
            'hero_btn2_text' => 'Kontak',
            'hero_btn2_route' => 'profil.kontak',

            // SUMMARY
            'summary_title' => 'Ringkasan',
            'summary_items' => [
                ['text' => 'Outdoor Advertising',      'dot' => 'var(--rg-blue)'],
                ['text' => 'Indoor Printing',          'dot' => 'var(--rg-red)'],
                ['text' => 'Multi (Stiker & Kecil)',   'dot' => 'var(--rg-yellow)'],
            ],

            // WHY
            'why_title' => 'Kenapa Memilih Kami',
            'why_desc'  => 'Kualitas produksi, komunikasi cepat, dan pengerjaan rapi.',
            'why_cards' => [
                ['title'=>'Kualitas Produksi','desc'=>'Material terkurasi, finishing rapi, QC sebelum kirim.','accent'=>'var(--rg-blue)','image'=>null],
                ['title'=>'Cepat & Tepat','desc'=>'Timeline jelas, komunikasi cepat, pengerjaan efisien.','accent'=>'var(--rg-red)','image'=>null],
                ['title'=>'Support Tim','desc'=>'Dibantu dari konsep sampai file siap produksi.','accent'=>'var(--rg-yellow)','image'=>null],
            ],

            // CATEGORIES (fixed 3)
            'categories' => [
                [
                    'title' => 'Outdoor',
                    'desc'  => 'Kebutuhan promosi luar ruang: kuat, tahan cuaca, dan terlihat jelas.',
                    'items' => [
                        ['title'=>'Billboard / Baliho','desc'=>'Ukuran besar untuk jangkauan luas.','image'=>null],
                        ['title'=>'Spanduk / Banner','desc'=>'Promosi event dan toko.','image'=>null],
                        ['title'=>'Neonbox / Signage','desc'=>'Branding permanen lebih standout.','image'=>null],
                    ],
                ],
                [
                    'title' => 'Indoor',
                    'desc'  => 'Kebutuhan promosi dalam ruang: detail tajam dan warna konsisten.',
                    'items' => [
                        ['title'=>'Backdrop','desc'=>'Event indoor lebih rapi & premium.','image'=>null],
                        ['title'=>'X-Banner / Roll Up','desc'=>'Praktis untuk promosi instan.','image'=>null],
                        ['title'=>'Poster','desc'=>'Tampilan informatif, hasil tajam.','image'=>null],
                    ],
                ],
                [
                    'title' => 'Multi Printing',
                    'desc'  => 'Kebutuhan cetak kecil-menengah: cepat, rapi, dan fleksibel.',
                    'items' => [
                        ['title'=>'Stiker','desc'=>'Cutting & finishing rapih.','image'=>null],
                        ['title'=>'Label Produk','desc'=>'Kuat dan presisi sesuai kebutuhan.','image'=>null],
                        ['title'=>'Akrilik / Display','desc'=>'Untuk kebutuhan display & dekor.','image'=>null],
                    ],
                ],
            ],

            // CTA
            'cta_title' => 'Siap Memulai Proyek Anda?',
            'cta_desc'  => 'Hubungi tim kami untuk konsultasi dan penawaran terbaik.',
            'cta_btn1_text' => 'WhatsApp',
            'cta_btn2_text' => 'Kontak',
            'cta_btn2_route' => 'profil.kontak',

            // GLOBAL
            'wa_value' => '62812xxxx',
        ];
    }
}
