<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPengiriman extends Model
{
    protected $table = 'pengirimans';

    protected $fillable = [
        'kode_pengiriman',
        'permintaan_id',
        'cabang_tujuan_id',
        'tanggal_pengiriman',
        'status_pengiriman',
        'status_kelengkapan',
        'tanggal_diterima',
        'keterangan',
        'catatan_gudang',
        'read_at'
    ];

    protected $casts = [
        'keterangan' => 'array',
        'tanggal_pengiriman' => 'date',
        'tanggal_diterima' => 'date',
        'read_at' => 'datetime'
    ];

    // =====================
    // RELASI
    // =====================

    public function permintaan()
    {
        return $this->belongsTo(MPermintaanPengiriman::class, 'permintaan_id');
    }

    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_tujuan_id');
    }

    public function cabangTujuan()
    {
        return $this->belongsTo(MCabang::class, 'cabang_tujuan_id');
    }

}
