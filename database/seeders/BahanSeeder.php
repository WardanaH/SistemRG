<?php

namespace Database\Seeders;

use App\Models\MBahanBaku;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahanBaku = [
            [
                'nama_bahan' => 'Flexy A',
                'kode_bahan' => 'FLA'
            ],
            [
                'nama_bahan' => 'Flexy B',
                'kode_bahan' => 'FLB'
            ],
            [
                'nama_bahan' => 'Flexy C',
                'kode_bahan' => 'FLC'
            ],
            [
                'nama_bahan' => 'Flexy D',
                'kode_bahan' => 'FLD'
            ],
            [
                'nama_bahan' => 'Flexy E',
                'kode_bahan' => 'FLE'
            ]
        ];

        foreach ($bahanBaku as $bahan) {
            MBahanBaku::create($bahan);
        }
    }
}
