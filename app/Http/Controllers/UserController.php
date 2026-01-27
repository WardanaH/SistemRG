<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MCabang;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // ambil data yang diperlukan untuk tabel + form tambah user di halaman index
        $users = User::with('cabang', 'roles')->paginate(1000);
        $roles = Role::all();         // <-- pastikan ini ada
        $cabangs = MCabang::all();    // <-- dan ini juga
        $title = 'Manajemen User';

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
        // $request->validate([
        //     'file_excel' => 'required|mimes:xlsx,xls'
        // ]);

        // try {
        //     Excel::import(new UsersImport, $request->file('file_excel'));

        //     // Log Aktivitas
        //     $isi = auth()->user()->username . " telah mengimpor banyak user via Excel.";
        //     $this->log($isi, "Penambahan");

        //     return redirect()->route('users.index')->with('success', 'Data karyawan berhasil diimpor.');
        // } catch (\Exception $e) {
        //     Log::error('Terjadi kesalahan: ' . $e->getMessage());
        //     return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        // }
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name');
        $cabangs = MCabang::pluck('nama', 'id');

        return view('admin.users.edit', compact('user', 'roles', 'cabangs'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        $isi = auth()->user()->username . " telah mengedit user " . $user->nama . ".";
        $this->log($isi, "Pengubahan");

        return redirect()->route('manajemen.user')->with('success', 'User diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        $isi = auth()->user()->username . " telah menghapus user " . $user->nama . ".";
        $this->log($isi, "Penghapusan");

        return redirect()->route('manajemen.user')->with('success', 'User dihapus.');
    }
}
