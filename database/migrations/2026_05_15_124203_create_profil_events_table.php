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
        Schema::create('profil_events', function (Blueprint $table) {
            $table->id();
            $table->string('tema'); // Cth: ramadhan, imlek, natal, kemerdekaan
            $table->string('badge_kategori'); // Cth: SOUVENIR MUG, BANNER OUTDOOR
            $table->string('nama_produk'); // Cth: MUG CUSTOM RAMADHAN
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_events');
    }
};
