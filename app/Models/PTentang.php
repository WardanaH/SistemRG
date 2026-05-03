<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PTentang extends Model
{
    protected $table = 'p_tentangs';

    protected $fillable = [
        'hero_chip','hero_title_parts','hero_desc','hero_btn1_label','hero_btn1_route','hero_btn2_label','hero_btn2_route',
        'focus_items',
        'why_title','why_desc','highlights','faq',
        'owner_small','owner_title','owner_message','owner_name','owner_role','owner_photo',
        'history_title','history_desc','history_stats',
        'vision_title','vision_desc',
        'mission_title','mission_items',
        'leaders',
        'clients',
        'colors',
    ];

    protected $casts = [
        'hero_title_parts' => 'array',
        'focus_items'      => 'array',
        'highlights'       => 'array',
        'faq'              => 'array',
        'history_stats'    => 'array',
        'mission_items'    => 'array',
        'leaders'          => 'array',
        'clients'          => 'array',
        'colors'           => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero_chip' => 'Percetakan & Advertising',
            'hero_title_parts' => [
                ['text' => 'Tentang', 'color' => 'var(--rg-blue)'],
                ['text' => 'Restu', 'color' => 'var(--rg-yellow)'],
                ['text' => 'Guru', 'color' => 'var(--rg-yellow)'],
                ['text' => 'Promosindo', 'color' => 'var(--rg-red)'],
            ],
            'hero_desc' => 'Kami bergerak di bidang percetakan dan advertising dengan layanan outdoor, indoor, dan multi.',
            'hero_btn1_label' => 'Lihat Layanan',
            'hero_btn1_route' => 'profil.layanan',
            'hero_btn2_label' => 'Kontak',
            'hero_btn2_route' => 'profil.kontak',

            'focus_items' => [
                ['label' => 'Outdoor Advertising', 'accent' => 'var(--rg-blue)'],
                ['label' => 'Indoor Printing', 'accent' => 'var(--rg-red)'],
                ['label' => 'Multi Printing', 'accent' => 'var(--rg-yellow)'],
            ],

            'why_title' => 'Kenapa Memilih Kami',
            'why_desc'  => 'Kualitas produksi, komunikasi cepat, dan pengerjaan rapi.',
            'highlights' => [
                ['text' => 'Material terkurasi & finishing rapi', 'color' => 'var(--rg-blue)'],
                ['text' => 'Timeline jelas & komunikasi cepat', 'color' => 'var(--rg-red)'],
                ['text' => 'Support dari konsep hingga produksi', 'color' => 'var(--rg-yellow)'],
            ],
            'faq' => [
                ['q' => 'Bagaimana sistem pemesanan?', 'a' => 'Hubungi tim kami untuk konsultasi, kirim file/desain, dan kami bantu proses sampai produksi.'],
                ['q' => 'Apakah bisa custom desain?', 'a' => 'Bisa. Tim kami dapat membantu dari konsep sampai file siap cetak/produksi.'],
                ['q' => 'Berapa lama pengerjaan?', 'a' => 'Tergantung jenis pekerjaan dan antrian produksi. Kami selalu infokan estimasi sejak awal.'],
            ],

            'owner_small' => 'Owner Message',
            'owner_title' => 'Komitmen kualitas dan layanan terbaik',
            'owner_message' => 'Kami ingin setiap hasil produksi rapi, tepat waktu, dan memberikan nilai terbaik untuk klien.',
            'owner_name' => 'Nama Owner',
            'owner_role' => 'Founder & Director',
            'owner_photo' => null,

            'history_title' => 'Sejarah Singkat',
            'history_desc' => 'Restu Guru Promosindo berkembang dari kebutuhan promosi lokal menjadi layanan percetakan dan advertising yang lebih luas.',
            'history_stats' => [
                ['k' => 'Outdoor', 'v' => 'Baliho, billboard, spanduk, neonbox.'],
                ['k' => 'Indoor', 'v' => 'Poster, banner, backdrop, display.'],
                ['k' => 'Multi', 'v' => 'Sticker vinyl, cutting, label, dll.'],
                ['k' => 'Support', 'v' => 'Bantu konsep sampai produksi.'],
            ],

            'vision_title' => 'Visi',
            'vision_desc'  => 'Menjadi mitra promosi terpercaya dengan kualitas produksi dan layanan yang konsisten.',
            'mission_title' => 'Misi',
            'mission_items' => [
                'Memberikan hasil produksi berkualitas dan rapi.',
                'Menyediakan layanan cepat dengan komunikasi yang jelas.',
                'Mendukung kebutuhan promosi klien dari awal hingga akhir.',
            ],

            'leaders' => [
                ['name' => 'Nama Leader 1', 'role' => 'Head Production', 'photo' => null],
                ['name' => 'Nama Leader 2', 'role' => 'Project Supervisor', 'photo' => null],
                ['name' => 'Nama Leader 3', 'role' => 'Customer Support', 'photo' => null],
            ],

            'clients' => [],

            'colors' => [
                'blob_blue' => 'var(--rg-blue)',
                'blob_red' => 'var(--rg-red)',
                'blob_yellow' => 'var(--rg-yellow)',
            ],
        ];
    }
}
