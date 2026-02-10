<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MAmbilAntar extends Model
{
    protected $table = 'ambil_antars';

    protected $casts = [
        'keterangan' => 'array',
        'keterangan_diterima' => 'array'
    ];

    protected $fillable = [
        'kode',
        'cabang_pengirim_id',
        'cabang_tujuan_id',
        'jenis',
        'tanggal',
        'atas_nama',
        'keterangan',
        'keterangan_diterima',
        'bukti_foto',
        'status'
    ];

    public function cabangPengirim()
    {
        return $this->belongsTo(MCabang::class, 'cabang_pengirim_id');
    }

    public function cabangTujuan()
    {
        return $this->belongsTo(MCabang::class, 'cabang_tujuan_id');
    }
}

