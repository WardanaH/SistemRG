<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotifikasiSpkBaru implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pesan;
    public $no_spk;
    public $tipe;

    // Terima data saat event dipanggil
    public function __construct($no_spk, $tipe = 'Reguler', $designer_nama)
    {
        $this->no_spk = $no_spk;
        $this->tipe = $tipe;
        $this->pesan = "SPK {$tipe} Baru dari {$designer_nama}!";

        Log::info("EVENT CONSTRUCT: Notifikasi dibuat untuk SPK " . $no_spk);
    }

    // Tentukan Channel (Ibarat Frekuensi Radio)
    public function broadcastOn()
    {
        Log::info("EVENT BROADCAST: Mengirim ke channel-admin");
        return new Channel('channel-admin');
    }

    // Nama Event yang akan didengar Javascript
    public function broadcastAs()
    {
        return 'spk-dibuat';
    }
}
