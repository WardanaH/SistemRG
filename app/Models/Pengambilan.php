<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengambilan extends Model
{
    use HasFactory;

    // Menyesuaikan ngaran tabel di database
    protected $table = 'pengambilan';

    // Kolom nang bulih diisi
    protected $fillable = [
        'cabang_id',
        'nomor_pengambilan',
        'foto_pengambilan'
    ];

    // Relasi ke tabel m_cabangs (Sesuaikan ngaran model cabang ikam)
    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }

    // Relasi ke tabel pengambilan_detail
    public function detail()
    {
        return $this->hasMany(PengambilanDetail::class, 'id_pengambilan');
    }
}
