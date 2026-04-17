<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('p_berita_pages', function (Blueprint $table) {
            $table->id();

            // HERO
            $table->string('hero_chip', 80)->default('Berita & Edukasi');
            $table->string('hero_title_1', 80)->default('Update');
            $table->string('hero_title_2', 80)->default('&');
            $table->string('hero_title_3', 80)->default('Insight');
            $table->string('hero_lead', 220)->default('Berita project terbaru dan artikel edukasi seputar printing & advertising.');

            // SEARCH
            $table->string('search_placeholder', 120)->default('Cari judul / kata kunci…');
            $table->string('search_button', 30)->default('Cari');
            $table->string('tab_news', 30)->default('Berita');
            $table->string('tab_edu', 30)->default('Edukasi');

            // 3 STAT CARDS
            $table->string('stat1_k', 40)->default('Konten');
            $table->string('stat1_v', 120)->default('Update berkala');
            $table->string('stat2_k', 40)->default('Topik');
            $table->string('stat2_v', 120)->default('Outdoor • Indoor • Multi');
            $table->string('stat3_k', 40)->default('Tujuan');
            $table->string('stat3_v', 120)->default('Biar kamu makin paham sebelum cetak');

            // SECTION (list)
            $table->string('news_heading', 80)->default('Berita');
            $table->string('news_desc', 220)->default('Update project dan dokumentasi pekerjaan terbaru.');
            $table->string('edu_heading', 80)->default('Edukasi');
            $table->string('edu_desc', 240)->default('Artikel edukasi untuk membantu kamu memilih bahan, menyiapkan file, dan strategi promosi.');

            // BUTTONS (list header)
            $table->string('btn_kontak', 40)->default('Kontak');
            $table->string('btn_layanan', 40)->default('Lihat Layanan');

            // CTA (bottom card)
            $table->string('cta_title', 120)->default('Butuh rekomendasi bahan & ukuran?');
            $table->string('cta_desc', 240)->default('Ceritakan kebutuhan promosi kamu, tim kami bantu pilih opsi yang paling pas.');
            $table->string('cta_btn_wa', 40)->default('WhatsApp');
            $table->string('cta_btn_kontak', 40)->default('Kontak');

            // Artikel detail (optional text)
            $table->string('article_help_text', 120)->default('Punya pertanyaan? Konsultasi gratis.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_berita_pages');
    }
};
