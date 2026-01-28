<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MSpk;
use App\Models\User;
use App\Models\MBahanBaku;
use Illuminate\Http\Request;
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

        // 2. Ambil User dengan Role Designer
        $designers = User::role('designer')
            ->when(!$isPusat, function ($query) use ($user) {
                // Jika BUKAN pusat, filter berdasarkan cabang user login
                return $query->where('cabang_id', $user->cabang_id);
            })
            ->get();

        // 3. Ambil User dengan Role Operator (Indoor/Outdoor/Multi)
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
        // --- 1. VALIDASI DATA ---
        $validated = $request->validate([
            'no_spk'          => 'required|unique:m_spks,no_spk',
            'tanggal'         => 'required',
            'jenis_order'     => 'required|in:outdoor,indoor,multi',
            'nama_pelanggan'  => 'required|string|max:255',

            // Validasi Telepon: Wajib angka, min 10 digit, max 13 digit
            'no_telp'         => 'required|numeric|digits_between:10,13',

            'nama_file'       => 'required|string',
            'ukuran_p'        => 'required|numeric|min:0',
            'ukuran_l'        => 'required|numeric|min:0',
            'bahan_id'        => 'required|exists:m_bahan_bakus,id',
            'qty'             => 'required|integer|min:1',
            'finishing'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'designer_id'     => 'required|exists:users,id',
            'operator_id'     => 'required|exists:users,id',
        ], [
            // Custom Error Messages (Agar User Paham)
            'no_telp.required'       => 'Nomor telepon wajib diisi.',
            'no_telp.numeric'        => 'Nomor telepon harus berupa angka.',
            'no_telp.digits_between' => 'Nomor WhatsApp tidak valid (harus 10-13 digit).',
            'bahan_id.required'      => 'Silakan pilih bahan baku.',
            'designer_id.required'   => 'Silakan pilih designer.',
            'operator_id.required'   => 'Silakan pilih operator.',
        ]);

        // --- 2. FORMAT TANGGAL ---
        // Ubah dari d-m-Y (View) ke Y-m-d (Database)
        try {
            $formattedDate = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
        } catch (\Exception $e) {
            $formattedDate = now()->format('Y-m-d');
        }

        // --- 3. SIMPAN KE DATABASE ---
        MSpk::create([
            'no_spk'          => $request->no_spk,
            'tanggal_spk'     => $formattedDate,
            'jenis_order_spk' => $request->jenis_order,
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_telepon'      => $request->no_telp, // Pastikan kolom di DB 'no_telepon'
            'nama_file'       => $request->nama_file,
            'ukuran_panjang'  => $request->ukuran_p,
            'ukuran_lebar'    => $request->ukuran_l,
            'bahan_id'        => $request->bahan_id,
            'kuantitas'       => $request->qty,
            'finishing'       => $request->finishing,
            'keterangan'      => $request->catatan,
            'designer_id'     => $request->designer_id,
            'operator_id'     => $request->operator_id,
            'cabang_id'       => Auth::user()->cabang_id, // Ambil otomatis dari user login
        ]);

        return redirect()->route('designer.spk.index')->with('success', 'SPK Berhasil Dibuat!');
    }

    public function destroy(MSpk $spk)
    {
        $spk->delete();
        return redirect()->route('designer.spk.index')->with('success', 'SPK Berhasil Dihapus!');
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
        if ($request->status_spk == 'acc') {
            $spk->status_produksi = 'ongoing';
        }

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
            ->where('status_produksi', 'ongoing');

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
    public function selesaiProduksi($id)
    {
        $spk = MSpk::findOrFail($id);

        // Validasi sederhana
        if ($spk->status_spk != 'acc') {
            return back()->with('error', 'SPK belum di-ACC!');
        }

        $spk->status_produksi = 'done';
        $spk->save();

        return back()->with('success', 'Status produksi berhasil diubah menjadi Selesai!');
    }
}
