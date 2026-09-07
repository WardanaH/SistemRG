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
        Schema::create('profil_kliens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klien'); // Cth: Pemko Banjarbaru, PT Adaro
            $table->string('logo')->nullable(); // Gasan foto/logo klien
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_kliens');
    }
};
