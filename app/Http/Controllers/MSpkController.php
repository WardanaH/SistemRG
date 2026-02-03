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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use App\Events\NotifikasiSpkBaru;

class MSpkController extends Controller
{
    public function buat() // Atau public function create()
    {
        $user = Auth::user();

        // Cek apakah user berada di Cabang Pusat
        $isPusat = $user->cabang->jenis === 'pusat';

        // 1. Ambil Bahan Baku (Disarankan filter stok > 0)
        $bahans = MBahanBaku::all();

        // 2. Ambil Finishing
        $finishings = MFinishing::all();

        // 3. Ambil User Designer (Filter cabang jika bukan pusat)
        $designers = User::role('designer')
            ->when(!$isPusat, function ($query) use ($user) {
                return $query->where('cabang_id', $user->cabang_id);
            })
            ->get();

        // 4. Ambil User Operator (Filter cabang jika bukan pusat)
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi'])
            ->when(!$isPusat, function ($query) use ($user) {
                return $query->where('cabang_id', $user->cabang_id);
            })
            ->get();

        // 5. [BARU] Ambil Data Cabang Lain (Untuk Opsi SPK Bantuan)
        // Ambil semua cabang KECUALI cabang user saat ini
        $cabangLain = MCabang::where('id', '!=', $user->cabang_id)->get();

        return view('spk.designer.spk', [
            'user'       => $user,
            'title'      => 'Buat SPK',
            'bahans'     => $bahans,
            'finishings' => $finishings,
            'designers'  => $designers,
            'operators'  => $operators,
            'cabangLain' => $cabangLain, // Kirim variabel ini ke View
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Mulai Query dengan Eager Loading agar hemat query database
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang'])
            ->where('is_bantuan', false);

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
        $user = Auth::user();

        // 1. RULE VALIDASI DASAR
        $rules = [
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
        ];

        // 2. VALIDASI TAMBAHAN JIKA MODE BANTUAN AKTIF
        if ($request->has('is_bantuan')) {
            // Jika Bantuan: Asal Cabang WAJIB, No Telp WAJIB (karena order luar)
            $rules['asal_cabang_id'] = 'required|exists:m_cabangs,id';
            $rules['no_telp']        = 'required|numeric|digits_between:10,13';
        } else {
            // Jika Biasa: No Telp Boleh Kosong (Nanti diisi admin) atau string "Di Isi Oleh Admin"
            $rules['no_telp']        = 'nullable';
        }

        $request->validate($rules, [
            'asal_cabang_id.required' => 'Harap pilih asal cabang pengirim order.',
            'no_telp.required'        => 'Untuk SPK Bantuan, nomor telepon pelanggan wajib diisi.',
        ]);

        // dd($request->all());

        // 3. MULAI TRANSAKSI DATABASE
        try {
            DB::transaction(function () use ($request, $user) {

                // Ambil Kode Cabang User Login (Misal: CBG-BJM)
                $cabangKode = $user->cabang->kode;
                $basePrefix = Str::after($cabangKode, '-'); // Hasil: BJM

                // Tentukan Prefix Akhir & Asal Cabang
                if ($request->has('is_bantuan')) {
                    $finalPrefix = 'B' . $basePrefix; // Hasil: BBJM
                    $asalCabangId = $request->asal_cabang_id;
                    $noTelp = $request->no_telp;
                } else {
                    $finalPrefix = $basePrefix; // Hasil: BJM
                    $asalCabangId = null;
                    // Jika inputnya text default, kita simpan null biar bersih di DB
                    $noTelp = ($request->no_telp == 'Di Isi Oleh Admin') ? null : $request->no_telp;
                }

                // --- GENERATE NOMOR URUT (LOCKING) ---
                // Cari nomor terakhir berdasarkan Cabang Pembuat & Pola Prefix
                $lastSpk = MSpk::where('cabang_id', $user->cabang_id)
                    ->where('no_spk', 'like', $finalPrefix . '-%') // Filter BJM- atau BBJM-
                    ->lockForUpdate() // Kunci baris agar tidak tabrakan
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastSpk) {
                    // Ambil angka di belakang strip terakhir
                    $lastNumber = (int) Str::afterLast($lastSpk->no_spk, '-');
                    $nextNumber = $lastNumber + 1;
                }

                // Format: PREFIX-000001 (Contoh: BJM-000001 atau BBJM-000001)
                $newNoSpk = $finalPrefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // --- FORMAT TANGGAL ---
                try {
                    $formattedDate = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
                } catch (\Exception $e) {
                    $formattedDate = now()->format('Y-m-d');
                }

                // --- SIMPAN DATA ---
                MSpk::create([
                    'no_spk'             => $newNoSpk,
                    'tanggal_spk'        => $formattedDate,
                    'jenis_order_spk'    => $request->jenis_order,
                    'nama_pelanggan'     => $request->nama_pelanggan,
                    'no_telepon'         => $noTelp,
                    'nama_file'          => $request->nama_file,
                    'ukuran_panjang'     => $request->ukuran_p,
                    'ukuran_lebar'       => $request->ukuran_l,
                    'bahan_id'           => $request->bahan_id,
                    'kuantitas'          => $request->qty,
                    'finishing'          => $request->finishing,
                    'keterangan'         => $request->catatan,
                    'designer_id'        => $request->designer_id,
                    'operator_id'        => $request->operator_id,
                    'cabang_id'          => $user->cabang_id, // Cabang tempat pembuatan (Produksi)

                    // Kolom Baru untuk Bantuan
                    'is_bantuan'         => $request->has('is_bantuan'),
                    'asal_cabang_id'     => $asalCabangId,
                ]);

                event(new NotifikasiSpkBaru($newNoSpk, 'Reguler', Auth::user()->nama));

                return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dibuat!');
            });

            return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dibuat!');
        } catch (\Exception $e) {
            Log::error('Gagal membuat SPK: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat SPK: ' . $e->getMessage())->withInput();
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

        // Validasi Update (Nomor SPK & Tipe Bantuan tidak boleh diubah untuk menjaga integritas)
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_telp'        => 'nullable|numeric|digits_between:10,13',
            'nama_file'      => 'required|string',
            'ukuran_p'       => 'required|numeric|min:0',
            'ukuran_l'       => 'required|numeric|min:0',
            'bahan_id'       => 'required|exists:m_bahan_bakus,id',
            'qty'            => 'required|integer|min:1',
            'designer_id'    => 'required|exists:users,id',
            'operator_id'    => 'required|exists:users,id',
        ]);

        $spk->update([
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_telepon'      => $request->no_telp, // Di sini Admin bisa update no telp
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

            // Asal Cabang bisa diupdate jika perlu, tapi is_bantuan & no_spk sebaiknya jangan
            'asal_cabang_id'  => $request->has('is_bantuan') ? $request->asal_cabang_id : null,
        ]);

        if ($spk->is_bantuan == '1') {
            return redirect()->route('spk-bantuan.index')->with('success', 'Data SPK bantuan berhasil diperbarui!');
        } else {
            return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui!');
        }
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
            ->where('is_bantuan', false);

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
        $query = MSpk::with(['bahan', 'designer', 'operator', 'cabang'])
            ->where('is_bantuan', false);

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
