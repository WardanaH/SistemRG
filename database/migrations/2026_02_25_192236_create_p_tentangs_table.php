<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('p_tentangs', function (Blueprint $table) {
            $table->id();

            // HERO
            $table->string('hero_chip', 120)->nullable();
            $table->json('hero_title_parts')->nullable(); // [{text,color}]
            $table->text('hero_desc')->nullable();
            $table->string('hero_btn1_label', 40)->nullable();
            $table->string('hero_btn1_route', 100)->nullable();
            $table->string('hero_btn2_label', 40)->nullable();
            $table->string('hero_btn2_route', 100)->nullable();

            // FOCUS (3)
            $table->json('focus_items')->nullable(); // [{label,accent}]

            // WHY + HIGHLIGHT + FAQ
            $table->string('why_title', 120)->nullable();
            $table->string('why_desc', 200)->nullable();
            $table->json('highlights')->nullable(); // [{text,color}]
            $table->json('faq')->nullable();        // [{q,a}]

            // OWNER
            $table->string('owner_small', 80)->nullable();
            $table->string('owner_title', 160)->nullable();
            $table->text('owner_message')->nullable();
            $table->string('owner_name', 80)->nullable();
            $table->string('owner_role', 80)->nullable();
            $table->string('owner_photo', 255)->nullable();

            // HISTORY
            $table->string('history_title', 120)->nullable();
            $table->text('history_desc')->nullable();
            $table->json('history_stats')->nullable(); // [{k,v}]

            // VISION / MISSION
            $table->string('vision_title', 80)->nullable();
            $table->text('vision_desc')->nullable();

            $table->string('mission_title', 80)->nullable();
            $table->json('mission_items')->nullable(); // [text...]

            // LEADERS (3)
            $table->json('leaders')->nullable(); // [{name,role,photo}]

            // CLIENTS
            $table->json('clients')->nullable(); // [logo_path...]

            // COLORS (optional, same pattern)
            $table->json('colors')->nullable(); // {blob_blue, blob_red, blob_yellow, ...}

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_tentangs');
    }
};
