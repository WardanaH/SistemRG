<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCabangBarang extends Model
{
    use HasFactory;

    protected $table = 'cabang_barangs';

    protected $fillable = [
        'cabang_id',
        'gudang_barang_id',
        'stok',
    ];

    protected $casts = [
        'stok' => 'float',
    ];

    /**
     * Relasi ke Cabang
     */
    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }

    /**
     * Relasi ke Gudang Barang (master barang)
     */
    public function gudangBarang()
    {
        return $this->belongsTo(MGudangBarang::class, 'gudang_barang_id');
    }
}
