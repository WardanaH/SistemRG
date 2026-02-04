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
        // --- Buat 2 cabang ---
        $cabangGDGUtama = MCabang::create([
            'kode' => 'GDG-UTM',
            'nama' => 'Gudang Utama',
            'slug' => 'gudang-utama',
            'email' => 'utama@example.com',
            'telepon' => '08123456789',
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
            'alamat' => 'Jl. Veteran No. 2, Banjarmasin',
            'jenis' => 'cabang',
        ]);

        // --- Buat user Manajemen ---
        $admin = User::create([
            'nama' => 'Manajemen',
            'username' => 'manajemen',
            'email' => 'manajemen@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangUtama->id,
        ]);
        $admin->assignRole('manajemen');

        // --- Buat user Admin BJM ---
        $admin = User::create([
            'nama' => 'Admin Banjarmasin',
            'username' => 'adminbjm',
            'email' => 'adminbanjarmasin@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $admin->assignRole('admin');

        // --- Buat user Admin BJB ---
        $admin = User::create([
            'nama' => 'Admin Banjarbaru',
            'username' => 'adminbjb',
            'email' => 'adminbanjarbaru@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $admin->assignRole('admin');

        // --- Buat user Operator Indoor ---
        $operatorIndoor = User::create([
            'nama' => 'Operator Cabang bjm',
            'username' => 'operatorIndoor',
            'email' => 'operatorOutdoor@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $operatorIndoor->assignRole('operator indoor');

        // --- Buat user Operator Outdoor ---
        $operatorOutdoor = User::create([
            'nama' => 'Operator Cabang bjm',
            'username' => 'operatorOutdoor',
            'email' => 'operatorIndoor@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $operatorOutdoor->assignRole('operator outdoor');

        // --- Buat user Operator Multi ---
        $operatorMulti = User::create([
            'nama' => 'Operator Cabang bjm',
            'username' => 'operatorMulti',
            'email' => 'operatorMulti@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $operatorMulti->assignRole('operator multi');

        // --- Buat user Operator Indoor ---
        $operatorIndoor = User::create([
            'nama' => 'Operator Cabang bjb',
            'username' => 'operatorIndoorbjb',
            'email' => 'operatorOutdoorbjb@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $operatorIndoor->assignRole('operator indoor');

        // --- Buat user Operator Outdoor ---
        $operatorOutdoor = User::create([
            'nama' => 'Operator Cabang bjb',
            'username' => 'operatorOutdoorbjb',
            'email' => 'operatorIndoorbjb@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $operatorOutdoor->assignRole('operator outdoor');

        // --- Buat user Operator Multi ---
        $operatorMulti = User::create([
            'nama' => 'Operator Cabang bjb',
            'username' => 'operatorMultibjb',
            'email' => 'operatorMultibjb@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjb->id,
        ]);
        $operatorMulti->assignRole('operator multi');

        // --- Buat user Designer ---
        $operatorMulti = User::create([
            'nama' => 'Designer',
            'username' => 'designer',
            'email' => 'designer@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $operatorMulti->assignRole('designer');

        // --- Buat user Gudang utama ---
        $admin = User::create([
            'nama' => 'gudang',
            'username' => 'gudang',
            'email' => 'gudang@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangGDGUtama->id,
        ]);
        $admin->assignRole('inventory utama');

        // --- Buat user Gudang bjm ---
        $admin = User::create([
            'nama' => 'gudang bjm',
            'username' => 'gudangbjm',
            'email' => 'gudangbjm@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangBjm->id,
        ]);
        $admin->assignRole('inventory cabang');

        // --- Buat user Gudang bjb ---
        $admin = User::create([
            'nama' => 'gudang bjb',
            'username' => 'gudangbjb',
            'email' => 'gudangbjb@example.com',
            'password' => Hash::make('password'),
            'cabang_id' => $cabangGDGUtama->id,
        ]);
        $admin->assignRole('inventory cabang');
    }
}
