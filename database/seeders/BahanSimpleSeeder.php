<?php

namespace Database\Seeders;

use App\Models\MBahanBaku;
use Illuminate\Database\Seeder;

class BahanSimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DAFTAR BAHAN BAKU DIAMBIL DARI DATA GUDANG BARANG SEEDER
        $semuaProduk = [
            'albatros',
            'amplop linen',
            'backlite 510',
            'triplek',
            'tumbler insert paper',
            'cloth banner/satin',
            'stiker scotlite',
            'art paper',
            'stiker oracal',
            'stiker bontak glossy A3',
            'stiker vinyl glossy A3',
            'stiker bontak doff A3',
            'PET DTF',
            'duratran',
            'flexy 280',
            'flexy 340',
            'ganci 4,5cm',
            'ganci 5,8 cm',
            'pin 4,5 cm',
            'pin 5,8 cm',
            'kertas HVS',
            'PVC id card',
            'yoyo',
            'infraboard',
            'kertas kalkir',
            'kanvas',
            'kaos T-shirt',
            'kipas kerang',
            'klemseng',
            'korea 440 doff',
            'korea 440 glossy',
            'lem korea',
            'kotak mug',
            'kotak plakat',
            'laminasi dingin doff',
            'laminasi glossy',
            'laminasi panas glossy 18 mic',
            'laminasi panas doff 18 mic',
            'laminating F4',
            'mata ayam',
            'mata itik',
            'kertas linen',
            'kertas samson',
            'kertas bufalo',
            'mika bening',
            'mika susu',
            'X-banner',
            'Y-banner',
            'mini x-banner',
            'mug lokal',
            'mug import',
            'roll up banner',
            'roll up elektrik banner',
            'one way stiker',
            'stiker vinyl graftac',
            'pabel mika',
            'stempel bulat',
            'stempel oval',
            'stempel kotak',
            'karet stempel',
            'tinta stempel',
            'tinta solvent outdoor',
            'tinta eco solvent indoor',
            'tinta DTF warna',
            'tinta DTF putih',
            'powder DTF',
            'cleaner DTF',
            'kawat spiral',
            'tas spunbond tali',
            'tas spunbond oval',
            'tripod banner'
        ];

        // Memanggil fungsi untuk insert data
        self::addBahanBaku($semuaProduk);
    }

    /**
     * Fungsi helper untuk insert data
     */
    public static function addBahanBaku(array $produkList): void
    {
        foreach ($produkList as $index => $namaProduk) {
            // Generate Kode Bahan Otomatis: PRD-001, PRD-002, dst.
            // str_pad berguna untuk membuat angka 001, 010, 100 agar rapi
            $kodeOtomatis = 'PRD-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Jadikan huruf kapital semua agar seragam di database
            $namaProdukKapital = strtoupper($namaProduk);

            MBahanBaku::firstOrCreate(
                ['nama_bahan' => $namaProdukKapital], // Cek berdasarkan nama agar tidak duplikat
                ['kode_bahan' => $kodeOtomatis] // Isi kode jika baru dibuat
            );
        }
    }
}
