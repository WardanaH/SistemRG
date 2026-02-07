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
            $table->string('no_spk')->unique();
            $table->date('tanggal_spk');

            // Info Pelanggan
            $table->string('nama_pelanggan');
            $table->string('no_telepon')->nullable();

            // Info Cabang & Asal (Penting untuk Bantuan)
            $table->foreignId('cabang_id')->constrained('m_cabangs')->onDelete('cascade');
            $table->boolean('is_bantuan')->default(false);
            $table->foreignId('asal_cabang_id')->nullable()->constrained('m_cabangs');

            // Penanggung Jawab Admin/Designer (Satu orang per SPK)
            $table->foreignId('designer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');

            // Status Global (Opsional, untuk tracking level admin)
            $table->enum('status_spk', ['pending', 'acc', 'rejected'])->default('pending');
            $table->text('alasan_pembatalan')->nullable();

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
