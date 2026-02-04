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

        $bahans = $query->get(); // atau paginate(10)
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

    public function destroy(MBahanBaku $bahanbaku)
    {
        $bahanbaku->delete();
        return redirect()->route('manajemen.bahanbaku')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
