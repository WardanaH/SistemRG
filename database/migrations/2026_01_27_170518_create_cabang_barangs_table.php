<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabangBarangsTable extends Migration
{
    public function up()
    {
        Schema::create('cabang_barangs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_id');
            $table->unsignedBigInteger('gudang_barang_id');

            $table->integer('stok')->default(0);
            $table->timestamps();

            // foreign key ke tabel m_cabangs
            $table->foreign('cabang_id')
                  ->references('id')
                  ->on('m_cabangs')
                  ->onDelete('cascade');

            // foreign key ke tabel gudang_barangs
            $table->foreign('gudang_barang_id')
                  ->references('id')
                  ->on('gudang_barangs')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cabang_barangs');
    }
}
