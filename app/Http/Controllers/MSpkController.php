<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MSpk;
use App\Models\User;
use App\Models\MCabang;
use App\Models\MBahanBaku;
use App\Models\MFinishing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class MSpkController extends Controller
{
    public function buat()
    {
        $user = Auth::user();

        // Cek apakah user berada di Cabang Pusat
        // Pastikan di database tabel m_cabangs kolom jenis isinya 'pusat'
        $isPusat = $user->cabang->jenis === 'pusat';

        // 1. Ambil Bahan Baku (Hanya stok tersedia)
        // Opsional: Jika bahan baku juga per cabang, tambahkan logika filter di sini juga
        $bahans = MBahanBaku::all();

        // 2. Ambil Finishing
        $finishings = MFinishing::all();

        // 3. Ambil User dengan Role Designer
        $designers = User::role('designer')
            ->when(!$isPusat, function ($query) use ($user) {
                // Jika BUKAN pusat, filter berdasarkan cabang user login
                return $query->where('cabang_id', $user->cabang_id);
            })
            ->get();

        // 4. Ambil User dengan Role Operator (Indoor/Outdoor/Multi)
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi'])
            ->when(!$isPusat, function ($query) use ($user) {
                // Jika BUKAN pusat, filter berdasarkan cabang user login
                return $query->where('cabang_id', $user->cabang_id);
            })
            ->get();

        return view('spk.designer.spk', [
            'user'      => $user,
            'title'     => 'Buat SPK',
            'bahans'    => $bahans,
            'finishings' => $finishings,
            'designers' => $designers,
            'operators' => $operators
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Mulai Query dengan Eager Loading agar hemat query database
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang']);

        // 1. Logika Filter Cabang
        // Jika user BUKAN dari pusat, filter hanya SPK cabangnya sendiri
        if ($user->cabang->jenis !== 'pusat') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // 2. Logika Pencarian (No SPK atau Nama Pelanggan)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%");
            });
        }

        // 3. Ambil data (Paginate 10 per halaman) & urutkan terbaru
        $spks = $query->latest()->paginate(10);

        return view('spk.designer.indexSpk', [
            'title' => 'Manajemen SPK',
            'spks' => $spks
        ]);
    }

    public function store(Request $request)
    {
        // 1. VALIDASI (Hapus validasi 'no_spk' dari sini karena kita generate sendiri)
        $validated = $request->validate([
            'tanggal'         => 'required',
            'jenis_order'     => 'required|in:outdoor,indoor,multi',
            'nama_pelanggan'  => 'required|string|max:255',
            'nama_file'       => 'required|string',
            'ukuran_p'        => 'required|numeric|min:0',
            'ukuran_l'        => 'required|numeric|min:0',
            'bahan_id'        => 'required|exists:m_bahan_bakus,id',
            'qty'             => 'required|integer|min:1',
            'finishing'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'designer_id'     => 'required|exists:users,id',
            'operator_id'     => 'required|exists:users,id',
        ]);

        // 2. PROSES DATABASE TRANSACTION
        // Kita bungkus semua logic generate nomor & save dalam transaction
        try {
            DB::transaction(function () use ($request) {

                $user = Auth::user();
                $cabangId = $user->cabang_id;

                // Ambil Kode Cabang (Prefix)
                // Misal: CBG-BJM -> Prefix: BJM
                $cabang = MCabang::findOrFail($cabangId);
                $fullKode = $cabang->kode;
                $prefix = \Illuminate\Support\Str::after($fullKode, '-');

                // --- LOGIKA GENERATE NOMOR URUT (RACE CONDITION PROOF) ---

                // 1. Cari SPK terakhir dari cabang ini DENGAN LOCK
                // lockForUpdate() akan menahan row ini sampai transaction selesai.
                // Request lain yang mencoba baca akan menunggu antrian.
                $lastSpk = MSpk::where('cabang_id', $cabangId)
                    ->where('no_spk', 'LIKE', "$prefix-%") // Filter sesuai prefix cabang
                    ->orderBy('id', 'desc')
                    ->lockForUpdate() // KUNCI UTAMA: Locking baris terakhir
                    ->first();

                if ($lastSpk) {
                    // Pecah string "BJM-000001" ambil angka belakangnya
                    $lastNumber = (int) \Illuminate\Support\Str::afterLast($lastSpk->no_spk, '-');
                    $nextNumber = $lastNumber + 1;
                } else {
                    // Jika belum ada data sama sekali
                    $nextNumber = 1;
                }

                // Format jadi 6 digit: 000001
                $newNoSpk = $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // --- FORMAT TANGGAL ---
                try {
                    $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
                } catch (\Exception $e) {
                    $formattedDate = now()->format('Y-m-d');
                }

                // --- SIMPAN DATA ---
                MSpk::create([
                    'no_spk'          => $newNoSpk, // Nomor hasil generate
                    'tanggal_spk'     => $formattedDate,
                    'jenis_order_spk' => $request->jenis_order,
                    'nama_pelanggan'  => $request->nama_pelanggan,
                    'nama_file'       => $request->nama_file,
                    'ukuran_panjang'  => $request->ukuran_p,
                    'ukuran_lebar'    => $request->ukuran_l,
                    'bahan_id'        => $request->bahan_id,
                    'kuantitas'       => $request->qty,
                    'finishing'       => $request->finishing,
                    'keterangan'      => $request->catatan,
                    'designer_id'     => $request->designer_id,
                    'operator_id'     => $request->operator_id,
                    'cabang_id'       => $cabangId,
                ]);
            });

            return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dibuat!');
        } catch (\Exception $e) {
            // Jika terjadi error (misal deadlock), kembalikan ke form
            return back()->with('error', 'Gagal membuat SPK. Silakan coba lagi. Error: ' . $e->getMessage())->withInput();
        }
    }

    // Halaman Edit SPK
    public function edit($id)
    {
        $spk = MSpk::findOrFail($id);

        // Pastikan user hanya bisa edit SPK cabangnya sendiri (kecuali admin pusat)
        if (Auth::user()->cabang->jenis !== 'pusat' && $spk->cabang_id !== Auth::user()->cabang_id) {
            abort(403, 'Akses ditolak');
        }

        $bahans = MBahanBaku::all();
        $finishings = MFinishing::all();

        // Ambil data designer & operator (sesuai cabang)
        $cabangId = $spk->cabang_id;
        $designers = User::role('designer')->where('cabang_id', $cabangId)->get();
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi'])
            ->where('cabang_id', $cabangId)->get();

        return view('spk.admin.editSpk', [
            'title'      => 'Edit SPK',
            'spk'        => $spk,
            'bahans'     => $bahans,
            'finishings' => $finishings,
            'designers'  => $designers,
            'operators'  => $operators,
        ]);
    }

    // Proses Update SPK
    public function update(Request $request, $id)
    {
        $spk = MSpk::findOrFail($id);

        // Validasi
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            // No Telp sekarang bisa diedit, wajib angka 10-13 digit
            'no_telp'        => 'required|numeric|digits_between:10,13',
            'nama_file'      => 'required|string',
            'ukuran_p'       => 'required|numeric|min:0',
            'ukuran_l'       => 'required|numeric|min:0',
            'bahan_id'       => 'required|exists:m_bahan_bakus,id',
            'qty'            => 'required|integer|min:1',
            'finishing'      => 'nullable|string',
            'catatan'        => 'nullable|string',
            'designer_id'    => 'required|exists:users,id',
            'operator_id'    => 'required|exists:users,id',
            'jenis_order'    => 'required|in:outdoor,indoor,multi',
        ], [
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.digits_between' => 'Nomor telepon tidak valid (10-13 digit).',
        ]);

        // Update Data
        $spk->update([
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_telepon'      => $request->no_telp, // Update No Telepon
            'nama_file'       => $request->nama_file,
            'ukuran_panjang'  => $request->ukuran_p,
            'ukuran_lebar'    => $request->ukuran_l,
            'bahan_id'        => $request->bahan_id,
            'kuantitas'       => $request->qty,
            'finishing'       => $request->finishing,
            'keterangan'      => $request->catatan,
            'designer_id'     => $request->designer_id,
            'operator_id'     => $request->operator_id,
            'jenis_order_spk' => $request->jenis_order,
        ]);

        return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui!');
    }

    public function destroy(MSpk $spk)
    {
        $spk->delete();
        return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dihapus!');
    }

    public function cetakSpk($id)
    {
        // Hanya ambil data SPK dan relasinya
        $spk = MSpk::with(['bahan', 'designer', 'operator', 'cabang'])->findOrFail($id);

        return view('spk.nota_spk.notaspk', compact('spk'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_spk' => 'required|in:pending,acc,reject',
        ]);

        $spk = MSpk::findOrFail($id);
        $spk->status_spk = $request->status_spk;

        // Opsional: Jika status ACC, status produksi otomatis jadi ongoing?
        // if ($request->status_spk == 'acc') {
        //     $spk->status_produksi = 'ongoing';
        // }

        $spk->save();

        return redirect()->back()->with('success', 'Status SPK berhasil diperbarui!');
    }

    public function operatorIndex(Request $request)
    {
        $user = Auth::user();

        // 1. Query Dasar: Ambil SPK beserta relasinya
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang']);

        // 2. Filter Wajib: Cabang, Status SPK (ACC), Status Produksi (Ongoing)
        if ($user->cabang->jenis !== 'pusat') {
            $query->where('cabang_id', $user->cabang_id);
        }

        $query->where('status_spk', 'acc')
            ->where('status_produksi', 'pending')
            ->orWhere('status_produksi', 'ripping')
            ->orWhere('status_produksi', 'ongoing')
            ->orWhere('status_produksi', 'finishing');
        // dd($query);

        // 3. Filter Berdasarkan Role Operator
        // Logika: Jika user punya role 'operator indoor', tampilkan hanya order 'indoor', dst.
        // Kita gunakan grouping (where function) untuk antisipasi jika user punya multiple role
        $query->where(function (Builder $q) use ($user) {
            $hasFilter = false;

            if ($user->hasRole('operator indoor')) {
                $q->orWhere('jenis_order_spk', 'indoor');
                $hasFilter = true;
            }

            if ($user->hasRole('operator outdoor')) {
                $q->orWhere('jenis_order_spk', 'outdoor');
                $hasFilter = true;
            }

            if ($user->hasRole('operator multi')) {
                $q->orWhere('jenis_order_spk', 'multi');
                $hasFilter = true;
            }

            // Fallback: Jika user ditugaskan secara spesifik (by ID) meskipun jenisnya beda
            $q->orWhere('operator_id', $user->id);

            // Jika tidak punya role spesifik di atas (misal admin ngetest), tampilkan semua/kosongkan
            if (!$hasFilter && !$user->hasRole('admin')) {
                // Opsional: cegah akses atau tampilkan kosong
                // $q->whereRaw('1 = 0');
            }
        });

        // 4. Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%");
            });
        }

        $spks = $query->latest()->paginate(10);

        return view('spk.operator.indexSpk', [
            'title' => 'Produksi SPK',
            'spks' => $spks
        ]);
    }

    // Method untuk Operator Menyelesaikan Pekerjaan
    public function updateStatusProduksi(Request $request, $id)
    {
        $request->validate([
            'status_produksi' => 'required|in:pending,ripping,ongoing,finishing,done',
            'catatan_operator' => 'nullable|string',
        ]);

        $spk = MSpk::findOrFail($id);

        // Validasi: SPK harus sudah di-ACC sebelum diproduksi
        if ($spk->status_spk != 'acc') {
            return back()->with('error', 'Gagal! SPK ini belum di-ACC oleh manajemen.');
        }

        $spk->status_produksi = $request->status_produksi;
        $spk->catatan_operator = $request->catatan_operator;
        $spk->save();

        return back()->with('success', 'Status produksi dan catatan berhasil diperbarui!');
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        // 1. Inisialisasi Query Awal
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang']);

        // 2. Filter Cabang (Jika bukan pusat, hanya lihat cabang sendiri)
        if ($user->cabang->jenis !== 'pusat') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // 3. Filter Status Produksi "DONE" (Selesai)
        $query->where('status_produksi', 'done');

        // 4. Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%");
            });
        }

        // 5. Eksekusi Data (Pagination)
        // Gunakan paginate() di akhir setelah semua filter diterapkan
        $spks = $query->latest()->paginate(10);

        return view('spk.operator.riwayatSpk', [
            'title' => 'Riwayat Produksi Selesai',
            'spks' => $spks
        ]);
    }
}
