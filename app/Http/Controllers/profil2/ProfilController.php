<?php

namespace App\Http\Controllers\profil2;

use App\Http\Controllers\Controller;
use App\Models\ProfilEvent;
use App\Models\ProfilKlien;
use App\Models\ProfilMesin;
use App\Models\ProfilPerusahaan;
use App\Models\ProfilProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfilController extends Controller
{
    // Hanya manampaiakan halaman Dashboard (Salam Panyambut)
    public function dashboard()
    {
        $adminName = Auth::user()->username ?? 'Admin';

        return view('profil2.admin.dashboard', [
            'title' => 'Dashboard Admin Profil',
            'adminName' => $adminName
        ]);
    }

    // Nampaiakan form Info Perusahaan
    public function editPerusahaan()
    {
        // Ambil data parusahaan, amun balum ada di-ulahakan baris kosong otomatis (ID = 1)
        $perusahaan = ProfilPerusahaan::firstOrCreate(['id' => 1]);

        return view('profil2.admin.perusahaan', [
            'title' => 'Atur Info Perusahaan',
            'perusahaan' => $perusahaan
        ]);
    }

    // Manyimpan isian Info Perusahaan
    public function updatePerusahaan(Request $request)
    {
        $perusahaan = ProfilPerusahaan::findOrFail(1);

        // Simpan sabarataan isian matan form
        $perusahaan->update($request->all());

        return back()->with('success', 'Mantap! Data Profil Perusahaan ba-hasil disimpan.');
    }

    // Nampaiakan form Atur Teks Hero
    public function editHero()
    {
        // Daftar halaman nang baisi Hero
        $halaman_list = ['beranda', 'layanan', 'produk', 'tentang', 'kontak'];

        // Pengecekan, amun datanya balum ada di database, kita ulahakan otomatis
        foreach ($halaman_list as $hal) {
            \App\Models\ProfilHero::firstOrCreate(
                ['halaman' => $hal],
                [
                    'judul_utama' => 'JUDUL ' . strtoupper($hal),
                    'sub_judul' => 'Sub judul gasan halaman ' . $hal
                ]
            );
        }

        // Ambil sabarataan data hero
        $heroes = \App\Models\ProfilHero::all();

        return view('profil2.admin.hero', [
            'title' => 'Atur Teks Hero',
            'heroes' => $heroes
        ]);
    }

    // Manyimpan isian Teks & Gambar Hero
    public function updateHero(Request $request)
    {
        // dd($request);
        try {

            if ($request->has('heroes')) {
                foreach ($request->heroes as $id => $data) {
                    $hero = \App\Models\ProfilHero::find($id);

                    // Cek amun ada upload gambar hanyar gasan hero ngini
                    if ($request->hasFile("heroes.{$id}.gambar_background")) {
                        // Hapus gambar nang lawas amun ada
                        if ($hero->gambar_background && file_exists(public_path($hero->gambar_background))) {
                            unlink(public_path($hero->gambar_background));
                        }

                        $file = $request->file("heroes.{$id}.gambar_background");
                        $nama_file = time() . "_" . $file->getClientOriginalName();
                        $file->move(public_path('uploads/profil_hero'), $nama_file);

                        // Masukakan path ka dalam array data nang handak di-update
                        $data['gambar_background'] = 'uploads/profil_hero/' . $nama_file;
                    }

                    $hero->update($data);
                }
            }

            return back()->with('success', 'Sip! Teks wan Gambar Hero ba-hasil di-update.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    // 1. Manampaiakan Daftar Produk
    public function indexProduk()
    {
        $produks = ProfilProduk::latest()->get();
        return view('profil2.admin.produk', [
            'title' => 'Kelola Produk',
            'produks' => $produks
        ]);
    }

    // 2. Manyimpan Produk Hanyar
    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'kategori_layanan' => 'required|in:indoor,outdoor,multi,dtf',
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['is_tampil_beranda'] = $request->has('is_tampil_beranda') ? 1 : 0;

        // Amun ada upload gambar
        if ($request->hasFile('gambar_produk')) {
            $file = $request->file('gambar_produk');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_produk'), $nama_file);
            $data['gambar_produk'] = 'uploads/profil_produk/' . $nama_file;
        }

        ProfilProduk::create($data);
        return back()->with('success', 'Produk hanyar ba-hasil ditambahakan!');
    }

    // 3. Ma-Update Produk
    public function updateProduk(Request $request, $id)
    {
        $produk = ProfilProduk::findOrFail($id);
        $data = $request->all();
        $data['is_tampil_beranda'] = $request->has('is_tampil_beranda') ? 1 : 0;

        // Amun admin ma-upload gambar hanyar
        if ($request->hasFile('gambar_produk')) {
            // Hapus gambar lawas amun ada
            if ($produk->gambar_produk && file_exists(public_path($produk->gambar_produk))) {
                unlink(public_path($produk->gambar_produk));
            }

            $file = $request->file('gambar_produk');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_produk'), $nama_file);
            $data['gambar_produk'] = 'uploads/profil_produk/' . $nama_file;
        }

        $produk->update($data);
        return back()->with('success', 'Data produk ba-hasil di-update!');
    }

    // 4. Mahapus Produk
    public function destroyProduk($id)
    {
        $produk = ProfilProduk::findOrFail($id);

        // Hapus file gambar
        if ($produk->gambar_produk && file_exists(public_path($produk->gambar_produk))) {
            unlink(public_path($produk->gambar_produk));
        }

        $produk->delete();
        return back()->with('success', 'Produk ba-hasil dihapus bujur-bujur!');
    }

    // Nampaiakan form Legal & Privasi
    public function editLegal()
    {
        // Ambil atawa ulah data bawaan Syarat & Ketentuan
        $syarat = \App\Models\ProfilLegal::firstOrCreate(
            ['jenis' => 'syarat_ketentuan'],
            ['isi_konten' => "1. Pambayaran DP minimal 50% sabalum pangarjaan dimulai.\n2. Desain nang sudah di-ACC palanggan kada kawa diubah amun sudah masuk mesin cetak.\n3. Palanggan batanggung jawab panuh atas hak cipta (copyright) matan desain atawa gambar nang disarahakan gasan dicetak.\n4. Barang nang kada diambil labih matan 30 hari di luar tanggung jawab kami."]
        );

        // Ambil atawa ulah data bawaan Kebijakan Privasi (Pakai UU PDP)
        $privasi = \App\Models\ProfilLegal::firstOrCreate(
            ['jenis' => 'kebijakan_privasi'],
            ['isi_konten' => "Sasuai lawan amanat Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP), CV Restu Guru Promosindo bakomitmen manjaga karahasiaan data palanggan kami.\n\n1. Data nangkaya Ngaran, No. WhatsApp, wan Alamat cuma dipakai gasan kaperluan transaksi, pancetakan nota, wan pangiriman barang.\n2. File desain nang di-upload atawa dikirim palanggan adalah hak milik palanggan. Kami kada pacang manyabarluasakan atawa manjual file ngitu ka pihak katiga.\n3. Kami manjamin kaamanan data palanggan matan panyalahgunaan pihak internal parusahaan."]
        );

        return view('profil2.admin.legal', [
            'title' => 'Legal & Privasi',
            'syarat' => $syarat,
            'privasi' => $privasi
        ]);
    }

    // Manyimpan isian Legal & Privasi
    public function updateLegal(Request $request)
    {
        $request->validate([
            'syarat_ketentuan' => 'required',
            'kebijakan_privasi' => 'required',
        ]);

        \App\Models\ProfilLegal::where('jenis', 'syarat_ketentuan')->update(['isi_konten' => $request->syarat_ketentuan]);
        \App\Models\ProfilLegal::where('jenis', 'kebijakan_privasi')->update(['isi_konten' => $request->kebijakan_privasi]);

        return back()->with('success', 'Sip! Aturan Legal & Privasi ba-hasil di-update.');
    }

    // 1. Manampaiakan Daftar Event
    public function indexEvent()
    {
        $events = ProfilEvent::latest()->get();
        return view('profil2.admin.event', [
            'title' => 'Kelola Event Spesial',
            'events' => $events
        ]);
    }

    // 2. Manyimpan Event Hanyar
    public function storeEvent(Request $request)
    {
        $request->validate([
            'tema' => 'required',
            'nama_produk' => 'required',
            'badge_kategori' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // Amun ada upload gambar
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_event'), $nama_file);
            $data['gambar'] = 'uploads/profil_event/' . $nama_file;
        }

        ProfilEvent::create($data);
        return back()->with('success', 'Desain Event hanyar ba-hasil ditambahakan!');
    }

    // 3. Ma-Update Event
    public function updateEvent(Request $request, $id)
    {
        $event = ProfilEvent::findOrFail($id);
        $data = $request->all();

        // Amun admin ma-upload gambar hanyar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lawas amun ada
            if ($event->gambar && file_exists(public_path($event->gambar))) {
                unlink(public_path($event->gambar));
            }

            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_event'), $nama_file);
            $data['gambar'] = 'uploads/profil_event/' . $nama_file;
        }

        $event->update($data);
        return back()->with('success', 'Data Event ba-hasil di-update!');
    }

    // 4. Mahapus Event
    public function destroyEvent($id)
    {
        $event = ProfilEvent::findOrFail($id);

        // Hapus file gambar
        if ($event->gambar && file_exists(public_path($event->gambar))) {
            unlink(public_path($event->gambar));
        }

        $event->delete();
        return back()->with('success', 'Desain Event ba-hasil dihapus bujur-bujur!');
    }

    // 1. Manampaiakan Daftar Mesin
    public function indexMesin()
    {
        $mesins = ProfilMesin::latest()->get();
        return view('profil2.admin.mesin', [
            'title' => 'Kelola Mesin Cetak',
            'mesins' => $mesins
        ]);
    }

    // 2. Manyimpan Mesin
    public function storeMesin(Request $request)
    {
        $request->validate(['nama_mesin' => 'required', 'warna_tema' => 'required']);
        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_mesin'), $nama_file);
            $data['gambar'] = 'uploads/profil_mesin/' . $nama_file;
        }

        ProfilMesin::create($data);
        return back()->with('success', 'Mesin hanyar ba-hasil ditambahakan!');
    }

    // 3. Update Mesin
    public function updateMesin(Request $request, $id)
    {
        $mesin = ProfilMesin::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($mesin->gambar && file_exists(public_path($mesin->gambar))) {
                unlink(public_path($mesin->gambar));
            }
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_mesin'), $nama_file);
            $data['gambar'] = 'uploads/profil_mesin/' . $nama_file;
        }

        $mesin->update($data);
        return back()->with('success', 'Data mesin ba-hasil di-update!');
    }

    // 4. Hapus Mesin
    public function destroyMesin($id)
    {
        $mesin = ProfilMesin::findOrFail($id);
        if ($mesin->gambar && file_exists(public_path($mesin->gambar))) {
            unlink(public_path($mesin->gambar));
        }
        $mesin->delete();
        return back()->with('success', 'Data mesin ba-hasil dihapus!');
    }

    // 1. Manampaiakan Daftar Klien
    public function indexKlien()
    {
        $kliens = ProfilKlien::latest()->get();
        return view('profil2.admin.klien', [
            'title' => 'Kelola Klien',
            'kliens' => $kliens
        ]);
    }

    // 2. Manyimpan Klien
    public function storeKlien(Request $request)
    {
        $request->validate(['nama_klien' => 'required']);
        $data = $request->all();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_klien'), $nama_file);
            $data['logo'] = 'uploads/profil_klien/' . $nama_file;
        }

        ProfilKlien::create($data);
        return back()->with('success', 'Klien hanyar ba-hasil ditambahakan!');
    }

    // 3. Update Klien
    public function updateKlien(Request $request, $id)
    {
        $klien = ProfilKlien::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('logo')) {
            if ($klien->logo && file_exists(public_path($klien->logo))) {
                unlink(public_path($klien->logo));
            }
            $file = $request->file('logo');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil_klien'), $nama_file);
            $data['logo'] = 'uploads/profil_klien/' . $nama_file;
        }

        $klien->update($data);
        return back()->with('success', 'Data Klien ba-hasil di-update!');
    }

    // 4. Hapus Klien
    public function destroyKlien($id)
    {
        $klien = ProfilKlien::findOrFail($id);
        if ($klien->logo && file_exists(public_path($klien->logo))) {
            unlink(public_path($klien->logo));
        }
        $klien->delete();
        return back()->with('success', 'Data Klien ba-hasil dihapus!');
    }
}
