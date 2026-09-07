<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    use HasFactory;

    protected $table = 'detail_permintaan';

    protected $fillable = [
        'permintaan_id',
        'barang_id', // Ini sekarang me-reference ka id di tabel gudang_barangs
        'sisa_stok_cabang',
        'jumlah_diminta',
        'jumlah_disetujui',
        'jumlah_diterima',
        'keterangan_barang',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id', 'id');
    }

    // Relasi ka master barang pusat (MGudangBarang)
    public function barangPusat()
    {
        return $this->belongsTo(MGudangBarang::class, 'barang_id', 'id');
    }
}
