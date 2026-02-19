<?php

namespace App\Http\Controllers;

use App\Events\NotifikasiOperator;
use App\Models\MBahanBaku;
use App\Models\MCabang;
use App\Models\MFinishing;
use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])
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
        $query = MSpk::with(['designer', 'cabang', 'items'])
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

    public function indexCharge(Request $request)
    {
        $user = Auth::user();

        $query = MSpk::with(['designer', 'cabang', 'items'])
            ->withCount('items')
            // Filter: Hanya ambil SPK yang memiliki item berjenis 'charge'
            ->whereHas('items', function ($q) {
                $q->where('jenis_order', 'charge');
            });

        // 1. Logika Filter Cabang (Non-Pusat hanya lihat cabang sendiri)
        if ($user->cabang->jenis !== 'pusat') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // 2. Logika Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spk', 'like', "%$search%")
                    ->orWhere('nama_pelanggan', 'like', "%$search%");
            });
        }

        $spks = $query->latest()->paginate(10);

        return view('spk.designer.indexSpkCharge', [ // Pastikan nama file view sesuai
            'title' => 'Manajemen SPK Charge Desain',
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
        // dd($request->all());

        // Ambil semua data items
        $items = $request->items;

        // Bersihkan data secara manual untuk jenis 'charge'
        foreach ($items as $key => $item) {
            if ($item['jenis'] === 'charge') {
                // Paksa menjadi null agar lolos validasi 'exists' dan 'nullable'
                $items[$key]['bahan_id'] = null;
                $items[$key]['operator_id'] = null;
                $items[$key]['p'] = 0; // Atau null jika di DB sudah nullable
                $items[$key]['l'] = 0; // Atau null jika di DB sudah nullable
            }
        }

        // Masukkan kembali data yang sudah dibersihkan ke dalam request
        $request->merge(['items' => $items]);
        // dd($request->all());

        // 1. VALIDASI DATA
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon'     => 'nullable|string',
            'tanggal'        => 'required',
            'is_lembur'      => 'nullable|boolean',
            'cabang_lembur_id' => 'required_if:is_lembur,1|exists:m_cabangs,id',

            // Detail Items
            'items'              => 'required|array|min:1',
            'items.*.jenis'      => 'required|in:outdoor,indoor,multi,dtf,charge',
            'items.*.file'       => 'required|string',
            'items.*.qty'        => 'required|integer|min:1',

            // Validasi Kondisional: Wajib diisi KECUALI jenisnya 'charge'
            'items.*.p'           => 'required_unless:items.*.jenis,charge|numeric|min:0',
            'items.*.l'           => 'required_unless:items.*.jenis,charge|numeric|min:0',
            'items.*.bahan_id'    => 'required_unless:items.*.jenis,charge|nullable|exists:m_bahan_bakus,id',
            'items.*.operator_id' => 'required_unless:items.*.jenis,charge|nullable|exists:users,id',

            'items.*.finishing'   => 'nullable|string',
            'items.*.catatan'     => 'nullable|string',
        ]);
        // dd($request->all());

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
                // Tentukan status default SPK
                $statusSpk = 'pending';

                // LOGIKA CERDAS (Opsional):
                // Jika SPK ini isinya HANYA 1 item dan itu adalah 'Charge Desain',
                // maka SPK langsung dianggap selesai ('done' atau 'acc').
                if (count($request->items) == 1 && $request->items[0]['jenis'] === 'charge') {
                    $statusSpk = 'done'; // (Ganti 'acc' jika di sistem Anda tidak ada status 'done' untuk header SPK)
                }

                $spk = MSpk::create([
                    'no_spk'           => $newNoSpk,
                    'tanggal_spk'      => $tgl,
                    'nama_pelanggan'   => $request->nama_pelanggan,
                    'no_telepon'       => $request->no_telepon,
                    'cabang_id'        => $targetCabangId,
                    'designer_id'      => $user->id,
                    'is_lembur'        => $isLembur,
                    'cabang_lembur_id' => $isLembur ? $targetCabangId : null,
                    'asal_cabang_id'   => $isLembur ? $user->cabang_id : null,
                    'is_bantuan'       => false,

                    // Masukkan variabel status yang sudah dikalkulasi di atas
                    'status_spk'       => $statusSpk,
                ]);

                // 3. SIMPAN HEADER (M_SPK)
                // $spk = MSpk::create([
                //     'no_spk'           => $newNoSpk,
                //     'tanggal_spk'      => $tgl,
                //     'nama_pelanggan'   => $request->nama_pelanggan,
                //     'no_telepon'       => $request->no_telepon,

                //     // PENTING: Cabang ID diisi Cabang Target agar masuk ke Admin sana
                //     'cabang_id'        => $targetCabangId,

                //     // Info Designer & Lembur
                //     'designer_id'      => $user->id,
                //     'is_lembur'        => $isLembur,
                //     'cabang_lembur_id' => $isLembur ? $targetCabangId : null,
                //     'asal_cabang_id'   => $isLembur ? $user->cabang_id : null, // Mencatat asal designer

                //     'status_spk'       => 'pending',
                //     'is_bantuan'       => false,
                // ]);

                // 4. SIMPAN ITEMS (M_SUB_SPK)
                foreach ($request->items as $item) {
                    $isCharge = $item['jenis'] === 'charge';

                    MSubSpk::create([
                        'spk_id'          => $spk->id,
                        'nama_file'       => $item['file'],
                        'jenis_order'     => $item['jenis'],
                        'p'               => $isCharge ? null : $item['p'],
                        'l'               => $isCharge ? null : $item['l'],
                        'bahan_id'        => $isCharge ? null : $item['bahan_id'],
                        'operator_id'     => $isCharge ? null : $item['operator_id'],
                        'finishing'       => $isCharge ? null : $item['finishing'],
                        'qty'             => $item['qty'],
                        'catatan'         => $item['catatan'] ?? '-',
                        'status_produksi' => $isCharge ? 'done' : 'pending',
                    ]);
                }

                if ($isLembur == true) {
                    event(new \App\Events\NotifikasiSpkLembur($newNoSpk, 'Lembur', $user->nama));
                } else {
                    event(new \App\Events\NotifikasiSpkBaru($newNoSpk, 'Reguler', $targetCabangId, $user->nama));
                }
            });

            if ($isLemburRoute == true) {
                return redirect()->route('spk-lembur.index')->with('success', 'SPK Lembur Berhasil Dibuat!');
            } else {
                return redirect()->route('spk.index')->with('success', 'SPK Berhasil Dibuat!');
            }
        } catch (\Exception $e) {
            Log::error('Gagal membuat SPK: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat SPK: ' . $e->getMessage())->withInput();
        }
    }

    // Halaman Edit SPK
    public function edit($id)
    {
        // 1. Ambil Data SPK beserta Items-nya
        $spk = MSpk::with(['items.operator', 'items.bahan'])->findOrFail($id);

        // 2. Validasi Akses Cabang
        $user = Auth::user();
        if ($user->cabang->jenis !== 'pusat') {
            if ($spk->is_lembur) {
                // Jika SPK Lembur: Izinkan jika user adalah PEMBUATNYA (designer) atau dari cabang tujuan
                if ($spk->designer_id !== $user->id && $spk->cabang_id !== $user->cabang_id) {
                    abort(403, 'Akses ditolak. Anda tidak berhak mengedit SPK Lembur ini.');
                }
            } else {
                if ($spk->cabang_id !== $user->cabang_id) {
                    abort(403, 'Akses ditolak');
                }
            }
        }

        // 3. Data Pendukung untuk Dropdown
        $bahans = MBahanBaku::all();
        $finishings = MFinishing::all();

        $cabangId = $spk->cabang_id;
        $designers = User::role('designer')->where('cabang_id', $cabangId)->get();

        // --- PERBAIKAN LOGIKA OPERATOR ---
        $rolesOperator = ['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'];

        if ($spk->is_lembur) {
            // JIKA LEMBUR: Ambil SEMUA operator dari seluruh cabang
            // (with 'cabang' agar kita bisa tampilkan nama cabangnya di dropdown)
            $operators = User::with('cabang')->role($rolesOperator)->get();
        } else {
            // JIKA REGULER: Ambil operator khusus di cabang SPK tersebut saja
            $operators = User::role($rolesOperator)->where('cabang_id', $cabangId)->get();
        }

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

        // 1. Bersihkan data secara manual untuk jenis 'charge' (SAMA SEPERTI STORE)
        $items = $request->items;
        foreach ($items as $key => $item) {
            if (isset($item['jenis']) && $item['jenis'] === 'charge') {
                $items[$key]['bahan_id'] = null;
                $items[$key]['operator_id'] = null;
                $items[$key]['p'] = 0;
                $items[$key]['l'] = 0;
            }
        }
        // Masukkan kembali data yang sudah dibersihkan ke dalam request
        $request->merge(['items' => $items]);

        // 2. Validasi Header & Items (SAMA SEPERTI STORE)
        $request->validate([
            // Header
            'nama_pelanggan' => 'required|string|max:255',
            'no_telepon'     => 'nullable|string',
            'items'          => 'required|array|min:1',

            // Detail Items (Validasi Kondisional)
            'items.*.jenis'       => 'required|in:outdoor,indoor,multi,dtf,charge', // dtf & charge ditambahkan
            'items.*.file'        => 'required|string',
            'items.*.qty'         => 'required|integer|min:1',

            'items.*.p'           => 'required_unless:items.*.jenis,charge|numeric|min:0',
            'items.*.l'           => 'required_unless:items.*.jenis,charge|numeric|min:0',
            'items.*.bahan_id'    => 'required_unless:items.*.jenis,charge|nullable|exists:m_bahan_bakus,id',
            'items.*.operator_id' => 'required_unless:items.*.jenis,charge|nullable|exists:users,id',

            'items.*.finishing'   => 'nullable|string',
            'items.*.catatan'     => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $spk) {
                // 3. Update Header
                $spk->update([
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon'     => $request->no_telepon,
                ]);

                // 4. Hapus Semua Item Lama (Reset)
                $spk->items()->delete();

                // 5. Buat Ulang Item Baru (SAMA SEPERTI STORE)
                foreach ($request->items as $item) {
                    $isCharge = $item['jenis'] === 'charge';

                    MSubSpk::create([
                        'spk_id'          => $spk->id,
                        'nama_file'       => $item['file'],
                        'jenis_order'     => $item['jenis'],
                        'p'               => $isCharge ? null : $item['p'],
                        'l'               => $isCharge ? null : $item['l'],
                        'bahan_id'        => $isCharge ? null : $item['bahan_id'],
                        'operator_id'     => $isCharge ? null : $item['operator_id'],
                        'qty'             => $item['qty'],
                        'finishing'       => $isCharge ? null : ($item['finishing'] ?? '-'),
                        'catatan'         => $item['catatan'] ?? '-',
                        'status_produksi' => 'pending', // Reset status jika diedit total
                    ]);
                }
            });

            // Tentukan arah redirect berdasarkan tipe SPK
            if ($spk->is_advertising) {
                return redirect()->route('advertising.index')->with('success', 'Data SPK Advertising berhasil diperbarui!');
            } elseif ($spk->is_bantuan) {
                return redirect()->route('spk-bantuan.index')->with('success', 'Data SPK Bantuan berhasil diperbarui!');
            } elseif ($spk->is_lembur) {
                return redirect()->route('spk-lembur.index')->with('success', 'Data SPK Lembur berhasil diperbarui!');
            } else {
                return redirect()->route('spk.index')->with('success', 'Data SPK berhasil diperbarui!');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal update SPK: ' . $e->getMessage());
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

    public function printCharge($id)
    {
        $spk = MSpk::with(['designer', 'cabang', 'items'])->findOrFail($id);

        // Pastikan ini adalah SPK Charge
        if (!$spk->items()->where('jenis_order', 'charge')->exists()) {
            return back()->with('error', 'Ini bukan SPK Charge.');
        }

        return view('spk.nota.notaCharge', compact('spk'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status_spk' => 'required|in:pending,acc,rejected',
            ]);

            $spk = MSpk::with('items')->findOrFail($id);

            $spk->status_spk = $request->status_spk;
            $spk->admin_id = auth()->id();
            $spk->save();

            // Kirim notifikasi jika ACC
            if ($request->status_spk === 'acc') {

                // 1. TENTUKAN TIPE SPK SECARA MANUAL BERDASARKAN BOOLEAN
                $tipe = 'reguler'; // Default
                if ($spk->is_advertising == true) {
                    $tipe = 'advertising';
                } elseif ($spk->is_bantuan == true) {
                    $tipe = 'bantuan';
                } elseif ($spk->is_lembur == true) {
                    $tipe = 'lembur';
                }

                foreach ($spk->items as $item) {
                    // Tidak mengirim notif untuk charge desain
                    if ($item->operator_id) {
                        event(new NotifikasiOperator(
                            $spk->no_spk,
                            $tipe, // <-- Gunakan variabel tipe yang sudah dicek di atas
                            $item->nama_file,
                            $item->operator_id
                        ));
                    }
                }
            }

            return redirect()->back()->with('success', 'Status SPK berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error("Gagal Update Status SPK: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
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
