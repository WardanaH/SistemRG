<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MSpk;
use App\Models\User;
use App\Models\MCabang;
use App\Models\MBahanBaku;
use App\Models\MFinishing;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SpkBantuanController extends Controller
{
    // MENAMPILKAN DAFTAR SPK BANTUAN SAJA
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mulai Query dengan Eager Loading agar hemat query database
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang', 'cabangAsal'])
            ->where('is_bantuan', true);

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
        // dd($spks->get());

        return view('spk.designer.indexSpkBantuan', [
            'title' => 'Manajemen SPK',
            'spks' => $spks
        ]);
    }

    // FORM INPUT KHUSUS SPK BANTUAN
    public function create()
    {
        $user = Auth::user();

        // Ambil Cabang Lain (Pengirim)
        $cabangLain = MCabang::where('id', '!=', $user->cabang_id)
            ->where('jenis', '!=', 'pusat')
            ->get();
        $bahans = MBahanBaku::all();
        $finishings = MFinishing::all();

        // Operator Lokal
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi'])
            ->where('cabang_id', $user->cabang_id)->get();

        return view('spk.designer.spkBantuan', [
            'title' => 'Input SPK Bantuan',
            'user' => $user,
            'cabangLain' => $cabangLain,
            'bahans' => $bahans,
            'finishings' => $finishings,
            'operators' => $operators
        ]);
    }

    // SIMPAN SPK BANTUAN (OTOMATIS BBJM...)
    public function store(Request $request)
    {
        $request->validate([
            'asal_cabang_id' => 'required|exists:m_cabangs,id',
            'nama_file' => 'required',
            'bahan_id' => 'required',
            'qty' => 'required|min:1',
            'operator_id' => 'required',
            'nama_pelanggan' => 'required',
            'no_telepon' => 'required', // Wajib karena info dari luar
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $cabangKode = $user->cabang->kode;
                $prefix = Str::after($cabangKode, '-');

                // Prefix Khusus Bantuan: B + KodeCabang (Misal: BBJM)
                $finalPrefix = 'B' . $prefix;

                // Generate Nomor
                $lastSpk = MSpk::where('cabang_id', $user->cabang_id)
                    ->where('no_spk', 'like', $finalPrefix . '-%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastSpk ? ((int) Str::afterLast($lastSpk->no_spk, '-') + 1) : 1;
                $newNoSpk = $finalPrefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Format Tanggal
                $tgl = $request->tanggal ? Carbon::createFromFormat('d-m-Y', $request->tanggal) : now();

                MSpk::create([
                    'no_spk' => $newNoSpk,
                    'is_bantuan' => true, // FLAG PENTING
                    'asal_cabang_id' => $request->asal_cabang_id, // FLAG PENTING
                    'cabang_id' => $user->cabang_id, // Cabang pembuat (Penerima Order)

                    'tanggal_spk' => $tgl,
                    'jenis_order_spk' => $request->jenis_order,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon' => $request->no_telepon,
                    'nama_file' => $request->nama_file,
                    'ukuran_panjang' => $request->ukuran_p,
                    'ukuran_lebar' => $request->ukuran_l,
                    'bahan_id' => $request->bahan_id,
                    'kuantitas' => $request->qty,
                    'finishing' => $request->finishing,
                    'keterangan' => $request->catatan,
                    'designer_id' => $user->id, // Designer yang login
                    'operator_id' => $request->operator_id,
                ]);
            });

            return redirect()->route('spk-bantuan.index')->with('success', 'SPK Bantuan Berhasil Dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        // 1. Inisialisasi Query Awal
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang'])
            ->where('is_bantuan', true);

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

    public function operatorIndex(Request $request)
    {
        $user = Auth::user();

        // 1. Query Dasar
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang']);

        // 2. FILTER CABANG (Penting: Gunakan cabang_produksi_id agar SPK Bantuan muncul)
        if ($user->cabang->jenis !== 'pusat') {
            // Operator hanya melihat pekerjaan yang dilimpahkan ke cabangnya
            // (Baik order lokal maupun bantuan dari luar)
            $query->where('cabang_id', $user->cabang_id);
        }

        // 3. FILTER STATUS (Gunakan whereIn agar RAPI dan tidak bocor)
        $query->where('status_spk', 'acc')
            ->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])
            ->where('is_bantuan', true);

        // 4. FILTER ROLE & JENIS ORDER (Grouping Wajib)
        $query->where(function (Builder $q) use ($user) {

            // Kumpulkan jenis order yang boleh dilihat user ini
            $allowedTypes = [];

            if ($user->hasRole('operator indoor')) {
                $allowedTypes[] = 'indoor';
            }
            if ($user->hasRole('operator outdoor')) {
                $allowedTypes[] = 'outdoor';
            }
            if ($user->hasRole('operator multi')) {
                $allowedTypes[] = 'multi';
            }

            // Logika: Tampilkan jika Jenis Order sesuai Role ATAU dia ditunjuk langsung (by ID)
            $q->whereIn('jenis_order_spk', $allowedTypes)
                ->orWhere('operator_id', $user->id);
        });

        // 5. PENCARIAN
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

    public function cetakSpkBantuan($id)
    {
        // Hanya ambil data SPK dan relasinya
        $spk = MSpk::with(['bahan', 'designer', 'operator', 'cabang', 'cabangAsal'])->findOrFail($id);

        return view('spk.nota_spk.notabantuan', compact('spk'));
    }
}
