<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengambilans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_id');
            $table->string('ambil_ke');
            $table->date('tanggal');
            $table->string('atas_nama');
            $table->json('list_barang');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->foreign('cabang_id')->references('id')->on('m_cabangs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengambilans');
    }
};
