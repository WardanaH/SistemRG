<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanDetail extends Model
{
    use HasFactory;

    // Menyesuaikan ngaran tabel di database
    protected $table = 'pengambilan_detail';

    // Kolom nang bulih diisi
    protected $fillable = [
        'id_pengambilan',
        'nama_barang',
        'jumlah_barang',
        'ukuran_barang',
        'atas_nama',
        'ambil_ke'
    ];

    // Relasi balik ke tabel pengambilan
    public function pengambilan()
    {
        return $this->belongsTo(Pengambilan::class, 'id_pengambilan');
    }
}
