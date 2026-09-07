<?php

namespace App\Events;

use App\Models\PermintaanBarang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifikasiCabangV2 implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $pesan;
    public $role;
    public $cabang;
    public $gudang_cabang_id; // Penting gasan filter di frontend supaya notif kada masuk ke cabang urang

    public function __construct($id, $pesan)
    {
        $permintaan = PermintaanBarang::with('gudang')->findOrFail($id);

        $this->id = $permintaan->id;
        $this->pesan = $pesan;
        $this->role = 'inventory cabang'; // Target role

        $this->cabang = $permintaan->gudang->nama ?? '-';
        $this->gudang_cabang_id = $permintaan->gudang_cabang_id;
    }

    public function broadcastOn()
    {
        return new Channel('distribusi-v2-channel');
    }

    public function broadcastAs()
    {
        return 'notif-cabang';
    }
}
