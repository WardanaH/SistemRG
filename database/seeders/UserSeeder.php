<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MCabang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Buat Cabang ---
        $cabangGDGUtama = MCabang::create([
            'kode' => 'GDG-UTM',
            'nama' => 'Gudang Utama',
            'slug' => 'gudang-utama',
            'email' => 'gudang-utama@restuguru.com',
            'telepon' => '0811000000',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'jenis' => 'pusat',
        ]);

        $cabangUtama = MCabang::create([
            'kode' => 'CBG-UTM',
            'nama' => 'Cabang Utama',
            'slug' => 'cabang-pusat',
            'email' => 'utama@restuguru.com',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'jenis' => 'pusat',
        ]);

        $cabangBjm = MCabang::create([
            'kode' => 'CBG-BJM',
            'nama' => 'Cabang Banjarmasin',
            'slug' => 'cabang-banjarmasin',
            'email' => 'banjarmasin@restuguru.com',
            'telepon' => '08234567890',
            'alamat' => 'Jl. Veteran No. 2, Banjarmasin',
            'jenis' => 'cabang',
        ]);

        $cabangBjb = MCabang::create([
            'kode' => 'CBG-BJB',
            'nama' => 'Cabang Banjarbaru',
            'slug' => 'cabang-banjarbaru',
            'email' => 'banjarbaru@restuguru.com',
            'telepon' => '08234567890',
            'alamat' => 'Jl. Veteran No. 3, Banjarbaru',
            'jenis' => 'cabang',
        ]);

        $cabangMtp = MCabang::create([
            'kode' => 'CBG-MTP',
            'nama' => 'Cabang Martapura',
            'slug' => 'cabang-martapura',
            'email' => 'Martapura@restuguru.com',
            'telepon' => '08234567890',
            'alamat' => 'Martapura, Cindai Alus, Kabupaten Banjar, Kalimantan Selatan',
            'jenis' => 'cabang',
        ]);

        $cabangLgg = MCabang::create([
            'kode' => 'CBG-LGG',
            'nama' => 'Cabang Liangangggang',
            'slug' => 'cabang-liangangggang',
            'email' => 'Liangangggang@restuguru.com',
            'telepon' => '08234567891',
            'alamat' => 'Jl. A. Yani No.17, Landasan Ulin Bar., Kec. Liang Anggang, Kota Banjar Baru, Kalimantan Selatan 70722',
            'jenis' => 'cabang',
        ]);

        // // --- 2. Fungsi Helper untuk membuat User A & B ---
        // $createUsers = function($cabang, $role, $prefixName, $usernameBase) {
        //     $suffix = ['a', 'b'];
        //     foreach ($suffix as $s) {
        //         $user = User::create([
        //             'nama' => $prefixName . ' ' . strtoupper($s),
        //             'username' => $usernameBase . $s,
        //             'email' => $usernameBase . $s . '@example.com',
        //             'password' => Hash::make('password'),
        //             'cabang_id' => $cabang->id,
        //         ]);
        //         $user->assignRole($role);
        //     }
        // };

        // // --- 3. Implementasi User A & B per Cabang ---

        // // MANAJEMEN (Di Cabang Utama)
        // $createUsers($cabangUtama, 'manajemen', 'Manajemen', 'manajemen');

        // // ADMIN per Cabang
        // $createUsers($cabangBjm, 'admin', 'Admin BJM', 'adminbjm');
        // $createUsers($cabangBjb, 'admin', 'Admin BJB', 'adminbjb');

        // // DESIGNER per Cabang
        // $createUsers($cabangBjm, 'designer', 'Designer BJM', 'designerbjm');
        // $createUsers($cabangBjb, 'designer', 'Designer BJB', 'designerbjb');

        // // OPERATOR INDOOR per Cabang
        // $createUsers($cabangBjm, 'operator indoor', 'Op Indoor BJM', 'opindoorbjm');
        // $createUsers($cabangBjb, 'operator indoor', 'Op Indoor BJB', 'opindoorbjb');

        // // OPERATOR OUTDOOR per Cabang
        // $createUsers($cabangBjm, 'operator outdoor', 'Op Outdoor BJM', 'opoutdoorbjm');
        // $createUsers($cabangBjb, 'operator outdoor', 'Op Outdoor BJB', 'opoutdoorbjb');

        // // OPERATOR MULTI per Cabang
        // $createUsers($cabangBjm, 'operator multi', 'Op Multi BJM', 'opmultibjm');
        // $createUsers($cabangBjb, 'operator multi', 'Op Multi BJB', 'opmultibjb');


        $manajemen = User::create([
            'nama' => 'Manajemen',
            'username' => 'manajemen',
            'email' => 'manajemen@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangUtama->id,
        ]);
        $manajemen->assignRole('manajemen');








        // MTP
        //admin
        $rahmawati = User::create([
            'nama' => 'Rahmawati',
            'username' => 'rahmawati',
            'email' => 'rahmawati@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $rahmawati->assignRole('admin');

        $egyDinda = User::create([
            'nama' => 'Egy Dinda Wulandari',
            'username' => 'egydinda',
            'email' => 'egydinda@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $egyDinda->assignRole('admin');

        $zaid = User::create([
            'nama' => 'Ahmad Mujahid admin',
            'username' => 'zaidadmin',
            'email' => 'zaidadmin@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $zaid->assignRole('admin');

        $rizkyMahdini = User::create([
            'nama' => 'Rizky Mahdini',
            'username' => 'rizkymahdini',
            'email' => 'rizky@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $rizkyMahdini->assignRole('admin');

        //operator outdoor
        $aan = User::create([
            'nama' => 'Aan',
            'username' => 'aan',
            'email' => 'aan@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $aan->assignRole('operator outdoor');

        $akbar = User::create([
            'nama' => 'Akbar',
            'username' => 'akbar',
            'email' => 'akbar@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $akbar->assignRole('operator outdoor');

        // opearator indoor
        $kiki = User::create([
            'nama' => 'Kiki',
            'username' => 'kiki',
            'email' => 'kiki@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $kiki->assignRole('operator indoor');

        // operator multi
        $nopal = User::create([
            'nama' => 'Nopal',
            'username' => 'nopal',
            'email' => 'nopal@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $nopal->assignRole('operator multi');

        //designer
        $zaid = User::create([
            'nama' => 'Zaid',
            'username' => 'zaid',
            'email' => 'zaid@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $zaid->assignRole('designer');

        $mila = User::create([
            'nama' => 'Mila',
            'username' => 'mila',
            'email' => 'mila@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $mila->assignRole('designer');

        $dini = User::create([
            'nama' => 'Dini',
            'username' => 'dini',
            'email' => 'dini@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $dini->assignRole('designer');

        $wawa = User::create([
            'nama' => 'Wawa',
            'username' => 'wawa',
            'email' => 'wawa@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $wawa->assignRole('designer');

        $alif = User::create([
            'nama' => 'Alif',
            'username' => 'alif',
            'email' => 'alif@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $alif->assignRole('designer');

        $indah = User::create([
            'nama' => 'Indah',
            'username' => 'indah',
            'email' => 'indah@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $indah->assignRole('designer');

        $mahmud = User::create([
            'nama' => 'Mahmud',
            'username' => 'mahmud',
            'email' => 'mahmud@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $mahmud->assignRole('designer');






        // BJB
        //admin
        $dina = User::create([
            'nama' => 'Dina',
            'username' => 'dina',
            'email' => 'dina@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $dina->assignRole('admin');

        $aya = User::create([
            'nama' => 'Aya',
            'username' => 'aya',
            'email' => 'aya@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $aya->assignRole('admin');

        $yeni = User::create([
            'nama' => 'Yeni',
            'username' => 'yeni',
            'email' => 'yeni@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $yeni->assignRole('admin');

        $edy = User::create([
            'nama' => 'Edy admin',
            'username' => 'edyadmin',
            'email' => 'edyadmin@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $edy->assignRole('admin');

        //operator outdoor
        $aziz = User::create([
            'nama' => 'Aziz',
            'username' => 'aziz',
            'email' => 'aziz@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $aziz->assignRole('operator outdoor');

        $madon = User::create([
            'nama' => 'Madon',
            'username' => 'madon',
            'email' => 'madon@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $madon->assignRole('operator outdoor');

        // Operator indoor
        $mubin = User::create([
            'nama' => 'Mubin',
            'username' => 'mubin',
            'email' => 'mubin@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $mubin->assignRole('operator indoor');

        //operator multi
        $adit = User::create([
            'nama' => 'Adit',
            'username' => 'adit',
            'email' => 'adit@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $adit->assignRole('operator multi');

        // Designer BJB
        $riki = User::create([
            'nama' => 'Riki',
            'username' => 'riki',
            'email' => 'riki@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $riki->assignRole('designer');

        $rifa = User::create([
            'nama' => 'Rifa',
            'username' => 'rifa',
            'email' => 'rifa@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $rifa->assignRole('designer');

        $syifa = User::create([
            'nama' => 'Syifa',
            'username' => 'syifa',
            'email' => 'syifa@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $syifa->assignRole('designer');

        $fajar = User::create([
            'nama' => 'Fajar',
            'username' => 'fajar',
            'email' => 'fajar@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $fajar->assignRole('designer');

        $edy = User::create([
            'nama' => 'Edy',
            'username' => 'edy',
            'email' => 'edy@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $edy->assignRole('designer');

        $husni = User::create([
            'nama' => 'Husni',
            'username' => 'husni',
            'email' => 'husni@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $husni->assignRole('designer');






        // BJM
        //admin
        $devi = User::create([
            'nama' => 'Devi',
            'username' => 'devi',
            'email' => 'devi@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $devi->assignRole('admin');

        $dila = User::create([
            'nama' => 'Dila',
            'username' => 'dila',
            'email' => 'dila@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $dila->assignRole('admin');

        // operator
        $uji = User::create([
            'nama' => 'Uji',
            'username' => 'uji',
            'email' => 'uji@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $uji->assignRole('operator outdoor');

        $tilah = User::create([
            'nama' => 'Tilah',
            'username' => 'tilah',
            'email' => 'tilah@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $tilah->assignRole('operator outdoor');

        // operator indoor
        $usai = User::create([
            'nama' => 'Usai',
            'username' => 'usai',
            'email' => 'usai@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $usai->assignRole('operator indoor');

        //designer
        $joe = User::create([
            'nama' => 'Joe',
            'username' => 'joe',
            'email' => 'joe@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $joe->assignRole('designer');

        $iqbal = User::create([
            'nama' => 'Iqbal',
            'username' => 'iqbal',
            'email' => 'iqbal@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $iqbal->assignRole('designer');

        $lisda = User::create([
            'nama' => 'Lisda',
            'username' => 'lisda',
            'email' => 'lisda@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $lisda->assignRole('designer');

        $heny = User::create([
            'nama' => 'Heny',
            'username' => 'heny',
            'email' => 'heny@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $heny->assignRole('designer');






        // Lianganggang
        //admin
        $ila = User::create([
            'nama' => 'Ila',
            'username' => 'ila',
            'email' => 'ila@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangLgg->id,
        ]);
        $ila->assignRole('admin');

        //operator outdoor
        $budi = User::create([
            'nama' => 'Budi',
            'username' => 'budi',
            'email' => 'budi@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangLgg->id,
        ]);
        $budi->assignRole('operator outdoor');

        //designer
        $darian = User::create([
            'nama' => 'Darian',
            'username' => 'darian',
            'email' => 'darian@restuguru.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangLgg->id,
        ]);
        $darian->assignRole('operator outdoor');






        // --- 4. INVENTORY (Tetap 1 User per Bagian) ---

        // Gudang Utama
        $invUtama = User::create([
            'nama' => 'Gudang Utama',
            'username' => 'gudang',
            'email' => 'gudang@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangGDGUtama->id,
        ]);
        $invUtama->assignRole('inventory utama');

        // Gudang BJM
        $invBjm = User::create([
            'nama' => 'Gudang BJM',
            'username' => 'gudangbjm',
            'email' => 'gudangbjm@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $invBjm->assignRole('inventory cabang');

        // Gudang BJB
        $invBjb = User::create([
            'nama' => 'Gudang BJB',
            'username' => 'gudangbjb',
            'email' => 'gudangbjb@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $invBjb->assignRole('inventory cabang');

        // Gudang Lianganggang
        $invLgg = User::create([
            'nama' => 'Gudang Lianganggang',
            'username' => 'gudanglgg',
            'email' => 'gudanglgg@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangLgg->id,
        ]);
        $invLgg->assignRole('inventory cabang');

        // Gudang MTP
        $invMtp = User::create([
            'nama' => 'Gudang MTP',
            'username' => 'gudangmtp',
            'email' => 'gudangmtp@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangMtp->id,
        ]);
        $invMtp->assignRole('inventory cabang');
    }
}
