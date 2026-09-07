<?php

namespace App\Events;

use App\Models\PermintaanBarang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifikasiPusatV2 implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $pesan;
    public $role;
    public $cabang;

    public function __construct($id, $pesan)
    {
        $this->id = $id;
        $this->pesan = $pesan;
        $this->role = 'inventory utama'; // Target role

        $permintaan = PermintaanBarang::with('gudang')->find($id);

        $this->cabang = $permintaan && $permintaan->gudang
            ? $permintaan->gudang->nama
            : '-';

        Log::info('EVENT PUSAT V2', [
            'id' => $id,
            'cabang' => $this->cabang
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('distribusi-v2-channel');
    }

    public function broadcastAs()
    {
        return 'notif-pusat';
    }
}
