<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\MCabang;
use App\Models\User;
use App\Models\MBahanBaku;
use App\Models\MFinishing;
use App\Events\NotifikasiOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdvertisingController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // 1. Ambil Statistik untuk Card Dashboard
        $stats = [
            // Total Keseluruhan
            'total_semua' => MSpk::where('designer_id', $userId)
                                 ->where('is_advertising', true)->count(),

            // Hitung yang biasa (Bukan bantuan)
            'total_biasa' => MSpk::where('designer_id', $userId)
                                 ->where('is_advertising', true)
                                 ->where('is_bantuan', false)->count(),

            // Hitung yang bantuan
            'total_bantuan' => MSpk::where('designer_id', $userId)
                                   ->where('is_advertising', true)
                                   ->where('is_bantuan', true)->count(),

            // Item yang sedang dikerjakan operator
            'item_proses' => MSubSpk::whereHas('spk', function($q) use ($userId) {
                $q->where('designer_id', $userId)->where('is_advertising', true);
            })->whereIn('status_produksi', ['pending', 'ripping', 'ongoing', 'finishing'])->count(),

            // Item yang sudah selesai
            'item_selesai' => MSubSpk::whereHas('spk', function($q) use ($userId) {
                $q->where('designer_id', $userId)->where('is_advertising', true);
            })->where('status_produksi', 'completed')->count(),
        ];

        // 2. Data Tabel SPK
        $query = MSpk::with(['items', 'items.operator'])
            ->withCount('items')
            ->where('designer_id', $userId)
            ->where('is_advertising', true) // Pastikan hanya data advertising
            ->latest();

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_spk', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_pelanggan', 'like', '%' . $request->search . '%');
            });
        }

        $spks = $query->paginate(10);

        // Return ke View Dashboard
        return view('spk.advertising.index', [
            'title' => 'Dashboard Advertising',
            'spks' => $spks,
            'stats' => $stats
        ]);
    }

    public function create()
    {
        // Ambil data pendukung untuk form
        // Filter operator hanya yang ada di cabang PUSAT (karena Adv di pusat)
        $cabangPusat = MCabang::where('jenis', 'pusat')->first();

        $operators = User::role(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])
            ->get();

        $bahans = MBahanBaku::all();
        $finishings = MFinishing::all();

        return view('spk.advertising.create', [
            'title' => 'Buat SPK Advertising',
            'operators' => $operators,
            'bahans' => $bahans,
            'finishings' => $finishings
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.file' => 'required',
            'items.*.operator_id' => 'required|exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $cabang = $user->cabang;

                $baseprefix = Str::after($cabang->kode, '-');

                // 1. Generate No SPK (Format: ADV-MTP-00001)
                $prefix = 'ADV-' . $baseprefix; // Hasil: ADV-PST
                // Ambil nomor urut terakhir
                $lastSpk = MSpk::where('no_spk', 'like', $prefix . '-%')->latest('id')->first();
                $nextNum = $lastSpk ? ((int)Str::afterLast($lastSpk->no_spk, '-') + 1) : 1;
                $newNoSpk = $prefix . '-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

                // --- TANGKAP NILAI CHECKBOX BANTUAN ---
                $isBantuan = $request->has('is_bantuan') ? true : false;

                // 2. Simpan Header
                $spk = MSpk::create([
                    'no_spk'         => $newNoSpk,
                    'tanggal_spk'    => now(),
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'no_telepon'     => $request->no_telepon,
                    'cabang_id'      => $cabang->id,
                    'designer_id'    => $user->id,
                    'admin_id'       => $user->id,
                    'status_spk'     => 'acc',
                    'is_lembur'      => false,
                    'is_bantuan'     => $isBantuan, // <--- UBAH DI SINI
                    'is_advertising' => true,
                    'folder'         => $request->folder,
                ]);

                // 3. Simpan Items & Kirim Notif
                foreach ($request->items as $item) {
                    $sub = MSubSpk::create([
                        'spk_id'          => $spk->id,
                        'nama_file'       => $item['file'],
                        'jenis_order'     => $item['jenis'],
                        'p'               => $item['p'] ?? 0,
                        'l'               => $item['l'] ?? 0,
                        'qty'             => $item['qty'],
                        'bahan_id'        => $item['bahan_id'],
                        'operator_id'     => $item['operator_id'],
                        'finishing'       => $item['finishing'] ?? '-',
                        'catatan'         => $item['catatan'] ?? '-',
                        'status_produksi' => 'pending'
                    ]);

                    // Trigger Pusher ke Operator (tetap dikirim sebagai advertising)
                    event(new NotifikasiOperator($newNoSpk, "advertising", $sub->nama_file, $sub->operator_id));
                }
            });

            return redirect()->route('advertising.dashboard')->with('success', 'SPK Advertising Berhasil Dikirim ke Operator!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $spk = MSpk::with(['items.operator', 'items.bahan'])->where('designer_id', Auth::id())->findOrFail($id);
        return view('spk.advertising.show', ['title' => 'Detail SPK', 'spk' => $spk]);
    }

    public function destroy($id)
    {
        $spk = MSpk::where('designer_id', Auth::id())->findOrFail($id);
        $spk->items()->delete();
        $spk->delete();
        return back()->with('success', 'SPK Berhasil Dihapus');
    }

    public function print($id)
    {
        $spk = MSpk::with(['items', 'designer'])->findOrFail($id);
        return view('spk.advertising.nota', compact('spk'));
    }

    public function riwayatProduksi()
    {
        // Melihat status item yang dikerjakan operator
        $items = MSubSpk::whereHas('spk', function ($q) {
            $q->where('designer_id', Auth::id());
        })
            ->with(['spk', 'operator'])
            ->where('status_produksi', '=', 'done')
            ->latest('updated_at')
            ->paginate(15);

        return view('spk.advertising.riwayat', [
            'title' => 'Riwayat Produksi',
            'items' => $items
        ]);
    }

    public function riwayatOperator()
    {
        $userId = Auth::id();

        $items = MSubSpk::where('operator_id', $userId)
            // Filter status 'completed' (sesuai logic tombol centang hijau sebelumnya)
            // Jika di database kamu pakai 'done', ganti jadi 'done'
            ->where('status_produksi', 'done')

            // Pastikan ini tugas dari Advertising
            ->whereHas('spk', function($q) {
                $q->where('is_advertising', true);
            })
            ->with(['spk', 'bahan'])
            ->latest('updated_at') // Urutkan dari yang terakhir selesai
            ->paginate(15);

        return view('spk.advertising.riwayatOperator', [
            'title' => 'Riwayat Produksi Saya',
            'items' => $items
        ]);
    }

    public function produksiIndex()
    {
        $userId = Auth::id();

        $items = MSubSpk::where('operator_id', $userId)
            ->whereHas('spk', function($q) {
                $q->where('is_advertising', true);
            })
            ->with('spk', 'bahan')
            ->where('status_produksi', '!=', 'done')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('spk.advertising.produksi', [
            'title' => 'Antrean Produksi Advertising',
            'items' => $items
        ]);
    }

    // UPDATE STATUS OLEH OPERATOR
    public function updateStatusProduksi(Request $request, $id)
    {
        try {
            $request->validate([
                'status_produksi' => 'required|in:pending,ripping,ongoing,finishing,done'
            ]);

            $subSpk = MSubSpk::where('operator_id', Auth::id())->findOrFail($id);
            $subSpk->status_produksi = $request->status_produksi;
            $subSpk->catatan_operator = $request->catatan;
            $subSpk->save();

            // Opsional: Jika Completed, bisa kirim notif balik ke Designer Advertising
            // if($request->status_produksi == 'completed') { ... }

            return back()->with('success', 'Status produksi berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error($e);
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}
