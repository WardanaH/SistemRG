<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MGudangBarang extends Model
{
    protected $table = 'gudang_barangs';

    protected $fillable = [
        'kategori_id',
        'nama_bahan',
        'satuan',
        'stok',
        'batas_stok',
        'keterangan',
    ];

        protected $casts = [
        'stok' => 'float',
        'batas_stok' => 'float',
    ];

    public function getStokFormattedAttribute()
    {
        // kalau bulat → tampil tanpa koma
        if (floor($this->stok) == $this->stok) {
            return number_format($this->stok, 0, ',', '.');
        }

        // kalau ada desimal → tampilkan apa adanya (tanpa buang nol penting)
        return rtrim(rtrim(number_format($this->stok, 2, ',', '.'), '0'), ',');
    }

}

