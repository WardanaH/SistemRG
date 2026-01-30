<?php

namespace App\Http\Controllers;

use App\Models\MFinishing;
use Illuminate\Http\Request;

class MFinishingController extends Controller
{
    public function index()
    {
        $finishings = MFinishing::latest()->get();
        return view('spk.manajemen.finishing', [
            'title' => 'Data Finishing',
            'finishings' => $finishings
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        MFinishing::create([
            'nama_finishing' => $request->nama,
        ]);

        return back()->with('success', 'Finishing berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $finishing = MFinishing::findOrFail($id);
        $finishing->update([
            'nama_finishing' => $request->nama,
        ]);

        return back()->with('success', 'Data finishing diperbarui!');
    }

    public function destroy($id)
    {
        MFinishing::findOrFail($id)->delete();
        return back()->with('success', 'Finishing dihapus!');
    }
}
