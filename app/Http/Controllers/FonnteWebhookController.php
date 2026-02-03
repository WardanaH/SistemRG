<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\MSpk; // Gunakan Model SPK
use Carbon\Carbon;

class FonnteWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info("WEBHOOK WA", [$request->getContent()]);

        $data = json_decode($request->getContent(), true) ?? [];
        $pengirim = $data['sender'] ?? null;
        $pesan    = trim(strtolower($data['message'] ?? ''));

        if (!$pengirim) return response("NO SENDER", 200);

        /** ======================================================
         * AUTO REPLY UNTUK PENGGUNA BARU
         * ====================================================== */
        // Pastikan Anda membuat tabel/model ChatHistory atau hapus blok ini jika tidak perlu
        // Disini saya contohkan session sederhana atau logic database

        // Cek Pattern Perintah
        if (preg_match('/cek spk (.+)/i', $pesan, $m)) {
            return $this->replyCekSpk($pengirim, trim($m[1]));
        }

        if (preg_match('/cek status (.+)/i', $pesan, $m)) {
            return $this->replyStatusSpk($pengirim, trim($m[1]));
        }

        if (preg_match('/info antrian/i', $pesan)) {
            return $this->replyInfoAntrian($pengirim);
        }

        if ($pesan == 'menu' || $pesan == 'halo' || $pesan == 'help') {
            return $this->sendText(
                $pengirim,
                "*Sistem Informasi SPK Digital Printing* 🖨️\n\n" .
                    "Gunakan perintah berikut:\n" .
                    "👉 *CEK SPK <nomor>*\n" .
                    "   (Melihat detail pesanan)\n" .
                    "👉 *CEK STATUS <nomor>*\n" .
                    "   (Melihat progres produksi)\n" .
                    "👉 *INFO ANTRIAN*\n" .
                    "   (Melihat kepadatan produksi)\n\n" .
                    "Contoh: _CEK SPK 00001_"   
            );
        }

        return response("OK", 200);
    }

    /** ====================================================
     * 1. CEK DETAIL SPK
     * ==================================================== */
    private function replyCekSpk($target, $keyword)
    {
        // Cari SPK berdasarkan No SPK (mirip) atau Nama Pelanggan
        // Menggunakan LIKE %...% agar user cukup ketik angka belakangnya saja
        $spk = MSpk::with(['bahan', 'designer', 'operator'])
            ->where('no_spk', 'LIKE', "%{$keyword}%")
            ->orWhere('nama_pelanggan', 'LIKE', "%{$keyword}%")
            ->latest()
            ->first();

        if (!$spk) {
            return $this->sendText($target, "❌ *Data tidak ditemukan!*\nPastikan Nomor SPK atau Nama Pelanggan benar.");
        }

        $ukuran = "{$spk->ukuran_panjang}x{$spk->ukuran_lebar} cm";
        $tgl = Carbon::parse($spk->tanggal_spk)->format('d M Y');
        $bahan = $spk->bahan->nama_bahan ?? '-';
        $finishing = $spk->finishing ?? '-';

        // Cek apakah ini SPK Bantuan
        $labelBantuan = $spk->is_bantuan ? "*(SPK BANTUAN)*" : "";

        $msg = "📄 *DETAIL SPK* $labelBantuan\n" .
            "--------------------------------\n" .
            "No SPK    : *{$spk->no_spk}*\n" .
            "Tanggal   : $tgl\n" .
            "Pelanggan : {$spk->nama_pelanggan}\n" .
            "File      : {$spk->nama_file}\n" .
            "--------------------------------\n" .
            "Bahan     : $bahan\n" .
            "Ukuran    : $ukuran\n" .
            "Qty       : {$spk->kuantitas}\n" .
            "Finishing : $finishing\n" .
            "--------------------------------\n" .
            "Status Admin : " . strtoupper($spk->status_spk) . "\n" .
            "Produksi     : " . strtoupper($spk->status_produksi);

        return $this->sendText($target, $msg);
    }

    /** ====================================================
     * 2. CEK STATUS PRODUKSI
     * ==================================================== */
    private function replyStatusSpk($target, $keyword)
    {
        $spk = MSpk::where('no_spk', 'LIKE', "%{$keyword}%")->first();

        if (!$spk) {
            return $this->sendText($target, "❌ *SPK tidak ditemukan!*");
        }

        // Mapping Emoticon Status
        $icon = match ($spk->status_produksi) {
            'pending' => '⏳',
            'ripping' => '🖥️',
            'ongoing' => '🖨️',
            'finishing' => '✂️',
            'done' => '✅',
            default => '❓'
        };

        $operator = $spk->operator->nama ?? 'Belum ada';

        $msg = "🔍 *STATUS PRODUKSI*\n\n" .
            "No SPK: *{$spk->no_spk}*\n" .
            "File: {$spk->nama_file}\n\n" .
            "Status Saat Ini:\n" .
            "$icon *" . strtoupper($spk->status_produksi) . "*\n\n" .
            "Operator: $operator\n" .
            "Catatan: {$spk->keterangan}";

        if ($spk->status_produksi == 'done') {
            $msg .= "\n\n_Barang sudah selesai dan siap diambil/dikirim._";
        }

        return $this->sendText($target, $msg);
    }

    /** ====================================================
     * 3. INFO ANTRIAN (Fitur Tambahan)
     * ==================================================== */
    private function replyInfoAntrian($target)
    {
        // Hitung jumlah antrian ongoing dan pending
        $ongoing = MSpk::where('status_produksi', 'ongoing')->count();
        $pending = MSpk::where('status_produksi', 'pending')->count();
        $finishing = MSpk::where('status_produksi', 'finishing')->count();

        $msg = "📊 *INFO ANTRIAN SAAT INI*\n\n" .
            "🖨️ Sedang Cetak: *$ongoing* file\n" .
            "✂️ Finishing: *$finishing* file\n" .
            "⏳ Menunggu: *$pending* file\n\n" .
            "_Estimasi waktu hubungi Admin._";

        return $this->sendText($target, $msg);
    }

    /** ====================================================
     * KIRIM KE API FONNTE
     * ==================================================== */
    private function sendText($target, $msg)
    {
        $token = "bnTxfJGZWyYGxSNt1wGL"; // Ganti dengan Token Fonnte Anda

        try {
            Http::withHeaders([
                "Authorization" => $token
            ])->post("https://api.fonnte.com/send", [
                "target"  => $target,
                "message" => $msg
            ]);
        } catch (\Exception $e) {
            Log::error("Fonnte Error: " . $e->getMessage());
        }

        return "OK";
    }
}
