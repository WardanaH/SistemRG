<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MBahanBaku extends Model
{
    protected $table = 'm_bahan_bakus';

    protected $fillable = [
        'nama_bahan',
        'kode_bahan',
    ];

    public function spks()
    {
        return $this->hasMany(MSpk::class, 'bahan_id', 'id');
    }
}
