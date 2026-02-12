<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ambil_antars', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();

            $table->foreignId('cabang_pengirim_id')->constrained('m_cabangs');
            $table->foreignId('cabang_tujuan_id')->constrained('m_cabangs');

            $table->enum('jenis', ['Ambil', 'Antar']);

            $table->date('tanggal');
            $table->string('atas_nama');

            // bisa lebih dari 1 keterangan
            $table->json('keterangan');

            $table->enum('status', ['Menunggu', 'Dikirim', 'Diterima'])->default('Menunggu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambil_antars');
    }
};
