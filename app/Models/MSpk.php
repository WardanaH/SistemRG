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
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
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


    // Relasi ke Cabang asal (Setiap SPK memiliki 1 Cabang asal di baris ini)
    public function cabangAsal()
    {
        return $this->belongsTo(MCabang::class, 'asal_cabang_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(MSubSpk::class, 'spk_id');
    }
}
