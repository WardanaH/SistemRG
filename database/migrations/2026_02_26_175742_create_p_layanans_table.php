<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_layanan', function (Blueprint $table) {
            $table->id();

            // HERO
            $table->string('hero_chip_text', 255)->nullable();
            $table->string('hero_chip_dot', 64)->nullable(); // var(--rg-blue) / #RRGGBB
            $table->json('hero_title_parts')->nullable();    // [{text,color}]
            $table->text('hero_desc')->nullable();
            $table->string('hero_btn1_text', 60)->nullable();
            $table->string('hero_btn2_text', 60)->nullable();
            $table->string('hero_btn2_route', 120)->nullable();

            // SUMMARY
            $table->string('summary_title', 120)->nullable();
            $table->json('summary_items')->nullable(); // [{text,dot}]

            // WHY
            $table->string('why_title', 120)->nullable();
            $table->string('why_desc', 255)->nullable();
            $table->json('why_cards')->nullable(); // [{title,desc,accent,image}]

            // CATEGORIES (FIXED 3)
            $table->json('categories')->nullable();
            // shape:
            // [
            //   {title, desc, items:[{title,desc,image}]},
            //   {title, desc, items:[...]},
            //   {title, desc, items:[...]}
            // ]

            // CTA
            $table->string('cta_title', 160)->nullable();
            $table->text('cta_desc')->nullable();
            $table->string('cta_btn1_text', 60)->nullable();
            $table->string('cta_btn2_text', 60)->nullable();
            $table->string('cta_btn2_route', 120)->nullable();

            // GLOBAL
            $table->string('wa_value', 255)->nullable(); // 62812xxxx atau https://wa.me/62xxxx

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_layanan');
    }
};
