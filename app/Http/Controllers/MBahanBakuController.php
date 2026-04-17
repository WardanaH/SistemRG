<?php

namespace App\Http\Controllers;

use App\Models\MBahanBaku;
use Illuminate\Http\Request;

class MBahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $query = MBahanBaku::query();

        // Jika ada pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_bahan', 'LIKE', "%{$search}%")
                ->orWhere('kode_bahan', 'LIKE', "%{$search}%");
        }

        $bahans = $query->paginate(10); // atau paginate(10)
        $title = 'Manajemen Bahan Baku';

        return view('spk.manajemen.bahanbaku', compact('bahans', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
        ]);

        MBahanBaku::create([
            'kode_bahan' => $request->input('kode'),
            'nama_bahan' => $request->input('nama'),
        ]);

        return redirect()->route('manajemen.bahanbaku')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    // --- TAMBAHAN: FUNGSI EDIT ---
    public function edit($id)
    {
        $bahan = MBahanBaku::findOrFail($id);
        $title = 'Edit Bahan Baku';

        // Kita buat view baru khusus edit agar rapi
        return view('spk.manajemen.edit_bahanbaku', compact('bahan', 'title'));
    }

    // --- TAMBAHAN: FUNGSI UPDATE ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|unique:m_bahan_bakus,kode_bahan,'.$id, // Ignore unique untuk id ini
            'nama' => 'required',
        ]);

        $bahan = MBahanBaku::findOrFail($id);

        $bahan->update([
            'kode_bahan' => $request->input('kode'),
            'nama_bahan' => $request->input('nama'),
        ]);

        return redirect()->route('manajemen.bahanbaku')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(MBahanBaku $bahanbaku)
    {
        $bahanbaku->delete();
        return redirect()->route('manajemen.bahanbaku')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
