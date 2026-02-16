<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifikasiOperator implements ShouldBroadcastNow
{
    public $no_spk;
    public $nama_file;
    public $operator_id;
    public $pesan;

    /**
     * Terima data detail SPK saat event dipanggil
     */
    public function __construct($no_spk, $nama_file, $operator_id)
    {
        $this->no_spk = $no_spk;
        $this->nama_file = $nama_file;
        $this->operator_id = $operator_id;
        $this->pesan = "Ada tugas baru: $nama_file ($no_spk)";

        Log::info("EVENT CONSTRUCT: Notifikasi tugas baru untuk Operator ID: $operator_id");
    }

    /**
     * Tentukan Channel
     * Kamu bisa pakai 'channel-operator' (global)
     * atau 'operator.'.$this->operator_id (spesifik per orang)
     */
    public function broadcastOn()
    {
        return new Channel('operator.'.$this->operator_id);
    }

    /**
     * Nama Event yang didengar Javascript (Pusher/Reverb)
     */
    public function broadcastAs()
    {
        return 'kerjaan-baru';
    }

    /**
     * Data tambahan yang dikirim ke frontend
     */
    public function broadcastWith()
    {
        return [
            'no_spk' => $this->no_spk,
            'nama_file' => $this->nama_file,
            'operator_id' => $this->operator_id,
            'pesan' => $this->pesan,
            'waktu' => now()->format('H:i:s')
        ];
    }
}
