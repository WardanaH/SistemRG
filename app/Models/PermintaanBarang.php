<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode_permintaan',
        'gudang_cabang_id',
        'tanggal_request',
        'status_permintaan',
        'keterangan',
    ];

    // Relasi One-to-Many ke tabel detail
    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaan::class, 'permintaan_id', 'id');
    }

    // Relasi ke tabel gudang cabang (Sesuaikan ngaran model Gudang-nya amun beda)
    public function gudang()
    {
        return $this->belongsTo(MCabang::class, 'gudang_cabang_id', 'id');
    }
}