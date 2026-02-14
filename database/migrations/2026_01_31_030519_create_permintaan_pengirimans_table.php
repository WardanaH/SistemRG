<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permintaan_pengirimans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permintaan')->unique();
            $table->foreignId('cabang_id')->constrained('m_cabangs')->cascadeOnDelete();
            $table->date('tanggal_permintaan');
            $table->enum('status', ['Menunggu', 'Diproses', 'Disetujui', 'Ditolak', 'Selesai'])->default('Menunggu');
            $table->json('detail_barang');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_pengirimans');
    }
};
