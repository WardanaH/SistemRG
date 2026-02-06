<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MSpk;
use App\Models\User;
use App\Models\MCabang;
use App\Models\MSubSpk;
use App\Models\MBahanBaku;
use App\Models\MFinishing;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\NotifikasiSpkBaru;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

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

        // PERBAIKAN: Hapus 'bahan' & 'operator' karena sudah pindah ke tabel items
        // Tambahkan 'withCount' untuk menghitung jumlah item di tabel depan
        $query = MSpk::with(['designer', 'cabang'])
            ->withCount('items') // Menghitung jumlah item di sub_spk
            ->where('is_bantuan', false);

        // 1. Logika Filter Cabang
        if ($user->cabang->jenis !== 'pusat') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // 2. Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%");
            });
        }

        $spks = $query->latest()->paginate(10);

        return view('spk.designer.indexSpk', [ // Sesuaikan folder view Anda
            'title' => 'Manajemen SPK',
            'spks' => $spks
        ]);
    }

    public function show($id)
    {
        // Ambil SPK Header beserta Relasi Detail Itemnya
        // Kita perlu load: items, items->bahan, items->operator
        $spk = MSpk::with(['designer', 'cabang', 'items.bahan', 'items.operator'])
            ->findOrFail($id);

        return view('spk.designer.show', [
            'title' => 'Detail SPK - ' . $spk->no_spk,
            'spk' => $spk
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI DATA (Mendukung Array Items)
        $request->validate([
            // Header (Data Pelanggan)
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon'     => 'nullable|string', // Boleh kosong buat SPK Reguler
            'tanggal'        => 'required',

            // Detail Items (Array)
            'items'                 => 'required|array|min:1',
            'items.*.jenis'         => 'required|in:outdoor,indoor,multi',
            'items.*.file'          => 'required|string',
            'items.*.p'             => 'required|numeric|min:0',
            'items.*.l'             => 'required|numeric|min:0',
            'items.*.bahan_id'      => 'required|exists:m_bahan_bakus,id',
            'items.*.qty'           => 'required|integer|min:1',
            'items.*.operator_id'   => 'required|exists:users,id',
            'items.*.finishing'     => 'nullable|string',
            'items.*.catatan'       => 'nullable|string',
        ], [
            'items.required' => 'Mohon masukkan minimal 1 item pesanan.',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {

                // 2. GENERATE NOMOR SPK (Format: KODE-000001)
                $cabangKode = $user->cabang->kode;
                $prefix = Str::after($cabangKode, '-'); // Contoh: BJM

                // Cari nomor terakhir untuk cabang ini (lock biar gak ganda)
                $lastSpk = MSpk::where('cabang_id', $user->cabang_id)
                    ->where('no_spk', 'like', $prefix . '-%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastSpk) {
                    $lastNumber = (int) Str::afterLast($lastSpk->no_spk, '-');
                    $nextNumber = $lastNumber + 1;
                }

                $newNoSpk = $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Format Tanggal
                try {
                    $tgl = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tgl = now()->format('Y-m-d');
                }

                // 3. SIMPAN HEADER (M_SPK)
                $spk = MSpk::create([
                    'no_spk'         => $newNoSpk,
                    'tanggal_spk'    => $tgl,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon'     => $request->no_telepon,
                    'cabang_id'      => $user->cabang_id,
                    'designer_id'    => $user->id, // User yang login adalah Designer/Admin
                    'is_bantuan'     => false,     // Ini SPK Reguler
                    'asal_cabang_id' => null,
                    'status_spk'     => 'pending',
                ]);

                // 4. SIMPAN ITEMS (M_SUB_SPK)
                foreach ($request->items as $item) {
                    MSubSpk::create([
                        'spk_id'          => $spk->id,
                        'nama_file'       => $item['file'],
                        'jenis_order'     => $item['jenis'],
                        'p'               => $item['p'],
                        'l'               => $item['l'],
                        'bahan_id'        => $item['bahan_id'],
                        'qty'             => $item['qty'],
                        'finishing'       => $item['finishing'] ?? '-',
                        'catatan'         => $item['catatan'] ?? '-',
                        'operator_id'     => $item['operator_id'], // Operator per item
                        'status_produksi' => 'pending',
                    ]);
                }

                // 5. KIRIM NOTIFIKASI
                event(new \App\Events\NotifikasiSpkBaru($newNoSpk, 'Reguler', $user->nama));
            });

            return redirect()->route('spk.index')
                ->with('success', 'SPK Reguler Berhasil Dibuat dengan ' . count($request->items) . ' Item!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat SPK: ' . $e->getMessage())->withInput();
        }
    }

    // Halaman Edit SPK
    public function edit($id)
    {
        // 1. Ambil Data SPK beserta Items-nya
        $spk = MSpk::with(['items'])->findOrFail($id);

        // 2. Validasi Akses Cabang
        if (Auth::user()->cabang->jenis !== 'pusat' && $spk->cabang_id !== Auth::user()->cabang_id) {
            abort(403, 'Akses ditolak');
        }

        // 3. Data Pendukung untuk Dropdown
        $bahans = MBahanBaku::all();
        $finishings = MFinishing::all();

        $cabangId = $spk->cabang_id;
        $designers = User::role('designer')->where('cabang_id', $cabangId)->get();
        // Ambil semua operator di cabang ini
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

    public function update(Request $request, $id)
    {
        $spk = MSpk::findOrFail($id);

        // 1. Validasi Header & Items
        $request->validate([
            // Header
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon'     => 'nullable|string',
            'items'          => 'required|array|min:1',

            // Detail Items (Validasi Array)
            'items.*.jenis'       => 'required|in:outdoor,indoor,multi',
            'items.*.file'        => 'required|string',
            'items.*.p'           => 'required|numeric|min:0',
            'items.*.l'           => 'required|numeric|min:0',
            'items.*.bahan_id'    => 'required|exists:m_bahan_bakus,id',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.operator_id' => 'required|exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($request, $spk) {
                // 2. Update Header
                $spk->update([
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon'     => $request->no_telepon,
                    // designer_id jarang berubah, tapi kalau mau diupdate silakan tambahkan
                ]);

                // 3. Hapus Semua Item Lama (Reset)
                // Cara paling aman & mudah untuk update one-to-many adalah hapus dulu, lalu buat baru
                // Kecuali Anda butuh tracking ID item yang persis sama
                $spk->items()->delete();

                // 4. Buat Ulang Item Baru
                foreach ($request->items as $item) {
                    MSubSpk::create([
                        'spk_id'          => $spk->id,
                        'nama_file'       => $item['file'],
                        'jenis_order'     => $item['jenis'],
                        'p'               => $item['p'],
                        'l'               => $item['l'],
                        'bahan_id'        => $item['bahan_id'],
                        'qty'             => $item['qty'],
                        'finishing'       => $item['finishing'] ?? '-',
                        'catatan'         => $item['catatan'] ?? '-',
                        'operator_id'     => $item['operator_id'],
                        'status_produksi' => 'pending', // Reset status jika diedit total
                    ]);
                }
            });

            $route = $spk->is_bantuan ? 'spk-bantuan.index' : 'spk.index';
            return redirect()->route($route)->with('success', 'Data SPK berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(MSpk $spk)
    {
        $spk->delete();
        return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dihapus!');
    }

    public function cetakSpk($id)
    {
        $spk = MSpk::with(['designer', 'items.bahan', 'items.operator'])
            ->findOrFail($id);

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

        // 1. Query ke Tabel ITEM (MSubSpk), bukan Header SPK
        // Eager Load relasi ke parent SPK, Bahan, dan Designer
        $query = MSubSpk::with(['spk', 'spk.designer', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang (Hanya ambil item dari SPK yang masuk ke cabang ini)
                if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }

                // Filter Status SPK Header Harus ACC
                $q->where('status_spk', 'acc')
                    ->where('is_bantuan', false); // Filter hanya SPK Bantuan
            });

        // 2. FILTER ROLE OPERATOR (Tampilkan item sesuai keahlian operator)
        $allowedTypes = [];
        if ($user->hasRole('operator indoor'))  $allowedTypes[] = 'indoor';
        if ($user->hasRole('operator outdoor')) $allowedTypes[] = 'outdoor';
        // 'multi' jarang dipakai di level item, biasanya item itu tegas indoor/outdoor
        // tapi kalau ada, masukkan saja
        if ($user->hasRole('operator multi'))   $allowedTypes[] = 'multi';

        $query->where(function ($q) use ($allowedTypes, $user) {
            // Tampilkan item jika jenisnya sesuai role operator
            $q->whereIn('jenis_order', $allowedTypes)
                // ATAU jika operator ini ditunjuk langsung secara spesifik untuk item tersebut
                ->orWhere('operator_id', $user->id);
        });

        // 3. FILTER STATUS PRODUKSI (Hanya yang aktif)
        // Sembunyikan yang sudah 'done' agar list tidak penuh (opsional, bisa dibuat tab history nanti)
        $query->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing']);

        // 4. PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%$search%")
                    ->orWhereHas('spk', function ($sq) use ($search) {
                        $sq->where('no_spk', 'like', "%$search%")
                            ->orWhere('nama_pelanggan', 'like', "%$search%");
                    });
            });
        }

        // Urutkan berdasarkan deadline / tanggal SPK terlama dulu (FIFO)
        $items = $query->oldest()->paginate(15);

        return view('spk.operator.indexSpk', [
            'title' => 'Antrian Operator',
            'items' => $items // Kirim variable $items, bukan $spks
        ]);
    }

    // Method untuk Operator Menyelesaikan Pekerjaan
    public function updateStatusProduksi(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'status_produksi' => 'required|in:pending,ripping,ongoing,finishing,done',
            'catatan_operator' => 'nullable|string',
        ]);

        // 2. Ambil Data Item (MSubSpk), bukan MSpk Header
        $item = \App\Models\MSubSpk::with('spk')->findOrFail($id);

        // 3. Validasi: SPK Induk harus sudah ACC
        if ($item->spk->status_spk != 'acc') {
            return back()->with('error', 'Gagal! SPK Induk belum di-ACC oleh manajemen.');
        }

        // 4. Update Status Item
        $item->update([
            'status_produksi' => $request->status_produksi,
            'catatan_operator' => $request->catatan_operator,

            // Otomatis set Operator yang sedang login saat status berubah dari 'pending'
            // Ini agar ketahuan siapa yang mengerjakan item ini
            'operator_id' => ($request->status_produksi != 'pending') ? auth()->id() : $item->operator_id
        ]);

        // 5. Logika Otomatis Update Status SPK Induk (Header)
        // Cek apakah SEMUA item dalam SPK ini sudah 'done'?
        $parentSpk = $item->spk;

        $totalItems = $parentSpk->items()->count();
        $doneItems = $parentSpk->items()->where('status_produksi', 'done')->count();
        $ongoingItems = $parentSpk->items()->where('status_produksi', '!=', 'pending')->count();

        if ($totalItems > 0 && $totalItems == $doneItems) {
            // Jika semua item sudah 'done', maka Header SPK juga 'done'
            $parentSpk->update(['status_produksi' => 'done']);
        } elseif ($ongoingItems > 0) {
            // Jika minimal ada 1 item yang sedang dikerjakan, Header jadi 'ongoing'
            $parentSpk->update(['status_produksi' => 'ongoing']);
        }

        return back()->with('success', 'Status Item Berhasil Diperbarui!');
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        // 1. Query ke Tabel ITEM (MSubSpk)
        $query = \App\Models\MSubSpk::with(['spk', 'spk.designer', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang (Hanya riwayat pekerjaan di cabang sendiri)
                if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }

                // SPK Reguler
                $q->where('is_bantuan', false);
            });

        // 2. Filter Status Produksi "DONE" (Selesai)
        $query->where('status_produksi', 'done');

        // 3. LOGIKA HAK AKSES MELIHAT DATA
        // Jika user BUKAN Admin dan BUKAN Manajemen (artinya dia Operator biasa)
        // Maka filter hanya pekerjaan milik dia sendiri.
        if (!$user->hasRole(['admin', 'manajemen'])) {
            $query->where('operator_id', $user->id);
        }
        // Jika Admin/Manajemen, kode di atas dilewati, jadi bisa lihat semua operator.

        // 4. Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%$search%")
                    ->orWhereHas('spk', function ($sq) use ($search) {
                        $sq->where('no_spk', 'like', "%$search%")
                            ->orWhere('nama_pelanggan', 'like', "%$search%");
                    });
            });
        }

        // 5. Eksekusi Data
        $items = $query->latest()->paginate(15);

        return view('spk.operator.riwayatSpk', [
            'title' => 'Riwayat Produksi Selesai',
            'items' => $items
        ]);
    }
}
