<?php

namespace App\Http\Controllers;

use App\Models\MSpk;
use App\Models\User;
use App\Models\MBahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'title' => 'Daftar SPK',
            'spks' => $spks
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'no_spk'          => 'required|unique:m_spks,no_spk',
            'tanggal'         => 'required', // Nanti kita format ulang
            'jenis_order'     => 'required|in:outdoor,indoor,multi',
            'nama_pelanggan'  => 'required|string',
            'no_telp'         => 'required|string',
            'nama_file'       => 'required|string',
            'ukuran_p'        => 'required|numeric',
            'ukuran_l'        => 'required|numeric',
            'bahan_id'        => 'required|exists:m_bahan_bakus,id', // Ubah name="bahan" jadi bahan_id di view
            'qty'             => 'required|integer|min:1',
            'finishing'       => 'nullable|string',
            'catatan'         => 'nullable|string',
            'designer_id'     => 'required|exists:users,id', // Ubah name="designer" jadi designer_id
            'operator_id'     => 'required|exists:users,id', // Ubah name="operator" jadi operator_id
        ]);

        // dd($request->all());

        // 2. Format Ulang Tanggal (Dari d-m-Y view ke Y-m-d database)
        $date = \DateTime::createFromFormat('d-m-Y', $request->tanggal);
        $formattedDate = $date ? $date->format('Y-m-d') : date('Y-m-d');

        // 3. Simpan Data
        MSpk::create([
            'no_spk'          => $request->no_spk,
            'tanggal_spk'     => $formattedDate,
            'jenis_order_spk' => $request->jenis_order,
            'nama_pelanggan'  => $request->nama_pelanggan,
            'no_telepon'      => $request->no_telp,
            'nama_file'       => $request->nama_file,
            'ukuran_panjang'  => $request->ukuran_p,
            'ukuran_lebar'    => $request->ukuran_l,
            'bahan_id'        => $request->bahan_id,
            'kuantitas'       => $request->qty,
            'finishing'       => $request->finishing,
            'keterangan'      => $request->catatan,
            'designer_id'     => $request->designer_id,
            'operator_id'     => $request->operator_id,
            'cabang_id'       => Auth::user()->cabang_id, // Otomatis dari user login
        ]);

        return redirect()->route('designer.spk')->with('success', 'SPK Berhasil Dibuat!');
    }

    public function destroy(MSpk $spk)
    {
        $spk->delete();
        return redirect()->route('designer.spk.index')->with('success', 'SPK Berhasil Dihapus!');
    }
}
