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
        Schema::create('m_spks', function (Blueprint $table) {
            $table->id();
            $table->string('no_spk')->unique(); // Unique agar tidak ada nomor ganda
            $table->date('tanggal_spk');

            // Menggunakan enum agar data konsisten (opsional, bisa string biasa)
            $table->enum('jenis_order_spk', ['outdoor', 'indoor', 'multi']);

            $table->string('nama_pelanggan');
            $table->string('no_telepon'); // Perbaikan typo 'no_telpom'
            $table->string('nama_file');

            // Detail Ukuran & Bahan
            $table->integer('ukuran_panjang');
            $table->integer('ukuran_lebar');

            // Relasi ke tabel m_bahan_bakus
            $table->enum('status_spk', ['pending', 'acc', 'rejected']);
            $table->text('alasan_pembatalan')->nullable();
            $table->enum('status_produksi', ['pending', 'ongoing', 'done']);
            $table->integer('kuantitas');
            $table->string('finishing')->nullable(); // Tambahan: biasanya SPK butuh info finishing
            $table->text('keterangan')->nullable(); // Text lebih panjang & nullable

            // Pastikan tabel 'm_bahan_bakus' sudah ada sebelum migration ini dijalankan
            $table->foreignId('bahan_id')
                ->constrained('m_bahan_bakus')
                ->onDelete('cascade');

            // Relasi ke Users (Designer)
            $table->foreignId('designer_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Jika user dihapus, data SPK tetap ada tapi designer null

            // Relasi ke Users (Operator)
            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Opsional: Jika SPK terikat cabang
            $table->foreignId('cabang_id')
                ->constrained('m_cabangs')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_spks');
    }
};
