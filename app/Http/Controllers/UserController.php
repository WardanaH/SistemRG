<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MCabang;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all();
        $cabangs = MCabang::all();
        $title = 'Manajemen User';

        // Logika Pencarian
        $query = User::with('cabang', 'roles');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('cabang', function ($c) use ($search) {
                        $c->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Pagination: 10 data per halaman
        $users = $query->latest()->paginate(10);

        return view('spk.manajemen.user', compact('users', 'roles', 'cabangs', 'title'));
    }

    public function create()
    {
        // jika kamu menggunakan halaman create terpisah
        $roles = Role::all();
        $cabangs = MCabang::all();
        return view('spk.manajemen.create', compact('roles', 'cabangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|unique:users,username',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'telepon'   => 'nullable|string',
            'gaji'      => 'nullable|numeric',
            'alamat'    => 'nullable|string',
            'cabang_id' => 'nullable|exists:m_cabangs,id',
            'role'      => 'required|string|exists:roles,name'
        ]);

        $user = User::create([
            'nama'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'] ?? null,
            'password'  => Hash::make($validated['password']),
            'telepon'   => $validated['telepon'] ?? null,
            'gaji'      => $validated['gaji'] ?? null,
            'alamat'    => $validated['alamat'] ?? null,
            'cabang_id' => $validated['cabang_id'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        $isi = auth()
            ->user()
            ->username . " telah menambahkan user " . $user->nama . ".";
        $this->log($isi, "Penambahan");

        return redirect()->route('manajemen.user')->with('success', 'User berhasil dibuat.');
    }

    public function importCsv(Request $request)
    {
        // 1. Validasi File
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048' // Max 2MB
        ]);
        // dd($request->file('file_excel'));

        try {
            // 2. Proses Import
            Excel::import(new UsersImport, $request->file('file_excel'));

            // 3. Log (Opsional, sesuaikan dengan sistem log Anda)
            // $this->log("Mengimport data user via Excel", "Import");

            return redirect()->route('manajemen.user')->with('success', 'Data User berhasil diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Error validasi Excel spesifik
            Log::error('Import Error: ' . $e->getMessage());
            $failures = $e->failures();
            return redirect()->back()->with('error', 'Gagal Validasi Excel di baris ke-' . $failures[0]->row());
        } catch (\Exception $e) {
            // Error umum
            Log::error('Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        // 1. Validasi Input sesuai Form Modal
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'      => 'required|exists:roles,name', // Input 'role' dari select option
            'cabang_id' => 'nullable', // Boleh null jika admin pusat
            'password'  => 'nullable|min:6', // Password opsional saat edit
            'telepon'   => 'nullable|string',
            'gaji'      => 'nullable|numeric',
            'alamat'    => 'nullable|string',
        ]);

        // 2. Siapkan Data Update
        // Hati-hati: Input form name="name", tapi kolom DB biasanya "nama"
        $dataUpdate = [
            'nama'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'cabang_id' => $request->cabang_id,
            'telepon'   => $request->telepon,
            'gaji'      => $request->gaji,
            'alamat'    => $request->alamat,
        ];

        // 3. Cek Password (Hanya update jika diisi)
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        // 4. Update Data User
        $user->update($dataUpdate);

        // 5. Update Role (Spatie)
        // Gunakan syncRoles untuk mengganti role lama dengan yang baru
        $user->syncRoles($request->role);

        // 6. Log Aktivitas (Opsional)
        // Pastikan method log() ada di Controller atau Trait Anda
        if (method_exists($this, 'log')) {
            $isi = auth()->user()->username . " telah memperbarui data user " . $user->nama . ".";
            $this->log($isi, "Pengubahan");
        }

        // 7. Kembali ke halaman index
        return redirect()->route('manajemen.user')->with('success', 'Data User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        $isi = auth()->user()->username . " telah menghapus user " . $user->nama . ".";
        $this->log($isi, "Penghapusan");

        return redirect()->route('manajemen.user')->with('success', 'User dihapus.');
    }

    public function getOperatorsByCabang()
    {
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'roles' => $user->getRoleNames()->implode(', ')
                ];
            });

        return response()->json($operators);
    }
}
