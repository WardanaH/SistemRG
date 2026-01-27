<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MPengiriman extends Model
{
    protected $table = 'pengirimans';

    protected $fillable = [
        'kode_pengiriman',
        'gudang_barang_id',
        'cabang_tujuan_id',
        'jumlah',
        'tanggal_pengiriman',
        'status_pengiriman',
        'tanggal_diterima',
        'keterangan'
    ];

    protected $casts = [
        'keterangan' => 'array',
    ];

    public function barang()
    {
        return $this->belongsTo(MGudangBarang::class, 'gudang_barang_id');
    }

    public function cabangTujuan()
    {
        return $this->belongsTo(MCabang::class, 'cabang_tujuan_id');
    }

}
