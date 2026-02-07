<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Lewati jika data penting kosong
            if (!isset($row['username']) || !isset($row['email'])) {
                continue;
            }

            // 2. Buat User Baru
            $user = User::create([
                'nama'      => $row['nama'],
                'username'  => $row['username'],
                'email'     => $row['email'],
                'password'  => Hash::make($row['password']), // Hash password
                'cabang_id' => $row['cabang_id'], // Pastikan di Excel isinya angka ID Cabang
                'telepon'   => $row['telepon'] ?? null,
                'gaji'      => $row['gaji'] ?? 0,
                'alamat'    => $row['alamat'] ?? null,
            ]);

            // 3. Assign Role (Pastikan nama role di Excel sesuai dengan database, misal: 'admin', 'designer')
            if (isset($row['role'])) {
                // Cek apakah role ada di database untuk mencegah error
                if (Role::where('name', $row['role'])->exists()) {
                    $user->assignRole($row['role']);
                }
            }
        }
    }
}
