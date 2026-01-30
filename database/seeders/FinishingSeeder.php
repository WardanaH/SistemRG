<?php

namespace Database\Seeders;

use App\Models\MFinishing;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FinishingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $finishing = [
            [
                'nama_finishing' => 'Mata Ayam'
            ],
            [
                'nama_finishing' => 'Tepi Sisi'
            ],
            [
                'nama_finishing' => 'Selongsong'
            ]
        ];

        foreach ($finishing as $finishing) {
            MFinishing::create($finishing);
        }
    }
}
