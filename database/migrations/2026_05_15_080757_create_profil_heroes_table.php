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
        Schema::create('profil_heroes', function (Blueprint $table) {
            $table->id();
            $table->string('halaman'); // Cth: 'beranda', 'layanan', 'produk', 'kontak'
            $table->string('judul_utama'); // Cth: "CETAK CEPAT, KUALITAS DEWA!"
            $table->text('sub_judul')->nullable();
            $table->string('gambar_background')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_heroes');
    }
};
