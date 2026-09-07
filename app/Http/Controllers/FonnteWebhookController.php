<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\MCabang;
use Carbon\Carbon;

class FonnteWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $pengirim = $data['sender'] ?? null;
        $pesan    = trim(strtolower($data['message'] ?? ''));

        if (!$pengirim) return response("NO SENDER", 200);

        // 1. CEK & BUAT SESI
        $session = DB::table('wa_sessions')->where('no_hp', $pengirim)->first();
        if (!$session) {
            DB::table('wa_sessions')->insert([
                'no_hp' => $pengirim,
                'state' => 'idle',
                'order_data' => json_encode([]),
                'last_request_at' => now()
            ]);
            $session = DB::table('wa_sessions')->where('no_hp', $pengirim)->first();
        }

        // 2. RATE LIMITING (Batas 3 Detik)
        $lastReq = Carbon::parse($session->last_request_at);
        if ($lastReq->diffInSeconds(now()) < 3) {
            return response("TOO FAST", 200);
        }
        DB::table('wa_sessions')->where('no_hp', $pengirim)->update(['last_request_at' => now()]);

        $state = $session->state ?? 'idle';
        $orderData = json_decode($session->order_data, true) ?? [];

        // 3. PERINTAH GLOBAL (Bisa diakses kapan saja)
        if ($pesan == 'batal') {
            $this->updateSession($pengirim, 'idle', []);
            return $this->sendText($pengirim, "❌ Proses dibatalkan. Ketik *MENU* untuk melihat layanan kami.");
        }

        if (in_array($pesan, ['menu', 'help', 'halo'])) {
            $this->updateSession($pengirim, 'idle', []);
            $msg = "🖨️ *SISTEM INFORMASI PERCETAKAN*\n\n";
            $msg .= "Silakan ketik perintah berikut:\n";
            $msg .= "👉 *ORDER* (Untuk pesan cetakan baru)\n";
            $msg .= "👉 *CEK SPK <nomor>* (Contoh: CEK SPK 001)\n";
            $msg .= "👉 *INFO ANTRIAN* (Cek kepadatan produksi)\n\n";
            $msg .= "_(Ketik *BATAL* kapan saja untuk menghentikan proses pesanan)_";
            return $this->sendText($pengirim, $msg);
        }

        // 4. MENU UTAMA (Hanya jalan jika state = idle)
        if ($state == 'idle') {

            if (preg_match('/cek spk (.+)/i', $pesan, $m)) {
                // Catatan: Karena kita tidak minta cabang di awal lagi, pencarian SPK dilakukan global
                // atau sesuaikan dengan logika cabangmu sebelumnya.
                return $this->replyCekSpk($pengirim, trim($m[1]));
            }

            if ($pesan == 'info antrian') {
                return $this->replyInfoAntrian($pengirim);
            }

            if ($pesan == 'order') {
                $this->updateSession($pengirim, 'tanya_produk');
                return $this->sendText($pengirim, "📝 *FORM ORDER*\n\nMau cetak apa hari ini? (Misal: Spanduk, Stiker, Id Card, dll)");
            }

            // Jika chat tidak dikenali di mode idle
            return response("OK", 200);
        }

        // 5. ALUR PEMESANAN (State Machine)

        if ($state == 'tanya_produk') {
            $orderData['produk'] = $pesan;
            $this->updateSession($pengirim, 'tanya_bahan', $orderData);
            return $this->sendText($pengirim, "Bahan apa yang ingin digunakan? (Misal: Flexi 280, Vinyl, Art Carton. Ketik *BELUM TAHU* jika ragu).");
        }

        if ($state == 'tanya_bahan') {
            $orderData['bahan'] = $pesan;
            $this->updateSession($pengirim, 'tanya_ukuran', $orderData);
            return $this->sendText($pengirim, "Berapa ukuran dan jumlahnya? (Misal: 2x3 meter 1 lembar, atau A3 5 lembar).");
        }

        if ($state == 'tanya_ukuran') {
            $orderData['ukuran_qty'] = $pesan;
            $this->updateSession($pengirim, 'tanya_tipe', $orderData);
            return $this->sendText($pengirim, "Apakah pesanan ini untuk:\n1. Pribadi\n2. Perusahaan / Instansi\n\nBalas dengan angka *1* atau *2*.");
        }

        if ($state == 'tanya_tipe') {
            if ($pesan == '1') {
                $orderData['tipe'] = 'Pribadi';
                $this->updateSession($pengirim, 'pilih_cabang_admin', $orderData);

                $cabangs = MCabang::where('jenis', 'cabang')->get();
                $listCabang = "Silakan pilih cabang terdekat untuk konfirmasi dengan Admin kami:\n";
                foreach ($cabangs as $c) {
                    $listCabang .= "👉 Ketik: *CABANG {$c->id}* ({$c->nama})\n";
                }
                return $this->sendText($pengirim, $listCabang);

            } elseif ($pesan == '2') {
                $orderData['tipe'] = 'Perusahaan';
                $ringkasan = $this->buatRingkasan($orderData);

                // Ganti dengan nomor WA Owner
                $linkOwner = "https://wa.me/6281234567890?text=" . urlencode($ringkasan);

                $this->updateSession($pengirim, 'idle', []);
                return $this->sendText($pengirim, "✅ Spesifikasi instansi tercatat.\n\nSilakan klik link di bawah ini untuk terhubung langsung dengan *Owner/Manajemen* kami:\n\n$linkOwner");
            } else {
                return $this->sendText($pengirim, "Mohon balas dengan angka *1* (Pribadi) atau *2* (Perusahaan).");
            }
        }

        if ($state == 'pilih_cabang_admin') {
            if (preg_match('/cabang (\d+)/i', $pesan, $m)) {
                $cabangId = $m[1];
                $cabang = MCabang::find($cabangId);

                if ($cabang) {
                    $ringkasan = $this->buatRingkasan($orderData);
                    // Asumsi ada kolom no_wa_admin di tabel M_Cabang
                    $linkAdmin = "https://wa.me/{$cabang->no_wa_admin}?text=" . urlencode($ringkasan);

                    $this->updateSession($pengirim, 'idle', []);
                    return $this->sendText($pengirim, "✅ Spesifikasi Anda tercatat.\n\nKlik link berikut untuk lanjut ke *Admin {$cabang->nama}* (kirim desain & pembayaran):\n\n$linkAdmin");
                }
                return $this->sendText($pengirim, "❌ ID Cabang tidak valid. Silakan pilih sesuai daftar.");
            }
        }

        return response("OK", 200);
    }

    // --- HELPER FUNCTIONS ---

    private function updateSession($no_hp, $state, $orderData = null)
    {
        $update = ['state' => $state];
        if ($orderData !== null) {
            $update['order_data'] = json_encode($orderData);
        }
        DB::table('wa_sessions')->where('no_hp', $no_hp)->update($update);
    }

    private function buatRingkasan($data)
    {
        return "Halo, saya mau cetak dengan detail berikut:\n" .
               "- Produk: " . ($data['produk'] ?? '-') . "\n" .
               "- Bahan: " . ($data['bahan'] ?? '-') . "\n" .
               "- Ukuran/Qty: " . ($data['ukuran_qty'] ?? '-') . "\n" .
               "- Tipe: " . ($data['tipe'] ?? '-');
    }

    private function replyCekSpk($target, $keyword)
    {
        // Sesuaikan logika pencarian SPK-mu di sini (Global tanpa filter ID Cabang jika belum login)
        $spk = MSpk::with(['items.bahan', 'items.operator'])
            ->where('no_spk', 'LIKE', "%{$keyword}%")
            ->orWhere('nama_pelanggan', 'LIKE', "%{$keyword}%")
            ->latest()
            ->first();

        if (!$spk) return $this->sendText($target, "❌ *Data SPK tidak ditemukan!*");

        $msg = "📄 *DETAIL SPK: {$spk->no_spk}*\nStatus SPK: *" . strtoupper($spk->status_spk) . "*";
        return $this->sendText($target, $msg);
    }

    private function replyInfoAntrian($target)
    {
        // Sesuaikan query antrian
        return $this->sendText($target, "📊 *KONDISI PRODUKSI*\n\nAntrian saat ini sedang berjalan normal.");
    }

    private function sendText($target, $msg)
    {
        $token = "bnTxfJGZWyYGxSNt1wGL";
        Http::withHeaders(["Authorization" => $token])->post("https://api.fonnte.com/send", [
            "target"  => $target,
            "message" => $msg
        ]);
        return response("OK", 200);
    }
}
