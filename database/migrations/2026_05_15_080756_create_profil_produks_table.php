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
        Schema::create('profil_produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kategori_produk'); // Cth: Spanduk, Stiker, Kaos
            $table->enum('kategori_layanan', ['indoor', 'outdoor', 'multi', 'dtf']);
            $table->text('deskripsi_singkat')->nullable();
            $table->text('deskripsi_lengkap')->nullable();
            $table->string('gambar_produk')->nullable();
            $table->boolean('is_tampil_beranda')->default(false); // Gasan manampilakan produk andalan di beranda
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_produks');
    }
};
