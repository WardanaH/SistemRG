<?php

namespace App\Http\Controllers;

use App\Models\MCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MCabangController extends Controller
{
    public function index()
    {
        $cabangs = MCabang::paginate(15);
        return view('spk.manajemen.cabang', compact('cabangs'));
    }

    public function create()
    {
        return view('admin.cabangs.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'kode' => 'required|string|max:20|unique:m_cabangs',
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email',
                'telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'jenis' => 'required|in:pusat,cabang',
            ]);
            // dd($request->all());

            MCabang::create(
                [
                    'kode' => $request->input('kode'),
                    'nama' => $request->input('nama'),
                    'slug' => str_replace(' ', '-', strtolower($request->input('nama'))),
                    'email' => $request->input('email'),
                    'telepon' => $request->input('telepon'),
                    'alamat' => $request->input('alamat'),
                    'jenis' => $request->input('jenis'),
                ]
            );

            $isi = auth()->user()->username . " telah menambahkan cabang baru." . $request->input('nama');
            $this->log($isi, "Penambahan");

            return redirect()->route('manajemen.cabang')->with('success', 'Cabang berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Gagal membuat cabang: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat cabang!');
        }
    }

    public function edit(MCabang $cabang)
    {
        return view('admin.cabangs.edit', compact('cabang'));
    }

    public function update(Request $request, MCabang $cabang)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:cabangs,kode,' . $cabang->id,
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $cabang->update($validated);

        $isi = auth()->user()->username . " telah mengedit cabang " . $cabang->nama . ".";
        $this->log($isi, "Pengubahan");

        return redirect()
            ->route('manajemen.cabang')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(MCabang $cabang)
    {
        try {
            $isi = auth()->user()->username . " telah menghapus cabang " . $cabang->nama . ".";
            $this->log($isi, "Penghapusan");

            $cabang->delete();

            return redirect()->route('manajemen.cabang')->with('success', 'Cabang dihapus.');
        } catch (\Throwable $th) {
            Log::error('Gagal menghapus cabang: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus cabang!');
        }
    }
}
