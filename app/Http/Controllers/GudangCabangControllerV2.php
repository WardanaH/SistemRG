<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaan;
use App\Models\MCabangBarang;
use App\Models\MGudangBarang; // <-- Sudah diganti ka MGudangBarang
use Carbon\Carbon;

class GudangCabangControllerV2 extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $cabangId = Auth::user()->cabang_id;

        // Jumlah pengajuan permintaan hari ini oleh cabang ini
        $pengajuanHariIni = PermintaanBarang::where('gudang_cabang_id', $cabangId)
            ->whereDate('created_at', $today)
            ->count();

        // Jumlah permintaan cabang ini nang balum di-ACC pusat
        $belumDiproses = PermintaanBarang::where('gudang_cabang_id', $cabangId)
            ->where('status_permintaan', 'menunggu_acc')
            ->count();

        // Jumlah barang diterima hari ini oleh cabang ini
        $diterimaHariIni = PermintaanBarang::where('gudang_cabang_id', $cabangId)
            ->where('status_permintaan', 'diterima')
            ->whereDate('updated_at', $today)
            ->count();

        return view('distribusi.gudang_cabang.dashboard', compact(
            'pengajuanHariIni',
            'belumDiproses',
            'diterimaHariIni'
        ));
    }

    public function permintaan()
    {
        // Ambil semua data barang dari Master Barang Gudang
        $bahanBakus = MGudangBarang::all(); // <-- Sudah diganti modelnya
        return view('distribusi.gudang_cabang.permintaan.index', compact('bahanBakus'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan dari form
        $request->validate([
            'keterangan' => 'required|string',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:gudang_barangs,id', // <-- Validasi diubah ka tabel gudang_barangs
            'sisa_stok_cabang' => 'required|array',
            'jumlah_diminta' => 'required|array',
            'keterangan' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 2. Olah Kode Permintaan Otomatis (Contoh: REQ-20260901-001)
            $hariIni = now()->format('Ymd');
            $jumlahHariIni = PermintaanBarang::whereDate('created_at', now()->today())->count() + 1;
            $kodePermintaan = 'REQ-' . $hariIni . '-' . str_pad($jumlahHariIni, 3, '0', STR_PAD_LEFT);

            // 3. Simpan Header ke tabel permintaan_barang
            $permintaan = PermintaanBarang::create([
                'kode_permintaan' => $kodePermintaan,
                'gudang_cabang_id' => Auth::user()->cabang_id, // Pastikan field ini sesuai lawan relasi user nyawa
                'tanggal_request' => now(),
                'status_permintaan' => 'menunggu_acc',
                'keterangan' => $request->keterangan
            ]);

            // 4. Looping untuk menyimpan Detail ke tabel detail_permintaan
            $detailData = [];
            foreach ($request->barang_id as $index => $b_id) {
                $detailData[] = [
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $b_id,
                    'sisa_stok_cabang' => $request->sisa_stok_cabang[$index],
                    'jumlah_diminta' => $request->jumlah_diminta[$index],
                    'jumlah_disetujui' => 0, // Default 0, kena admin utama nang maubah
                    'keterangan_barang' => $request->keterangan_barang[$index] ?? null, // Ambil keterangan barang jika ada
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Pakai insert() supaya eksekusi query-nya cuma sekali, kada bulak-balik ngehit database
            DetailPermintaan::insert($detailData);

            // Amun lancar barataan, baru commit simpan ke database
            DB::commit();

            // Ganti NotifikasiCabangV2 jadi NotifikasiPusatV2
            event(new \App\Events\NotifikasiPusatV2(
                $permintaan->id,
                'Permintaan barang baru dari cabang: ' . Auth::user()->cabang->nama
            ));

            return redirect()->route('gudang-cabang.permintaan')
                ->with('success', 'Permintaan barang berhasil dikirim ke Gudang Utama.');
        } catch (\Exception $e) {
            // Amun ada error, batalkan semua simpanan (rollback)
            DB::rollBack();
            return back()->with('error', 'Gagal membuat permintaan: ' . $e->getMessage());
        }
    }

    public function riwayat_permintaan(Request $request)
    {
        // buat query untuk filter
        $query = PermintaanBarang::with(['detailPermintaan.barangPusat'])
            ->where('gudang_cabang_id', Auth::user()->cabang_id);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_permintaan', $request->status);
        }

        // Filter Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Pencarian Teks (Kode Permintaan)
        if ($request->filled('search')) {
            $query->where('kode_permintaan', 'like', '%' . $request->search . '%');
        }

        $riwayat = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('distribusi.gudang_cabang.permintaan.riwayat', compact('riwayat'));
    }

    public function penerimaan()
    {
        // Tampilkan cuma nang statusnya 'dikirim' gasan cabang ini
        $permintaan = PermintaanBarang::with(['detailPermintaan.barangPusat'])
            ->where('gudang_cabang_id', Auth::user()->cabang_id)
            ->where('status_permintaan', 'dikirim')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('distribusi.gudang_cabang.permintaan.penerimaan', compact('permintaan'));
    }

    public function terimaBarang(Request $request, $id)
    {
        // Validasi inputan form dari modal
        $request->validate([
            'detail_id' => 'required|array',
            'jumlah_diterima' => 'required|array',
            'jumlah_diterima.*' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $permintaan = PermintaanBarang::with('detailPermintaan')->findOrFail($id);

            if ($permintaan->status_permintaan !== 'dikirim') {
                return back()->with('error', 'Barang ngini kada dalam status dikirim.');
            }

            $isLengkap = true; // Penanda awal gasan ngecek kelengkapan

            // 1. Looping data nang diinput di modal
            foreach ($request->detail_id as $index => $detailId) {
                $detail = DetailPermintaan::find($detailId);
                $jmlDiterimaFisik = $request->jumlah_diterima[$index];

                // Cek amun fisik nang ditampi kurang dari nang dikirim (di-ACC)
                if ($jmlDiterimaFisik < $detail->jumlah_disetujui) {
                    $isLengkap = false;
                }

                // Catat jumlah nang bujuran sampai ka database
                $detail->update([
                    'jumlah_diterima' => $jmlDiterimaFisik
                ]);

                // Tambah stok ka cabang sasuai fisik nang diinput
                if ($jmlDiterimaFisik > 0) {
                    $stokCabang = MCabangBarang::firstOrCreate(
                        [
                            'cabang_id' => $permintaan->gudang_cabang_id,
                            'gudang_barang_id' => $detail->barang_id
                        ],
                        ['stok' => 0]
                    );

                    $stokCabang->increment('stok', $jmlDiterimaFisik);
                }
            }

            // 2. Tentukan status akhir lalu update header
            $statusAkhir = $isLengkap ? 'diterima' : 'tidak_lengkap';

            $permintaan->update([
                'status_permintaan' => $statusAkhir
            ]);

            DB::commit();
            return back()->with('success', 'Barang diproses lawan status: ' . strtoupper($statusAkhir));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal manerima barang: ' . $e->getMessage());
        }
    }
}
