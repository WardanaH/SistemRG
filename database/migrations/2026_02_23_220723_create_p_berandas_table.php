<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('p_berandas', function (Blueprint $table) {
            $table->id();

            // HERO LEFT
            $table->string('hero_badge_label')->nullable();
            $table->string('hero_badge_dot')->nullable(); // var(--rg-yellow) / #hex
            $table->json('hero_title_parts')->nullable(); // [{text,color}, ...]
            $table->text('hero_desc')->nullable();

            $table->string('hero_btn1_label')->nullable();
            $table->string('hero_btn1_route')->nullable();
            $table->string('hero_btn2_label')->nullable();
            $table->string('hero_btn2_route')->nullable();

            $table->json('hero_branches')->nullable(); // ["Banjarmasin", ...]

            // HERO BOTTOM LABELS (3)
            $table->json('hero_labels')->nullable(); // [{text,color},...]

            // HERO RIGHT PANEL
            $table->string('hero_right_small_label')->nullable();
            $table->string('hero_right_title')->nullable();
            $table->string('hero_right_detail_route')->nullable();

            // HERO RIGHT categories (3)
            $table->json('hero_cats')->nullable(); // [{text,color},...]

            $table->string('hero_ask_label')->nullable();
            $table->string('hero_ask_wa_label')->nullable();
            $table->string('hero_ask_contact_label')->nullable();
            $table->string('hero_ask_contact_route')->nullable();

            // LAYANAN UTAMA (3 cards)
            $table->json('main_cards')->nullable(); // [{title,desc,dot,route,image},...]

            // WHY SECTION
            $table->string('why_title')->nullable();
            $table->string('why_desc')->nullable();
            $table->string('why_btn1_label')->nullable();
            $table->string('why_btn1_route')->nullable();
            $table->string('why_btn2_label')->nullable();
            $table->string('why_btn2_route')->nullable();
            $table->json('why_cards')->nullable(); // [{title,desc,accent},...]

            // ABOUT
            $table->string('about_title')->nullable();
            $table->text('about_desc')->nullable();
            $table->string('about_btn1_label')->nullable();
            $table->string('about_btn1_route')->nullable();
            $table->string('about_btn2_label')->nullable();
            $table->string('about_btn2_route')->nullable();

            $table->json('about_branches')->nullable();
            $table->text('about_small_text')->nullable();

            // NEWS SECTION header only
            $table->string('news_title')->nullable();
            $table->string('news_desc')->nullable();
            $table->string('news_btn_label')->nullable();
            $table->string('news_btn_route')->nullable();

            // CTA
            $table->string('cta_title')->nullable();
            $table->text('cta_desc')->nullable();
            $table->string('cta_wa_label')->nullable();
            $table->string('cta_contact_label')->nullable();
            $table->string('cta_contact_route')->nullable();

            // Manual contacts
            $table->string('ig_url')->nullable();
            $table->string('wa_value')->nullable(); // "62812..." atau "https://wa.me/..."

            // COLORS (global overrides)
            $table->json('colors')->nullable(); // {blob_blue, blob_red,...} value var() atau #hex

            // Optional hero image (kalau nanti dipakai)
            $table->string('hero_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_berandas');
    }
};
