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
            'email' => 'gudang-utama@example.com',
            'telepon' => '0811000000',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'jenis' => 'pusat',
        ]);

        $cabangUtama = MCabang::create([
            'kode' => 'CBG-UTM',
            'nama' => 'Cabang Utama',
            'slug' => 'cabang-pusat',
            'email' => 'utama@example.com',
            'telepon' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'jenis' => 'pusat',
        ]);

        $cabangBjm = MCabang::create([
            'kode' => 'CBG-BJM',
            'nama' => 'Cabang Banjarmasin',
            'slug' => 'cabang-banjarmasin',
            'email' => 'banjarmasin@example.com',
            'telepon' => '08234567890',
            'alamat' => 'Jl. Veteran No. 2, Banjarmasin',
            'jenis' => 'cabang',
        ]);

        $cabangBjb = MCabang::create([
            'kode' => 'CBG-BJB',
            'nama' => 'Cabang Banjarbaru',
            'slug' => 'cabang-banjarbaru',
            'email' => 'banjarbaru@example.com',
            'telepon' => '08234567890',
            'alamat' => 'Jl. Veteran No. 3, Banjarbaru',
            'jenis' => 'cabang',
        ]);

        // --- 2. Fungsi Helper untuk membuat User A & B ---
        $createUsers = function($cabang, $role, $prefixName, $usernameBase) {
            $suffix = ['a', 'b'];
            foreach ($suffix as $s) {
                $user = User::create([
                    'nama' => $prefixName . ' ' . strtoupper($s),
                    'username' => $usernameBase . $s,
                    'email' => $usernameBase . $s . '@example.com',
                    'password' => Hash::make('password'),
                    'cabang_id' => $cabang->id,
                ]);
                $user->assignRole($role);
            }
        };

        // --- 3. Implementasi User A & B per Cabang ---

        // MANAJEMEN (Di Cabang Utama)
        $createUsers($cabangUtama, 'manajemen', 'Manajemen', 'manajemen');

        // ADMIN per Cabang
        $createUsers($cabangBjm, 'admin', 'Admin BJM', 'adminbjm');
        $createUsers($cabangBjb, 'admin', 'Admin BJB', 'adminbjb');

        // DESIGNER per Cabang
        $createUsers($cabangBjm, 'designer', 'Designer BJM', 'designerbjm');
        $createUsers($cabangBjb, 'designer', 'Designer BJB', 'designerbjb');

        // OPERATOR INDOOR per Cabang
        $createUsers($cabangBjm, 'operator indoor', 'Op Indoor BJM', 'opindoorbjm');
        $createUsers($cabangBjb, 'operator indoor', 'Op Indoor BJB', 'opindoorbjb');

        // OPERATOR OUTDOOR per Cabang
        $createUsers($cabangBjm, 'operator outdoor', 'Op Outdoor BJM', 'opoutdoorbjm');
        $createUsers($cabangBjb, 'operator outdoor', 'Op Outdoor BJB', 'opoutdoorbjb');

        // OPERATOR MULTI per Cabang
        $createUsers($cabangBjm, 'operator multi', 'Op Multi BJM', 'opmultibjm');
        $createUsers($cabangBjb, 'operator multi', 'Op Multi BJB', 'opmultibjb');


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
    }
}
