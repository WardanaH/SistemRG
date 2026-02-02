<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use App\Models\MBahanBaku;
use App\Models\MCabang;
use App\Models\MFinishing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SpkBantuanController extends Controller
{
    // MENAMPILKAN DAFTAR SPK BANTUAN SAJA
    public function index()
    {
        $user = Auth::user();

        $spks = MSpk::with(['asalCabang', 'designer', 'operator', 'bahan'])
            ->where('cabang_id', $user->cabang_id) // Data di cabang ini
            ->where('is_bantuan', true) // HANYA YANG BANTUAN
            ->latest()
            ->paginate(10);

        return view('spk.desginer.indexSpkBantuan', [
            'title' => 'Manajemen SPK Bantuan (Eksternal)',
            'spks' => $spks
        ]);
    }

    // FORM INPUT KHUSUS SPK BANTUAN
    public function create()
    {
        $user = Auth::user();

        // Ambil Cabang Lain (Pengirim)
        $cabangLain = MCabang::where('id', '!=', $user->cabang_id)->get();
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

            return redirect()->route('spk-bantuan')->with('success', 'SPK Bantuan Berhasil Dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
