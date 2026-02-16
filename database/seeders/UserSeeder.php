<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MCabang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Cabang
        $c = [
            'gdg' => MCabang::create(['kode' => 'GDG-UTM', 'nama' => 'Gudang Utama', 'slug' => 'gudang-utama', 'email' => 'gudang-utama@restuguru.com', 'telepon' => '0811000000', 'alamat' => 'Jl. Merdeka No. 1, Jkt', 'jenis' => 'pusat']),
            'utm' => MCabang::create(['kode' => 'CBG-UTM', 'nama' => 'Cabang Utama', 'slug' => 'cabang-pusat', 'email' => 'utama@restuguru.com', 'telepon' => '08123456789', 'alamat' => 'Jl. Merdeka No. 1, Jkt', 'jenis' => 'pusat']),
            'bjm' => MCabang::create(['kode' => 'CBG-BJM', 'nama' => 'Cabang Banjarmasin', 'slug' => 'cabang-banjarmasin', 'email' => 'banjarmasin@restuguru.com', 'telepon' => '08234567890', 'alamat' => 'Jl. Veteran No. 2, Bjm', 'jenis' => 'cabang']),
            'bjb' => MCabang::create(['kode' => 'CBG-BJB', 'nama' => 'Cabang Banjarbaru', 'slug' => 'cabang-banjarbaru', 'email' => 'banjarbaru@restuguru.com', 'telepon' => '08234567890', 'alamat' => 'Jl. Veteran No. 3, Bjb', 'jenis' => 'cabang']),
            'mtp' => MCabang::create(['kode' => 'CBG-MTP', 'nama' => 'Cabang Martapura', 'slug' => 'cabang-martapura', 'email' => 'Martapura@restuguru.com', 'telepon' => '08234567890', 'alamat' => 'Martapura, Cindai Alus', 'jenis' => 'cabang']),
            'lgg' => MCabang::create(['kode' => 'CBG-LGG', 'nama' => 'Cabang Liangangggang', 'slug' => 'cabang-liangangggang', 'email' => 'Liangangggang@restuguru.com', 'telepon' => '08234567891', 'alamat' => 'Jl. A. Yani No.17', 'jenis' => 'cabang']),
        ];

        // 2. Buat User Berdasarkan Role & Cabang
        $this->createUser('Manajemen', 'manajemen', 'manajemen@restuguru.com', 'manajemen', $c['utm']->id);

        // MARTAPURA (MTP)
        foreach (['rahmawati', 'egydinda', 'zaidadmin', 'rizkymahdini'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'admin', $c['mtp']->id);
        $this->createUser('Operator Outdoor MTP', 'outdoormartapura', 'outdoormtp@restuguru.com', 'operator outdoor', $c['mtp']->id);
        $this->createUser('Operator Indoor MTP', 'indoormartapura', 'indoormtp@restuguru.com', 'operator indoor', $c['mtp']->id);
        $this->createUser('Operator Multi MTP', 'multimartapura', 'multimtp@restuguru.com', 'operator multi', $c['mtp']->id);
        $this->createUser('Operator DTF UV MTP', 'dtfmartapura', 'dtfmtp@restuguru.com', 'operator dtf', $c['mtp']->id);
        $this->createUser('Adversting MTP', 'advertisingmartapura', 'advertisingmtp@restuguru.com', 'advertising', $c['mtp']->id);
        foreach (['zaid', 'mila', 'dini', 'wawa', 'alif', 'indah', 'mahmud'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'designer', $c['mtp']->id);

        // BANJARBARU (BJB)
        foreach (['dina', 'aya', 'yeni', 'edyadmin'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'admin', $c['bjb']->id);
        $this->createUser('Operator Outdoor BJB', 'outdoorbanjarbaru', 'outdoorbjb@restuguru.com', 'operator outdoor', $c['bjb']->id);
        $this->createUser('Operator Indoor BJB', 'indoorbanjarbaru', 'indoorbjb@restuguru.com', 'operator indoor', $c['bjb']->id);
        $this->createUser('Operator Multi BJB', 'multibanjarbaru', 'multibbjb@restuguru.com', 'operator multi', $c['bjb']->id);
        $this->createUser('Operator DTF UV BJB', 'dtfbanjarbaru', 'dtfbjb@restuguru.com', 'operator dtf', $c['bjb']->id);
        $this->createUser('Adversting BJB', 'advertisingbanjarbaru', 'advertisingbjb@restuguru.com', 'advertising', $c['bjb']->id);
        foreach (['riki', 'rifa', 'syifa', 'fajar', 'edy', 'husni'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'designer', $c['bjb']->id);

        // BANJARMASIN (BJM)
        foreach (['devi', 'dila'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'admin', $c['bjm']->id);
        $this->createUser('Operator Outdoor BJM', 'outdoorbanjarmasin', 'outdoorbjm@restuguru.com', 'operator outdoor', $c['bjm']->id);
        $this->createUser('Operator Indoor BJM', 'indoorbanjarmasin', 'indoorbjm@restuguru.com', 'operator indoor', $c['bjm']->id);
        $this->createUser('Operator Multi BJM', 'multibanjarmasin', 'multibbjm@restuguru.com', 'operator multi', $c['bjm']->id);
        $this->createUser('Operator DTF UV BJM', 'dtfbanjarmasin', 'dtfbjm@restuguru.com', 'operator dtf', $c['bjm']->id);
        $this->createUser('Adversting BJM', 'advertisingbanjarmasin', 'advertisingbjm@restuguru.com', 'advertising', $c['bjm']->id);
        foreach (['joe', 'iqbal', 'lisda', 'heny'] as $u) $this->createUser(ucfirst($u), $u, "$u@restuguru.com", 'designer', $c['bjm']->id);

        // LIANGANGGANG (LGG)
        $this->createUser('Ila', 'ila', 'ila@restuguru.com', 'admin', $c['lgg']->id);
        $this->createUser('Operator Outdoor LGG', 'outdoorlianganggang', 'outdoorlgg@restuguru.com', 'operator outdoor', $c['lgg']->id);
        $this->createUser('Operator Indoor LGG', 'indoorlianganggang', 'indoorlgg@restuguru.com', 'operator indoor', $c['lgg']->id);
        $this->createUser('Operator Multi LGG', 'multilianganggang', 'multilgg@restuguru.com', 'operator multi', $c['lgg']->id);
        $this->createUser('Operator DTF UV LGG', 'dtflianganggang', 'dtflgg@restuguru.com', 'operator dtf', $c['lgg']->id);
        $this->createUser('Adversting LGG', 'advertisinglianganggang', 'advertisinglgg@restuguru.com', 'advertising', $c['lgg']->id);
        $this->createUser('Darian', 'darian', 'darian@restuguru.com', 'designer', $c['lgg']->id);

        // INVENTORY
        $this->createUser('Gudang Utama', 'gudang', 'gudang@example.com', 'inventory utama', $c['gdg']->id);
        foreach (['bjm', 'bjb', 'lgg', 'mtp'] as $key) {
            $this->createUser("Gudang ".strtoupper($key), "gudang$key", "gudang$key@example.com", 'inventory cabang', $c[$key]->id);
        }
    }

    /**
     * Fungsi Helper untuk membuat User & Assign Role
     */
    private function createUser($nama, $username, $email, $role, $cabang_id)
    {
        $user = User::create([
            'nama' => $nama,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('password'),
            'cabang_id' => $cabang_id,
        ]);
        $user->assignRole($role);
        return $user;
    }
}
