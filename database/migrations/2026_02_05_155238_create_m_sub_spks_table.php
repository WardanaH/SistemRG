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
        Schema::create('m_sub_spks', function (Blueprint $table) {
            $table->id();

            // RELASI KE PARENT (SPK UTAMA)
            $table->foreignId('spk_id')->constrained('m_spks')->onDelete('cascade');

            // Detail Item
            $table->string('nama_file');
            $table->enum('jenis_order', ['indoor', 'outdoor', 'multi']);

            // Ukuran (Pakai double/float agar bisa koma)
            $table->double('p')->default(0);
            $table->double('l')->default(0);

            $table->integer('qty');

            // Relasi ke Bahan
            $table->foreignId('bahan_id')->constrained('m_bahan_bakus');

            $table->string('finishing')->nullable();
            $table->text('catatan')->nullable();

            // OPERATOR & STATUS PRODUKSI (Pindah ke sini agar per item bisa beda status)
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing', 'done'])->default('pending');
            $table->text('catatan_operator')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_sub_spks');
    }
};
