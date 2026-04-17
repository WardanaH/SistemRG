<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PKontakPage extends Model
{
    protected $table = 'p_kontak_pages';

    protected $fillable = [
        'hero_chip','hero_title','hero_lead',
        'hero_btn_wa_label','hero_btn2_label','hero_btn2_route',
        'panel_title','panel_desc',
        'hero_pills',
        'stats',
        'branches_heading','branches_desc','branch_open_label',
        'map_heading','map_desc','map_fallback',
        'help_title','help_btn_wa','help_btn2_label','help_btn2_route',
        'cta_title','cta_desc','cta_btn_wa','cta_btn_back','cta_btn_back_route',
    ];

    protected $casts = [
        'hero_pills' => 'array',
        'stats'      => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero_chip' => 'Kontak',
            'hero_title' => 'Kontak & Lokasi Cabang',
            'hero_lead' => 'Untuk konsultasi cepat, hubungi WhatsApp. Untuk navigasi, klik cabang dan kamu akan diarahkan ke Google Maps. Peta menampilkan pin cabang via OpenStreetMap (tanpa Google Maps API).',

            'hero_btn_wa_label' => 'WhatsApp',
            'hero_btn2_label' => 'Lihat Layanan',
            'hero_btn2_route' => 'profil.layanan',

            'panel_title' => 'Jam Respons',
            'panel_desc' => "Senin–Sabtu • 09.00–17.00\n(bisa menyesuaikan kondisi lapangan)",

            'hero_pills' => [
                ['text' => 'Outdoor', 'color' => 'var(--rg-blue)'],
                ['text' => 'Indoor',  'color' => 'var(--rg-red)'],
                ['text' => 'Multi',   'color' => 'var(--rg-yellow)'],
            ],

            'stats' => [
                ['k' => 'Cabang',     'v' => '4 titik layanan',   'accent' => 'var(--rg-blue)'],
                ['k' => 'Konsultasi', 'v' => 'Respon cepat',      'accent' => 'var(--rg-red)'],
                ['k' => 'Navigasi',   'v' => 'Klik cabang → Maps','accent' => 'var(--rg-yellow)'],
            ],

            'branches_heading' => 'Cabang',
            'branches_desc' => 'Klik cabang untuk membuka lokasi di Google Maps.',
            'branch_open_label' => 'Buka →',

            'map_heading' => 'Peta',
            'map_desc' => 'OpenStreetMap (tanpa API key) + pin tiap cabang.',
            'map_fallback' => 'Map gagal dimuat (Leaflet/tiles tidak ke-load). Cek koneksi internet atau CDN terblokir.',

            'help_title' => 'Butuh bantuan cepat?',
            'help_btn_wa' => 'WhatsApp',
            'help_btn2_label' => 'Layanan',
            'help_btn2_route' => 'profil.layanan',

            'cta_title' => 'Konsultasi cepat via WhatsApp',
            'cta_desc' => 'Kirim kebutuhan (Outdoor/Indoor/Multi), ukuran, dan jumlah — tim kami bantu arahkan.',
            'cta_btn_wa' => 'WhatsApp',
            'cta_btn_back' => 'Kembali ke Beranda',
            'cta_btn_back_route' => 'profil.beranda',
        ];
    }
}
