<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MPengambilan extends Model
{
    use HasFactory;

    protected $table = 'pengambilans';

    protected $fillable = [
        'cabang_id',
        'ambil_ke',
        'tanggal',
        'atas_nama',
        'list_barang',
        'foto',
    ];

    protected $casts = [
        'list_barang' => 'array',
        'tanggal' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(MCabang::class, 'cabang_id');
    }
}
