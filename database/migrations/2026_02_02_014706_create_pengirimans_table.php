<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pengiriman')->unique();

            // relasi ke permintaan cabang
            $table->unsignedBigInteger('permintaan_id')->nullable();

            // cabang tujuan
            $table->unsignedBigInteger('cabang_tujuan_id');

            $table->date('tanggal_pengiriman');

            $table->enum('status_pengiriman', [
                'Dikirim',
                'Diterima'
            ])->default('Dikirim');

            $table->enum('status_kelengkapan', [
                'Lengkap',
                'Tidak Lengkap'
            ])->nullable();

            $table->date('tanggal_diterima')->nullable();

            // detail barang dikirim (JSON)
            $table->json('keterangan')->nullable();

            // catatan dari gudang pusat
            $table->text('catatan_gudang')->nullable();

            $table->timestamps();

            // =====================
            // FOREIGN KEY
            // =====================

            $table->foreign('permintaan_id')
                ->references('id')
                ->on('permintaan_pengirimans')
                ->nullOnDelete();

            $table->foreign('cabang_tujuan_id')
                ->references('id')
                ->on('m_cabangs')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengirimans');
    }
};
