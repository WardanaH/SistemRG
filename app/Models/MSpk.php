<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MSpk extends Model
{
    protected $table = 'm_spks'; // Sesuaikan nama tabel
    protected $guarded = ['id'];

    // Relasi ke Bahan Baku (Setiap SPK memiliki 1 jenis bahan utama di baris ini)
    public function bahan()
    {
        return $this->belongsTo(MBahanBaku::class, 'bahan_id', 'id');
    }

    // Relasi ke User sebagai Designer
    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id', 'id');
    }

    // Relasi ke User sebagai Operator
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }

    // Relasi ke Cabang
    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id', 'id');
    }
}
