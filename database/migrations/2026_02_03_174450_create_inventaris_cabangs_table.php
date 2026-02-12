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
        Schema::create('inventaris_cabangs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cabang_id')
                ->constrained('m_cabangs')
                ->cascadeOnDelete();

            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->integer('jumlah')->default(0);
            $table->string('kondisi')->default('Baik');
            $table->string('lokasi')->nullable();
            $table->date('tanggal_input');
            $table->string('foto')->nullable();

            $table->string('qr_code')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_cabangs');
    }
};
