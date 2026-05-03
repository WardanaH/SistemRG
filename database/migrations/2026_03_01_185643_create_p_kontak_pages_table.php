<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('p_kontak_pages', function (Blueprint $table) {
            $table->id();

            // HERO
            $table->string('hero_chip')->default('Kontak');
            $table->string('hero_title')->default('Kontak & Lokasi Cabang');
            $table->text('hero_lead')->nullable();

            $table->string('hero_btn_wa_label')->default('WhatsApp');
            $table->string('hero_btn2_label')->default('Lihat Layanan');
            $table->string('hero_btn2_route')->default('profil.layanan');

            // PANEL KANAN (Jam Respons)
            $table->string('panel_title')->default('Jam Respons');
            $table->text('panel_desc')->nullable();

            // PILLS (3)
            $table->json('hero_pills')->nullable(); // [{text,color},...]

            // STATS (3)
            $table->json('stats')->nullable(); // [{k,v,accent},...]

            // SECTION CABANG
            $table->string('branches_heading')->default('Cabang');
            $table->string('branches_desc')->default('Klik cabang untuk membuka lokasi di Google Maps.');
            $table->string('branch_open_label')->default('Buka →');

            // MAP
            $table->string('map_heading')->default('Peta');
            $table->string('map_desc')->default('OpenStreetMap (tanpa API key) + pin tiap cabang.');
            $table->string('map_fallback')->default('Map gagal dimuat (Leaflet/tiles tidak ke-load). Cek koneksi internet atau CDN terblokir.');

            // HELP BOX (kiri bawah)
            $table->string('help_title')->default('Butuh bantuan cepat?');
            $table->string('help_btn_wa')->default('WhatsApp');
            $table->string('help_btn2_label')->default('Layanan');
            $table->string('help_btn2_route')->default('profil.layanan');

            // CTA BOTTOM
            $table->string('cta_title')->default('Konsultasi cepat via WhatsApp');
            $table->text('cta_desc')->nullable();
            $table->string('cta_btn_wa')->default('WhatsApp');
            $table->string('cta_btn_back')->default('Kembali ke Beranda');
            $table->string('cta_btn_back_route')->default('profil.beranda');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_kontak_pages');
    }
};
