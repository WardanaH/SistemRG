<?php

namespace App\Events;

use App\Models\MPermintaanPengiriman;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotifikasiInventaris implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $pesan;
    public $role;
    public $jenis;
    public $cabang;

    public function __construct($id, $pesan, $role, $jenis)
    {
        $this->id    = $id;
        $this->pesan = $pesan;
        $this->role  = $role;
        $this->jenis = $jenis;

        $permintaan = MPermintaanPengiriman::with('cabang')->find($id);

        $this->cabang = $permintaan && $permintaan->cabang
            ? $permintaan->cabang->nama
            : '-';

        Log::info('EVENT INVENTARIS', [
            'id' => $id,
            'cabang' => $this->cabang
        ]);
    }

    public function broadcastOn()
    {
        return new Channel('inventaris-channel');
    }

    public function broadcastAs()
    {
        return 'inventaris-notif';
    }
}
