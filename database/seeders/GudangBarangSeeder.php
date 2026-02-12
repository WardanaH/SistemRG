<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GudangBarangSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::parse('2026-02-10 11:42:18');

        $data = [
            ['kategori_id'=>null,'nama_bahan'=>'albatros','harga'=>0,'satuan'=>'roll','stok'=>95,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>Carbon::parse('2026-02-11 17:07:32')],
            ['kategori_id'=>null,'nama_bahan'=>'amplop linen','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'backlite 510','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'triplek','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tumbler insert paper','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'cloth banner/satin','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stiker scotlite','harga'=>0,'satuan'=>'meter','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'art paper','harga'=>0,'satuan'=>'rim','stok'=>96,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>Carbon::parse('2026-02-11 17:07:32')],
            ['kategori_id'=>null,'nama_bahan'=>'stiker oracal','harga'=>0,'satuan'=>'meter','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stiker bontak glossy A3','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stiker vinyl glossy A3','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stiker bontak doff A3','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'PET DTF','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'duratran','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'flexy 280','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'flexy 340','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'ganci 4,5cm','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'ganci 5,8 cm','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'pin 4,5 cm','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'pin 5,8 cm','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kertas HVS','harga'=>0,'satuan'=>'rim','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'PVC id card','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'yoyo','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'infraboard','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kertas kalkir','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kanvas','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kaos T-shirt','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kipas kerang','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'klemseng','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'korea 440 doff','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'korea 440 glossy','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'lem korea','harga'=>0,'satuan'=>'box','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kotak mug','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kotak plakat','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'laminasi dingin doff','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'laminasi glossy','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'laminasi panas glossy 18 mic','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'laminasi panas doff 18 mic','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'laminating F4','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mata ayam','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mata itik','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kertas linen','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kertas samson','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kertas bufalo','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mika bening','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mika susu','harga'=>0,'satuan'=>'lembar','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'X-banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'Y-banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mini x-banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mug lokal','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'mug import','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'roll up banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'roll up elektrik banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'one way stiker','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stiker vinyl graftac','harga'=>0,'satuan'=>'roll','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'pabel mika','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stempel bulat','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stempel oval','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'stempel kotak','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'karet stempel','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tinta stempel','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tinta solvent outdoor','harga'=>0,'satuan'=>'tank','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tinta eco solvent indoor','harga'=>0,'satuan'=>'botol','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tinta DTF warna','harga'=>0,'satuan'=>'botol','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tinta DTF putih','harga'=>0,'satuan'=>'botol','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'powder DTF','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'cleaner DTF','harga'=>0,'satuan'=>'botol','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'kawat spiral','harga'=>0,'satuan'=>'pack','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tas spunbond tali','harga'=>0,'satuan'=>'lusin','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tas spunbond oval','harga'=>0,'satuan'=>'lusin','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
            ['kategori_id'=>null,'nama_bahan'=>'tripod banner','harga'=>0,'satuan'=>'pcs','stok'=>100,'batas_stok'=>1000,'keterangan'=>null,'created_at'=>$now,'updated_at'=>$now],
        ];

        DB::table('gudang_barangs')->insert($data);
    }
}
