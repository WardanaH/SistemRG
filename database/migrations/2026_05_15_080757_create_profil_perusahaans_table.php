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
        Schema::create('profil_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->text('tentang_kami')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();

            // Info Kontak Pusat
            $table->string('wa_pusat')->nullable();
            $table->string('email_pusat')->nullable();

            // Info Cabang & Maps
            $table->text('alamat_cabang_1')->nullable(); // Martapura
            $table->string('wa_cabang_1')->nullable();
            $table->text('link_maps_cabang_1')->nullable();

            $table->text('alamat_cabang_2')->nullable(); // Banjabaru
            $table->string('wa_cabang_2')->nullable();
            $table->text('link_maps_cabang_2')->nullable();

            $table->text('alamat_cabang_3')->nullable(); // Banjarmasin
            $table->string('wa_cabang_3')->nullable();
            $table->text('link_maps_cabang_3')->nullable();

            $table->text('alamat_cabang_4')->nullable(); // LiangAnggang
            $table->string('wa_cabang_4')->nullable();
            $table->text('link_maps_cabang_4')->nullable();

            // Medsos
            $table->string('link_instagram')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->string('link_facebook')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_perusahaans');
    }
};
