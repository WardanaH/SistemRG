<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->id();

            // FK barang
            $table->foreignId('gudang_barang_id')
                  ->constrained('gudang_barangs')
                  ->cascadeOnDelete();

            // FK cabang tujuan
            $table->foreignId('cabang_tujuan_id')
                  ->constrained('m_cabangs')
                  ->cascadeOnDelete();

            $table->integer('jumlah');
            $table->date('tanggal_pengiriman');

            $table->enum('status_pengiriman', [
                'Dikemas',
                'Dikirim',
                'Diterima'
            ])->default('Dikemas');

            $table->timestamp('tanggal_diterima')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengirimans');
    }
};
