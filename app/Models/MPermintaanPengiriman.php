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
        'catatan'
    ];

    protected $casts = [
        'detail_barang' => 'array'
    ];

    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }
}
