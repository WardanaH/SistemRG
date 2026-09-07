<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profil_legals', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['syarat_ketentuan', 'kebijakan_privasi']);
            $table->text('isi_konten'); // Di sini kaina di-isi lengkap termassuk rujukan UU PDP No 27 Tahun 2022
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_legals');
    }
};
