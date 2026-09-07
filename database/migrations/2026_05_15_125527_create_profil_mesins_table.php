<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profil_mesins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mesin'); // Cth: PRINTER OUTDOOR
            $table->string('warna_tema')->default('cyan'); // Gasan warna border kotak (cyan, magenta, yellow, green)
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_mesins');
    }
};
