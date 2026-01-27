<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MGudangBarang extends Model
{
    protected $table = 'gudang_barangs';

    protected $fillable = [
        'kategori_id',
        'nama_bahan',
        'harga',
        'satuan',
        'stok',
        'batas_stok',
        'keterangan',
    ];
}

