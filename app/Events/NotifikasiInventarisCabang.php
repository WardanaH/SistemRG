<?php

namespace App\Events;

use App\Models\MPengiriman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NotifikasiInventarisCabang implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $id;
    public $pesan;
    public $role;
    public $cabang;

    public function __construct($pengirimanId)
    {
        $pengiriman = MPengiriman::with('cabangTujuan')->findOrFail($pengirimanId);

        $this->id     = $pengiriman->id;
        $this->role   = 'inventory cabang';
        $this->cabang = $pengiriman->cabangTujuan->nama;
        $this->pesan  = 'Barang telah dikirim ke ' . $this->cabang;
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
