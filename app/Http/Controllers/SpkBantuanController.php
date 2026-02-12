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
            ->withCount('items')
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

    public function show($id)
    {
        // Load Relasi:
        // 1. designer (User pembuat/penerima)
        // 2. asalCabang (Cabang pengirim bantuan)
        // 3. items (List Item) -> beserta bahan & operator
        $spk = MSpk::with(['designer', 'cabangAsal', 'items.bahan', 'items.operator'])
            ->where('is_bantuan', true) // Pastikan ini SPK Bantuan
            ->findOrFail($id);

        return view('spk.designer.showBantuan', [ // Arahkan ke view khusus bantuan
            'title' => 'Detail SPK Bantuan - ' . $spk->no_spk,
            'spk' => $spk
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
        // 1. Validasi Header & Pastikan Item Ada
        $request->validate([
            'asal_cabang_id' => 'required',
            'nama_pelanggan' => 'required',
            'no_telepon'     => 'required',
            'items'          => 'required|array|min:1', // Wajib ada minimal 1 item
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();

                // A. GENERATE NOMOR SPK (Logic Lama)
                $cabangKode = $user->cabang->kode;
                $prefix = Str::after($cabangKode, '-');
                $finalPrefix = 'B' . $prefix;

                $lastSpk = MSpk::where('cabang_id', $user->cabang_id)
                    ->where('no_spk', 'like', $finalPrefix . '-%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = $lastSpk ? ((int) Str::afterLast($lastSpk->no_spk, '-') + 1) : 1;
                $newNoSpk = $finalPrefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                try {
                    $tgl = Carbon::createFromFormat('d-m-Y H:i:s', $request->tanggal);
                } catch (\Exception $e) {
                    $tgl = now();
                }

                // B. SIMPAN HEADER (M_SPK)
                // Note: Kolom detail di m_spks dikosongkan/null karena pindah ke sub
                $spk = MSpk::create([
                    'no_spk'         => $newNoSpk,
                    'is_bantuan'     => true,
                    'asal_cabang_id' => $request->asal_cabang_id,
                    'cabang_id'      => $user->cabang_id,
                    'tanggal_spk'    => $tgl,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon'     => $request->no_telepon,
                    'designer_id'    => $user->id,

                    // Kolom status global (bisa diambil dari logika item nanti)
                    'status_spk'     => 'pending',
                ]);

                // C. SIMPAN ITEMS (LOOPING) -> MASUK KE TABLE BARU
                foreach ($request->items as $item) {
                    MSubSpk::create([
                        'spk_id'       => $spk->id, // Link ke Parent ID
                        'nama_file'    => $item['file'],
                        'jenis_order'  => $item['jenis'],
                        'p'            => $item['p'],
                        'l'            => $item['l'],
                        'bahan_id'     => $item['bahan_id'],
                        'qty'          => $item['qty'],
                        'finishing'    => $item['finishing'] ?? '-',
                        'catatan'      => $item['catatan'] ?? '-',
                        'operator_id'  => $item['operator_id'], // Operator per item
                        'status_produksi' => 'pending'
                    ]);
                }

                // D. KIRIM NOTIFIKASI
                // Kirim notif bahwa ada SPK Bantuan Baru
                event(new NotifikasiSpkBaru($newNoSpk, 'Bantuan', Auth::user()->nama));
            });

            return redirect()->route('spk-bantuan.index')->with('success', 'SPK Bantuan Berhasil Dibuat dengan ' . count($request->items) . ' Item!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();

        // 1. Query ke Tabel ITEM (MSubSpk)
        $query = \App\Models\MSubSpk::with(['spk', 'spk.designer', 'spk.cabangAsal', 'bahan'])
            ->whereHas('spk', function ($q) use ($user) {
                // Filter Cabang
                if ($user->cabang->jenis !== 'pusat') {
                    $q->where('cabang_id', $user->cabang_id);
                }

                // SPK Bantuan
                $q->where('is_bantuan', true);
            });

        // 2. Filter Status Produksi "DONE"
        $query->where('status_produksi', 'done');

        // 3. LOGIKA HAK AKSES MELIHAT DATA
        // Jika user BUKAN Admin dan BUKAN Manajemen (artinya dia Operator biasa)
        // Maka filter hanya pekerjaan milik dia sendiri.
        if (!$user->hasRole(['admin', 'manajemen'])) {
            $query->where('operator_id', $user->id);
        }
        // Jika Admin/Manajemen, filter ini tidak jalan, otomatis melihat semua.

        // 4. Logika Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_file', 'like', "%$search%")
                    ->orWhereHas('spk', function ($sq) use ($search) {
                        $sq->where('no_spk', 'like', "%$search%")
                            ->orWhere('nama_pelanggan', 'like', "%$search%")
                            ->orWhereHas('cabangAsal', function ($sc) use ($search) {
                                $sc->where('nama', 'like', "%$search%");
                            });
                    });
            });
        }

        // 5. Pagination
        $items = $query->latest()->paginate(15);

        return view('spk.operator.riwayatSpk', [
            'title' => 'Riwayat Bantuan Produksi Selesai',
            'items' => $items
        ]);
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
                    ->where('is_bantuan', true); // Filter hanya SPK Bantuan
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
            'title' => 'Produksi SPK Bantuan',
            'items' => $items // Kirim variable $items, bukan $spks
        ]);
    }

    public function cetakSpkBantuan($id)
    {
        // Hanya ambil data SPK dan relasinya
        $spk = MSpk::with(['bahan', 'designer', 'operator', 'cabang', 'cabangAsal'])->findOrFail($id);

        return view('spk.nota_spk.notabantuan', compact('spk'));
    }
}
