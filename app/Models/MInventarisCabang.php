<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MInventarisCabang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventaris_cabangs';

    protected $fillable = [
        'cabang_id',
        'kode_barang',
        'nama_barang',
        'jumlah',
        'kondisi',
        'lokasi',
        'tanggal_input',
        'qr_code',
    ];

    public function cabang()
    {
        return $this->belongsTo(MCabang::class);
    }
}

