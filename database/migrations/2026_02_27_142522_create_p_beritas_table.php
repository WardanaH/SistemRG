<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('p_beritas', function (Blueprint $table) {
            $table->id();

            // news | education
            $table->string('type', 20)->default('news')->index();

            $table->string('title', 180);
            $table->string('slug', 220)->unique();

            // untuk card/list
            $table->string('excerpt', 320)->nullable();

            // cover image optional
            $table->string('cover')->nullable();

            // untuk education, optional
            $table->string('category_label', 80)->nullable();

            // konten html (karena kamu pakai <p><ul> dll)
            $table->longText('content')->nullable();

            // publish date (biar bisa schedule / konsisten)
            $table->timestamp('published_at')->nullable()->index();

            $table->boolean('is_published')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_beritas');
    }
};
