<?php

namespace Database\Seeders;

use App\Models\MFinishing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Tambahkan ini

class FinishingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key sementara (Supaya tidak error jika tabel ini dipakai tabel lain)
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel dan reset ID ke 1
        MFinishing::truncate();

        // 3. Hidupkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();

        // 4. Data Finishing
        $finishing = [
            ['nama_finishing' => 'Mata Ayam'],
            ['nama_finishing' => 'Tepi Sisi'],
            ['nama_finishing' => 'Selongsong'],
            ['nama_finishing' => 'Jilid'],
            ['nama_finishing' => 'Jilid Spiral'],
            ['nama_finishing' => 'Jilid Softcover'],
            ['nama_finishing' => 'Jilid Hardcover'],
            ['nama_finishing' => 'Jilid Hot Binding'],
            ['nama_finishing' => 'Potong'],
            ['nama_finishing' => 'Potong Die Cut'],
            ['nama_finishing' => 'Potong Kiss Cut'],
            ['nama_finishing' => 'Lipat'],
            ['nama_finishing' => 'Laminasi Doff'],
            ['nama_finishing' => 'Laminasi Glossy'],
        ];

        foreach ($finishing as $item) {
            MFinishing::create($item);
        }
    }
}
