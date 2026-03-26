<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\MCabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

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

    public function getOperatorsByCabang(Request $request, $cabangId)
    {
        $jenisOrder = $request->query('jenis');

        $query = User::query(); // Mulai query kosong

        // Cek: Jika $cabangId BUKAN 'all', filter berdasarkan cabang.
        // Jika 'all', abaikan filter cabang ini agar semua tampil.
        if ($cabangId !== 'all') {
            $query->where('cabang_id', $cabangId);
        }

        // Filter berdasarkan jenis order tetap jalan, baik lembur maupun tidak
        if ($jenisOrder === 'outdoor') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Operator Outdoor');
            });
        } elseif ($jenisOrder === 'indoor') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Operator Indoor');
            });
        } elseif ($jenisOrder === 'multi') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Operator Multi');
            });
        } elseif ($jenisOrder === 'dtf') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Operator DTF');
            });
        }

        $operators = $query->get()->map(function ($op) {
            return [
                'id' => $op->id,
                'nama' => $op->nama,
                'roles' => $op->roles->pluck('name')->implode(', ')
            ];
        });

        return response()->json($operators);
    }

    public function indexSetting()
    {
        return view('spk.layout.userSetting', [
            'title' => 'User Setting',
            'user' => auth()->user(),
        ]);
    }
    
    public function updateUser(Request $request)
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Validasi Input dari Form
        // Aturan 'confirmed' otomatis akan mencocokkan field 'password' dengan 'password_confirmation'
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ], [
            // Kustomisasi pesan error (opsional) agar lebih mudah dipahami user
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal harus 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 3. Cek apakah password lama yang diinput SESUAI dengan password di Database
        if (!Hash::check($request->current_password, $user->password)) {
            // Jika salah, kembalikan ke halaman sebelumnya dengan pesan error
            return back()->with('error', 'Password lama yang Anda masukkan salah!');
        }

        // 4. Jika password lama benar, Update ke password baru
        // Kita gunakan Hash::make() agar password dienkripsi dengan aman
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 5. Kembalikan dengan notifikasi sukses (SweetAlert di View akan menangkap ini)
        return back()->with('success', 'Password Anda berhasil diperbarui!');
    }
}
