<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\MSpk;
use App\Models\MSubSpk;
use App\Models\MCabang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FonnteWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $pengirim = $data['sender'] ?? null;
        $pesan    = trim(strtolower($data['message'] ?? ''));

        if (!$pengirim) return response("NO SENDER", 200);

        // 1. RATE LIMITING SEDERHANA (Anti DDOS)
        $session = DB::table('wa_sessions')->where('no_hp', $pengirim)->first();
        if ($session && $session->last_request_at) {
            $lastReq = Carbon::parse($session->last_request_at);
            if ($lastReq->diffInSeconds(now()) < 3) { // Batas 3 detik per pesan
                return response("TOO FAST", 200);
            }
        }

        // Update waktu request terakhir
        DB::table('wa_sessions')->updateOrInsert(
            ['no_hp' => $pengirim],
            ['last_request_at' => now()]
        );

        // 2. LOGIKA PILIH CABANG
        if (preg_match('/pilih cabang (\d+)/i', $pesan, $m)) {
            $cabangId = $m[1];
            $cabang = MCabang::find($cabangId);
            if ($cabang) {
                DB::table('wa_sessions')->where('no_hp', $pengirim)->update(['cabang_id' => $cabangId]);
                return $this->sendText($pengirim, "✅ Berhasil memilih cabang: *{$cabang->nama}*\n\nSekarang Anda dapat menggunakan fitur cek status.");
            }
            return $this->sendText($pengirim, "❌ ID Cabang tidak valid.");
        }

        // 3. CEK APAKAH SUDAH PILIH CABANG
        if (!$session || !$session->cabang_id) {
            $cabangs = MCabang::where('jenis', 'cabang')->get();
            $listCabang = "👋 Halo! Sebelum memulai, silakan pilih cabang tempat Anda melakukan order:\n\n";
            foreach ($cabangs as $c) {
                $listCabang .= "👉 Ketik: *PILIH CABANG {$c->id}* untuk {$c->nama}\n";
            }
            return $this->sendText($pengirim, $listCabang);
        }

        // 4. ROUTING PERINTAH (Hanya jalan jika sudah pilih cabang)
        if (preg_match('/cek spk (.+)/i', $pesan, $m)) {
            return $this->replyCekSpk($pengirim, trim($m[1]), $session->cabang_id);
        }

        if (preg_match('/info antrian/i', $pesan)) {
            return $this->replyInfoAntrian($pengirim, $session->cabang_id);
        }

        if ($pesan == 'menu' || $pesan == 'help' || $pesan == 'halo') {
            $cabangName = MCabang::find($session->cabang_id)->nama;
            return $this->sendText(
                $pengirim,
                "*Sistem Informasi SPK ($cabangName)* 🖨️\n\n" .
                    "Gunakan perintah berikut:\n" .
                    "👉 *CEK SPK <nomor>*\n" .
                    "👉 *INFO ANTRIAN*\n" .
                    "👉 *GANTI CABANG* (Untuk pindah cabang)\n\n" .
                    "Contoh: _CEK SPK 00001_"
            );
        }

        if ($pesan == 'ganti cabang') {
            DB::table('wa_sessions')->where('no_hp', $pengirim)->update(['cabang_id' => null]);
            return $this->sendText($pengirim, "🔄 Silakan pilih cabang kembali.");
        }

        return response("OK", 200);
    }

    /**
     * 1. REPLY CEK SPK (Multi Item Support)
     */
    private function replyCekSpk($target, $keyword, $cabangId)
    {
        // Cari SPK berdasarkan cabang yang dipilih
        $spk = MSpk::with(['items.bahan', 'items.operator'])
            ->where('cabang_id', $cabangId)
            ->where(function ($q) use ($keyword) {
                $q->where('no_spk', 'LIKE', "%{$keyword}%")
                    ->orWhere('nama_pelanggan', 'LIKE', "%{$keyword}%");
            })
            ->latest()
            ->first();

        if (!$spk) {
            return $this->sendText($target, "❌ *Data tidak ditemukan di cabang ini!*");
        }

        $msg = "📄 *DETAIL SPK: {$spk->no_spk}*\n";
        $msg .= "👤 Pelanggan: {$spk->nama_pelanggan}\n";
        $msg .= "📅 Tgl: " . Carbon::parse($spk->tanggal_spk)->format('d/m/Y') . "\n";
        $msg .= "--------------------------------\n";

        // Loop Detail Items
        foreach ($spk->items as $index => $item) {
            $n = $index + 1;
            $icon = $item->status_produksi == 'done' ? '✅' : '⏳';
            $msg .= "$n. *{$item->nama_file}*\n";
            $msg .= "   Mat: {$item->bahan->nama_bahan} ({$item->p}x{$item->l}cm)\n";
            $msg .= "   Qty: {$item->qty} | Status: $icon " . strtoupper($item->status_produksi) . "\n\n";
        }

        $msg .= "--------------------------------\n";
        $msg .= "Status SPK: *" . strtoupper($spk->status_spk) . "*";

        return $this->sendText($target, $msg);
    }

    /**
     * 2. INFO ANTRIAN (Per Cabang)
     */
    private function replyInfoAntrian($target, $cabangId)
    {
        // Hitung antrian berdasarkan item di cabang tersebut
        $antrian = MSubSpk::whereHas('spk', function ($q) use ($cabangId) {
            $q->where('cabang_id', $cabangId)->where('status_spk', 'acc');
        })
            ->whereIn('status_produksi', ['pending', 'ripping', 'ongoing'])
            ->count();

        $doneToday = MSubSpk::whereHas('spk', function ($q) use ($cabangId) {
            $q->where('cabang_id', $cabangId);
        })
            ->where('status_produksi', 'done')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        $msg = "📊 *KONDISI PRODUKSI CABANG*\n\n";
        $msg .= "⏳ Antrian Aktif: *{$antrian}* file\n";
        $msg .= "✅ Selesai Hari Ini: *{$doneToday}* file\n\n";
        $msg .= "Status kepadatan: " . ($antrian > 10 ? "🔴 *PADAT*" : "🟢 *NORMAL*");

        return $this->sendText($target, $msg);
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
