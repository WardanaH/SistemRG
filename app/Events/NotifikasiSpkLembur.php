<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotifikasiSpkLembur implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pesan;
    public $tipe_spk;
    public $no_spk;


    public function __construct($no_spk, $tipe_spk, $designer_nama)
    {
        $this->no_spk = $no_spk;
        $this->tipe_spk = $tipe_spk;
        $this->pesan = "SPK {$tipe_spk} Baru oleh {$designer_nama}!";

        Log::info("EVENT CONSTRUCT: Notifikasi dibuat untuk SPK Lembur no - " . $no_spk);
    }
    public function broadcastOn()
    {
        Log::info("EVENT BROADCAST: Mengirim ke channel-admin");
        return new Channel('channel-lembur');
    }

    public function broadcastAs()
    {
        return 'spk-lembur-dibuat';
    }
}
