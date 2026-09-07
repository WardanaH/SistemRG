<?php

namespace App\Http\Controllers\profil2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfilHero;
use App\Models\ProfilMesin;
use App\Models\ProfilPerusahaan;
use App\Models\ProfilProduk;
use App\Models\ProfilEvent;
use App\Models\ProfilKlien;
use App\Models\ProfilLegal;

class BerandaController extends Controller
{
    // Fungsi manampaiakan halaman Beranda (Public)
    public function index()
    {
        $hero = ProfilHero::where('halaman', 'beranda')->first();
        $perusahaan = ProfilPerusahaan::first();
        $mesins = ProfilMesin::all();
        $kliens = ProfilKlien::all();

        $produkIndoor = ProfilProduk::whereIn('kategori_layanan', ['indoor', 'outdoor'])->where('is_tampil_beranda', 1)->get();
        $produkDtf = ProfilProduk::where('kategori_layanan', 'dtf')->where('is_tampil_beranda', 1)->get();
        $produkMerch = ProfilProduk::where('kategori_layanan', 'multi')->where('is_tampil_beranda', 1)->get();

        return view('profil2.dashboard', [ // Pastikan arah view-nya bujur (profil2.index atau profil2.dashboard)
            'title' => 'Beranda',
            'hero' => $hero,
            'perusahaan' => $perusahaan,
            'produkIndoor' => $produkIndoor,
            'produkDtf' => $produkDtf,
            'produkMerch' => $produkMerch,
            'mesins' => $mesins,
            'kliens' => $kliens
        ]);
    }

    // Fungsi manampaiakan halaman Layanan Kami
    public function layanan()
    {
        $hero = ProfilHero::where('halaman', 'layanan')->first();
        $mesins = ProfilMesin::all();

        $produkIndoor = ProfilProduk::whereIn('kategori_layanan', ['indoor', 'outdoor'])->get();
        $produkDtf = ProfilProduk::where('kategori_layanan', 'dtf')->get();
        $produkMerch = ProfilProduk::where('kategori_layanan', 'multi')->get();

        return view('profil2.layanan', [
            'title' => 'Layanan Kami',
            'hero' => $hero,
            'produkIndoor' => $produkIndoor,
            'produkDtf' => $produkDtf,
            'produkMerch' => $produkMerch,
            'mesins' => $mesins
        ]);
    }

    // Halaman Katalog Produk
    public function produk(Request $request)
    {
        $hero = ProfilHero::where('halaman', 'produk')->first();
        $mesins = ProfilMesin::all();

        $kategoris = ProfilProduk::select('kategori_produk')->distinct()->get();

        $query = ProfilProduk::query();
        if ($request->has('kategori')) {
            $query->where('kategori_produk', $request->kategori);
        }
        $produks = $query->latest()->get();

        return view('profil2.produk', [
            'title' => 'Katalog Produk',
            'hero' => $hero,
            'kategoris' => $kategoris,
            'produks' => $produks,
            'mesins' => $mesins
        ]);
    }

    // Halaman Detail Produk
    public function detailProduk($id)
    {
        $produk = ProfilProduk::findOrFail($id);
        $perusahaan = ProfilPerusahaan::first();
        $mesins = ProfilMesin::all();

        return view('profil2.detail_produk', [
            'title' => 'Detail ' . $produk->nama_produk,
            'produk' => $produk,
            'perusahaan' => $perusahaan,
            'mesins' => $mesins
        ]);
    }

    // Fungsi manampaiakan halaman Event Spesial
    public function event($tema)
    {
        $events = ProfilEvent::where('tema', $tema)->get();
        $perusahaan = ProfilPerusahaan::first();
        $mesins = ProfilMesin::all();

        return view('profil2.event', [
            'title' => 'Event ' . ucfirst($tema),
            'tema' => $tema,
            'events' => $events,
            'perusahaan' => $perusahaan,
            'mesins' => $mesins
        ]);
    }

    // Fungsi manampaiakan halaman Tentang Kami
    public function tentang()
    {
        $hero = ProfilHero::where('halaman', 'tentang')->first();
        $perusahaan = ProfilPerusahaan::first();
        $mesins = ProfilMesin::all();

        return view('profil2.tentang', [
            'title' => 'Tentang Kami',
            'hero' => $hero,
            'perusahaan' => $perusahaan,
            'mesins' => $mesins
        ]);
    }

    // Fungsi manampaiakan halaman Hubungi Kami
    public function kontak()
    {
        $hero = ProfilHero::where('halaman', 'kontak')->first();
        $perusahaan = ProfilPerusahaan::first();
        $mesins = ProfilMesin::all();

        return view('profil2.kontak', [
            'title' => 'Hubungi Kami',
            'hero' => $hero,
            'perusahaan' => $perusahaan,
            'mesins' => $mesins
        ]);
    }

    // Fungsi manampaiakan halaman Syarat Ketentuan / Kebijakan Privasi
    public function legal($jenis)
    {
        if (!in_array($jenis, ['syarat_ketentuan', 'kebijakan_privasi'])) {
            abort(404);
        }

        $legal = ProfilLegal::where('jenis', $jenis)->first();
        $judul = $jenis == 'syarat_ketentuan' ? 'SYARAT & KETENTUAN' : 'KEBIJAKAN PRIVASI';
        $mesins = ProfilMesin::all();

        return view('profil2.legal', [
            'title' => $judul,
            'legal' => $legal,
            'judul' => $judul,
            'mesins' => $mesins
        ]);
    }
}
