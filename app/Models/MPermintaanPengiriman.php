<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPermintaanPengiriman extends Model
{
    protected $table = 'permintaan_pengirimans';

    protected $fillable = [
        'kode_permintaan',
        'cabang_id',
        'tanggal_permintaan',
        'status',
        'detail_barang',
        'catatan',
        'read_at'
    ];

    protected $casts = [
        'detail_barang' => 'array',
        'read_at' => 'datetime'
    ];

    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }

    public function pengirimans()
    {
        return $this->hasMany(MPengiriman::class, 'permintaan_id');
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }

}
