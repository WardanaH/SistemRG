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
            ->where('is_bantuan', false)
            ->where('is_lembur', false);

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

    public function indexLembur(Request $request)
    {
        $user = Auth::user();

        // Eager load relasi yang dibutuhkan
        // 'cabang' = Cabang Tujuan (Tempat lembur)
        // 'cabangAsal' = Cabang Asal Designer (Penting untuk ditampilkan di tabel)
        $query = MSpk::with(['designer', 'cabang', 'cabangAsal'])
            ->withCount('items') // Menghitung jumlah item
            ->where('is_lembur', true); // Hanya ambil data lembur

        // 1. LOGIKA FILTER AKSES
        if ($user->cabang->jenis !== 'pusat') {

            if ($user->hasRole('designer')) {
                // A. DESIGNER: Melihat riwayat lembur yang DIA kerjakan (Outgoing)
                // Filter berdasarkan 'designer_id', bukan cabang.
                // Karena saat lembur, cabang_id SPK = Cabang Tujuan, sedangkan user->cabang_id = Cabang Asal.
                $query->where('designer_id', $user->id);
            }
        }

        // 2. LOGIKA PENCARIAN
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%")
                    // Optional: Cari berdasarkan nama designer
                    ->orWhereHas('designer', function ($d) use ($search) {
                        $d->where('nama', 'like', "%$search%");
                    });
            });
        }

        $spks = $query->latest()->paginate(10);

        return view('spk.designer.indexSpkLembur', [
            'title' => 'Manajemen SPK Lembur',
            'spks'  => $spks
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

        // 1. VALIDASI DATA
        $request->validate([
            // Header (Data Pelanggan)
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon'     => 'nullable|string',
            'tanggal'        => 'required',

            // Validasi Lembur
            'is_lembur'        => 'nullable|boolean',
            'cabang_lembur_id' => 'required_if:is_lembur,1|exists:m_cabangs,id',

            // Detail Items (Array)
            'items'             => 'required|array|min:1',
            'items.*.jenis'     => 'required|in:outdoor,indoor,multi',
            'items.*.file'      => 'required|string',
            'items.*.p'         => 'required|numeric|min:0',
            'items.*.l'         => 'required|numeric|min:0',
            'items.*.bahan_id'  => 'required|exists:m_bahan_bakus,id',
            'items.*.qty'       => 'required|integer|min:1',
            'items.*.operator_id' => 'required|exists:users,id', // ID Operator ini valid karena sudah difilter JS di view
            'items.*.finishing' => 'nullable|string',
            'items.*.catatan'   => 'nullable|string',
        ], [
            'items.required' => 'Mohon masukkan minimal 1 item pesanan.',
            'cabang_lembur_id.required_if' => 'Mohon pilih cabang lokasi lembur.',
        ]);

        $isLemburRoute = $request->has('is_lembur') && $request->is_lembur == '1';

        try {
            DB::transaction(function () use ($request, $user) {

                // A. TENTUKAN CABANG TARGET (Dimana SPK ini akan masuk?)
                // Jika lembur = Cabang Lembur. Jika tidak = Cabang User.
                $isLembur = $request->has('is_lembur') && $request->is_lembur == '1';
                $targetCabangId = $isLembur ? $request->cabang_lembur_id : $user->cabang_id;

                // Ambil Data Cabang Target untuk Kode SPK
                $targetCabang = MCabang::findOrFail($targetCabangId);
                $cabangKode = $targetCabang->kode; // Misal: CAB-MTP
                $basePrefix = \Illuminate\Support\Str::after($cabangKode, '-'); // Ambil 'MTP'

                // Logika Prefix:
                // Jika Lembur -> LMTP (L + Kode Cabang)
                // Jika Reguler -> MTP (Kode Cabang saja)
                if ($isLembur) {
                    $prefix = 'L' . $basePrefix; // Contoh: LMTP
                } else {
                    $prefix = $basePrefix; // Contoh: MTP
                }

                // Cari nomor terakhir berdasarkan PREFIX yang spesifik ini
                // Jadi sequence LMTP tidak akan mengganggu sequence MTP biasa
                $lastSpk = MSpk::where('cabang_id', $targetCabangId)
                    ->where('no_spk', 'like', $prefix . '-%') // Cari yang depannya LMTP- atau MTP-
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastSpk) {
                    // Ambil angka dibelakang strip terakhir
                    $lastNumber = (int) \Illuminate\Support\Str::afterLast($lastSpk->no_spk, '-');
                    $nextNumber = $lastNumber + 1;
                }

                // Gabungkan menjadi format baru
                $newNoSpk = $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Format Tanggal
                try {
                    $tgl = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal);
                } catch (\Exception $e) {
                    $tgl = now();
                }

                // 3. SIMPAN HEADER (M_SPK)
                $spk = MSpk::create([
                    'no_spk'           => $newNoSpk,
                    'tanggal_spk'      => $tgl,
                    'nama_pelanggan'   => $request->nama_pelanggan,
                    'no_telepon'       => $request->no_telepon,

                    // PENTING: Cabang ID diisi Cabang Target agar masuk ke Admin sana
                    'cabang_id'        => $targetCabangId,

                    // Info Designer & Lembur
                    'designer_id'      => $user->id,
                    'is_lembur'        => $isLembur,
                    'cabang_lembur_id' => $isLembur ? $targetCabangId : null,
                    'asal_cabang_id'   => $isLembur ? $user->cabang_id : null, // Mencatat asal designer

                    'status_spk'       => 'pending',
                    'is_bantuan'       => false,
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
                        'operator_id'     => $item['operator_id'], // Operator yang dipilih (sudah sesuai cabang target)
                        'status_produksi' => 'pending',
                    ]);
                }

                if ($isLembur == true) {
                    event(new \App\Events\NotifikasiSpkLembur($newNoSpk, 'Lembur', $user->nama));
                } else {
                    event(new \App\Events\NotifikasiSpkBaru($newNoSpk, 'Reguler', $targetCabangId, $user->nama));
                }

                // 5. KIRIM NOTIFIKASI
                // Menandai jenis notifikasi apakah Reguler atau Lembur
            });

            if ($isLemburRoute == true) {
                return redirect()->route('spk-lembur.index')->with('success', 'SPK Lembur Berhasil Dibuat!');
            } else {
                return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dibuat!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat SPK: ' . $e->getMessage())->withInput();
        }
    }

    // Halaman Edit SPK
    public function edit($id)
    {
        // 1. Ambil Data SPK beserta Items-nya
        $spk = MSpk::with(['items.operator', 'items.bahan'])->findOrFail($id);
        // dd($spk);

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

        if ($spk->is_lembur == true) {
            return view('spk.nota_spk.notaLembur', compact('spk'));
        }

        return view('spk.nota_spk.notaspk', compact('spk'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_spk' => 'required|in:pending,acc,rejected',
        ]);

        $spk = MSpk::findOrFail($id);
        $spk->status_spk = $request->status_spk;
        $spk->admin_id = auth()->id();

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

        // 1. Query ke Tabel ITEM (MSubSpk)
        $query = MSubSpk::with(['spk', 'spk.designer', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang (Hanya di cabang user login)
                if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }

                // Filter Header: Harus ACC dan BUKAN bantuan (untuk reguler index)
                $q->where('status_spk', 'acc')
                    ->where('is_bantuan', false)
                    ->where('is_lembur', false);
            });

        // 2. PERBAIKAN: FILTER SPESIFIK PER USER
        // Kita tidak lagi memfilter berdasarkan "Role", tapi langsung berdasarkan "operator_id"
        // agar Operator A tidak melihat kerjaan Operator B meski role-nya sama.
        $query->where('operator_id', $user->id);

        // 3. Filter Status Produksi (Hanya yang masih aktif)
        $query->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing']);

        // 4. Pencarian
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

        $items = $query->oldest()->paginate(15);

        return view('spk.operator.indexSpk', [
            'title' => 'Produksi SPK',
            'items' => $items
        ]);
    }

    public function operatorIndexLembur(Request $request)
    {
        $user = Auth::user();

        // 1. Mulai Query dari Item (MSubSpk)
        $query = MSubSpk::with(['spk.designer', 'spk.cabang', 'spk.cabangAsal', 'bahan'])
            ->whereHas('spk', function ($q) {

                // --- HAPUS FILTER CABANG INI ---
                // Masalahnya: User Cabang 6, SPK Cabang 5.
                // Jika difilter pakai user->cabang_id (6), SPK Cabang 5 tidak akan ketemu.
                // Karena ini LEMBUR, operator boleh mengerjakan lintas cabang.
                /* if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }
                */

                // Filter Khusus Lembur & Status
                // Gunakan whereIn status 'pending' & 'acc' supaya data muncul
                // walaupun admin belum klik tombol ACC (untuk testing).
                $q->whereIn('status_spk', ['acc'])
                    ->where('is_lembur', true);
            });

        // 2. FILTER UTAMA: Spesifik Item milik Operator yang Login
        // Ini kuncinya. Budi (ID 41) hanya akan melihat item dimana dia ditugaskan.
        $query->where('operator_id', $user->id);

        // 3. Filter Status Produksi Aktif
        $query->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing']);

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

        $items = $query->oldest()->paginate(15);

        // PENTING: Gunakan View Operator (Tabel Item), BUKAN View Designer (Tabel Header)
        return view('spk.operator.indexSpk', [
            'title' => 'Produksi Lembur',
            'items' => $items
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
        $query = MSubSpk::with(['spk', 'spk.designer', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang (Hanya riwayat pekerjaan di cabang sendiri)
                if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }

                // SPK Reguler
                $q->where('is_bantuan', false)
                    ->where('is_lembur', false);
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

    public function riwayatLembur(Request $request)
    {
        $user = Auth::user();

        // 1. Query Item (MSubSpk)
        $query = MSubSpk::with(['spk', 'spk.designer', 'spk.cabang', 'spk.cabangAsal', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang (Hanya riwayat pekerjaan di cabang sendiri)

                $q->where('is_bantuan', false)
                    ->where('is_lembur', true);
            });

        // 2. Filter Utama: Milik Operator yg Login & Status DONE
        $query->where('status_produksi', 'done');
            // dd($query);

        if (!$user->hasRole(['admin', 'manajemen'])) {
            $query->where('operator_id', $user->id);
        }

        // 3. Pencarian
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

        // 4. Urutkan dari yang paling baru selesai (updated_at)
        $items = $query->orderBy('updated_at', 'desc')->paginate(15);

        return view('spk.operator.riwayatSpk', [
            'title' => 'Riwayat Pekerjaan Selesai',
            'items' => $items
        ]);
    }
}
